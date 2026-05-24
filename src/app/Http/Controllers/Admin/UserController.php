<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\SubscriptionService;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\ValueObjects\Money;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kyc')) {
            $query->where('kyc_status', $request->kyc);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(25)->withQueryString();

        $counts = User::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        $statusPills = [
            ['value' => '',        'label' => 'All',      'count' => $counts->sum()],
            ['value' => 'active',  'label' => 'Active',   'count' => $counts['active'] ?? 0],
            ['value' => 'inactive', 'label' => 'Inactive', 'count' => $counts['inactive'] ?? 0],
            ['value' => 'banned',  'label' => 'Banned',   'count' => $counts['banned'] ?? 0],
        ];

        return view('admin.users.index', compact('users', 'statusPills'));
    }

    public function show(User $user)
    {
        $logins = $user->logins()->orderByDesc('logged_in_at')->limit(10)->get();
        $subscription = $user->subscription()->with('plan')->first();
        $plans = Plan::active()->orderBy('sort')->get();

        return view('admin.users.show', compact('user', 'logins', 'subscription', 'plans'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'status' => ['required', 'in:active,inactive,banned'],
            'kyc_status' => ['required', 'in:none,pending,approved,rejected'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->status = $data['status'];
        $user->kyc_status = $data['kyc_status'];
        $user->save();

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,inactive,banned'],
        ]);

        $user->status = $data['status'];
        $user->save();

        return back()->with('success', 'Status updated to '.ucfirst($data['status']).'.');
    }

    public function updateKyc(Request $request, User $user)
    {
        $data = $request->validate([
            'kyc_status' => ['required', 'in:none,pending,approved,rejected'],
        ]);

        $user->kyc_status = $data['kyc_status'];
        $user->save();

        return back()->with('success', 'KYC status updated to '.ucfirst($data['kyc_status']).'.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($data['password']);
        $user->setRememberToken(\Illuminate\Support\Str::random(60));
        $user->save();

        return back()->with('success', 'Password updated. User will be required to log in again.');
    }

    public function adjustBalance(Request $request, User $user, WalletService $wallet)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'not_in:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (float) $data['amount'];
        $money = Money::of($amount, 'USD');
        $note = $data['note'] ?: ($amount > 0 ? 'Manual credit by admin' : 'Manual deduction by admin');
        $admin = auth('admin')->user();

        try {
            $tx = $wallet->adjust($user, $money, $note, $admin);
        } catch (\DomainException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        $direction = $amount > 0 ? 'credited' : 'deducted';

        return back()->with('success', 'Balance '.$direction.'. New balance: '.$tx->formattedBalance());
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => ['required', 'in:ban,unban,delete'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $users = User::whereIn('id', $data['user_ids']);

        match ($data['action']) {
            'ban' => $users->update(['status' => 'banned']),
            'unban' => $users->update(['status' => 'active']),
            'delete' => $users->delete(),
        };

        return back()->with('success', 'Bulk action applied.');
    }

    public function verifyEmail(User $user)
    {
        $user->email_verified_at = now();
        $user->save();

        return back()->with('success', 'Email marked as verified.');
    }

    public function unverifyEmail(User $user)
    {
        $user->email_verified_at = null;
        $user->save();

        return back()->with('success', 'Email verification removed.');
    }

    public function assignPlan(Request $request, User $user, SubscriptionService $subscriptionService)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $subscriptionService->subscribe($user, $plan);

        return back()->with('success', 'Plan assigned to user.');
    }

    public function cancelSubscription(User $user, SubscriptionService $subscriptionService)
    {
        $sub = $user->subscription()->active()->first();
        if (! $sub) {
            return back()->withErrors(['error' => 'No active subscription.']);
        }
        $subscriptionService->cancel($sub);

        return back()->with('success', 'Subscription cancelled.');
    }

    public function impersonate(Request $request, User $user)
    {
        $request->session()->put('admin_impersonating_user_id', auth('admin')->id());
        auth('user')->login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }
}
