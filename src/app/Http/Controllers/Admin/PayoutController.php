<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Models\Payout;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\ValueObjects\Money;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    public function __construct(private WalletService $wallet) {}

    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $payouts = Payout::with(['user', 'method'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = Payout::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        $statusPills = [
            ['value' => 'all',       'label' => 'All',        'count' => $counts->sum()],
            ['value' => 'pending',   'label' => 'Pending',    'count' => $counts['pending'] ?? 0],
            ['value' => 'approved',  'label' => 'Approved',   'count' => $counts['approved'] ?? 0],
            ['value' => 'rejected',  'label' => 'Rejected',   'count' => $counts['rejected'] ?? 0],
            ['value' => 'cancelled', 'label' => 'Cancelled',  'count' => $counts['cancelled'] ?? 0],
        ];

        return view('admin.payouts.index', compact('payouts', 'statusPills', 'status'));
    }

    public function show(Payout $payout)
    {
        $payout->load(['user', 'method.form.fields', 'reviewer', 'transaction']);

        return view('admin.payouts.show', compact('payout'));
    }

    public function approve(Payout $payout)
    {
        if (! $payout->isPending()) {
            return back()->with('error', 'Payout is not pending.');
        }

        DB::transaction(function () use ($payout) {
            $admin = auth('admin')->user();
            $amount = Money::of($payout->amount, $payout->currency);

            $tx = $this->wallet->debit(
                user: $payout->user,
                amount: $amount,
                description: 'Payout via '.$payout->method->getLocaleName(),
                meta: [
                    'payout_id' => $payout->id,
                    'admin' => ['id' => $admin->id, 'name' => $admin->name],
                    'payout' => $payout->payout_details,
                ],
                adminId: $admin->id,
                subjectType: Payout::class,
                subjectId: $payout->id,
            );

            $payout->status = Payout::STATUS_APPROVED;
            $payout->transaction_id = $tx->id;
            $payout->reviewed_by = $admin->id;
            $payout->reviewed_at = now();
            $payout->save();
        });

        return back()->with('success', 'Payout approved and wallet debited.');
    }

    public function reject(Request $request, Payout $payout)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        if (! $payout->isPending()) {
            return back()->with('error', 'Payout is not pending.');
        }

        $admin = auth('admin')->user();

        $payout->status = Payout::STATUS_REJECTED;
        $payout->rejection_reason = $request->input('reason');
        $payout->reviewed_by = $admin->id;
        $payout->reviewed_at = now();
        $payout->save();

        return back()->with('success', 'Payout rejected.');
    }
}
