<?php

namespace App\Http\Controllers\User;

use App\Domain\Payment\Models\Payout;
use App\Domain\Payment\Models\PayoutMethod;
use App\Domain\Wallet\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Support\AdminNotify;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        $user = auth('user')->user();
        $payouts = Payout::where('user_id', $user->id)
            ->with('method')
            ->latest()
            ->paginate(15);

        $balance = app(WalletService::class)->getBalance($user);

        return view('user.payout.index', compact('payouts', 'balance'));
    }

    public function create()
    {
        $user = auth('user')->user();
        $methods = PayoutMethod::enabled()->with('form.fields')->get();
        $balance = app(WalletService::class)->getBalance($user);

        return view('user.payout.create', compact('methods', 'balance'));
    }

    public function store(Request $request)
    {
        $user = auth('user')->user();

        $data = $request->validate([
            'method_id' => 'required|exists:payout_methods,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $method = PayoutMethod::findOrFail($data['method_id']);
        $amount = (float) $data['amount'];

        if ($amount < $method->min_amount) {
            return back()->withErrors(['amount' => 'Amount is below the minimum of $'.number_format($method->min_amount, 2)]);
        }
        if ($amount > $method->max_amount) {
            return back()->withErrors(['amount' => 'Amount exceeds the maximum of $'.number_format($method->max_amount, 2)]);
        }

        $fee = $method->calculateFee($amount);
        $totalDebit = $amount; // we debit gross from wallet; fee is informational

        $balance = app(WalletService::class)->getBalance($user);
        if ($totalDebit > $balance->amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        // Collect payout detail fields from the method's form
        $payoutDetails = [];
        if ($method->form) {
            foreach ($method->form->fields as $field) {
                $payoutDetails[$field->name] = $request->input($field->name);
            }
        }

        $payout = new Payout;
        $payout->user_id = $user->id;
        $payout->payout_method_id = $method->id;
        $payout->amount = $amount;
        $payout->currency = 'USD';
        $payout->fee = $fee;
        $payout->status = Payout::STATUS_PENDING;
        $payout->payout_details = $payoutDetails;
        $payout->save();

        AdminNotify::all(
            'New payout request',
            $user->name.' requested $'.number_format($amount, 2).' via '.$method->name,
            route('admin.payouts.index')
        );

        return redirect()->route('user.payout.pending', $payout);
    }

    public function pending(Payout $payout)
    {
        $this->authorizePayoutRequest($payout);

        return view('user.payout.pending', compact('payout'));
    }

    public function cancel(Payout $payout)
    {
        $this->authorizePayoutRequest($payout);

        if (! $payout->isPending()) {
            return back()->with('error', 'Payout cannot be cancelled.');
        }

        $payout->status = Payout::STATUS_CANCELLED;
        $payout->save();

        return redirect()->route('user.payout.index')
            ->with('success', 'Payout cancelled.');
    }

    private function authorizePayoutRequest(Payout $payout): void
    {
        if ($payout->user_id !== auth('user')->id()) {
            abort(403);
        }
    }
}
