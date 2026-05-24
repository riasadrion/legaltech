<?php

namespace App\Http\Controllers\User;

use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\PaymentGatewayManager;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\SubscriptionService;
use App\Domain\Upload\FileUploader;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\ValueObjects\Money;
use App\Http\Controllers\Controller;
use App\Support\AdminNotify;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(WalletService $wallet)
    {
        $user = auth('user')->user();
        $gateways = Gateway::enabled()->orderBy('sort')->get();
        $payments = Payment::where('user_id', $user->id)->with('gateway')->latest()->paginate(10);
        $balance  = $wallet->getBalance($user);

        // Total spent per gateway (completed only, all pages)
        $gatewayTotals = Payment::where('user_id', $user->id)
            ->where('status', Payment::STATUS_COMPLETED)
            ->selectRaw('gateway_id, SUM(amount) as total')
            ->groupBy('gateway_id')
            ->pluck('total', 'gateway_id');

        return view('user.payment.index', compact('gateways', 'payments', 'gatewayTotals', 'balance'));
    }

    public function create(Request $request)
    {
        if (!$request->gateway) {
            $gateways = Gateway::enabled()->orderBy('sort')->get();
            return view('user.payment.select', compact('gateways'));
        }

        $gateway = Gateway::where('driver', $request->gateway)->enabled()->firstOrFail();

        return view('user.payment.create', compact('gateway'));
    }

    public function store(Request $request, PaymentGatewayManager $manager, FileUploader $uploader)
    {
        $data = $request->validate([
            'gateway_id' => ['required', 'exists:gateways,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'receipt' => ['nullable', 'file'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
        ]);

        $gateway = Gateway::enabled()->findOrFail($data['gateway_id']);
        $driver = $manager->forGateway($gateway);
        $amount = (float) $data['amount'];
        $fee = $gateway->calculateFee($amount);
        $user = auth('user')->user();

        // Validate amount bounds
        if ($amount < $gateway->min_amount) {
            return back()->withErrors(['amount' => 'Amount is below the minimum for this gateway.']);
        }
        if ($amount > $gateway->max_amount) {
            return back()->withErrors(['amount' => 'Amount exceeds the maximum for this gateway.']);
        }

        $payment = new Payment;
        $payment->user_id = $user->id;
        $payment->gateway_id = $gateway->id;
        $payment->amount = $amount;
        $payment->currency = strtoupper($data['currency']);
        $payment->fee = $fee;
        $payment->status = Payment::STATUS_PENDING;
        $payment->save();

        // Attach plan if provided
        if ($data['plan_id'] ?? null) {
            $payment->meta = ['plan_id' => (int) $data['plan_id']];
            $payment->save();
        }

        // Handle manual receipt upload
        if ($gateway->driver === 'manual' && $request->hasFile('receipt')) {
            try {
                $meta = $uploader->store(
                    $request->file('receipt'),
                    'payments',
                );

                $payment->receipt_path = $meta->path;
                $payment->save();
            } catch (\InvalidArgumentException $e) {
                $payment->delete();

                return back()->withErrors(['receipt' => $e->getMessage()]);
            }
        }

        // Initiate with driver — returns redirect URL or null
        try {
            $redirectUrl = $driver->initiate($payment);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Gateway error: '.$e->getMessage()]);
        }

        if ($redirectUrl) {
            AdminNotify::all(
                'New payment initiated',
                $user->name.' initiated $'.number_format($payment->amount, 2).' via '.$gateway->name,
                route('admin.payments.index')
            );
            return redirect()->away($redirectUrl);
        }

        AdminNotify::all(
            'New payment request',
            $user->name.' submitted $'.number_format($payment->amount, 2).' via '.$gateway->name,
            route('admin.payments.index')
        );

        // No redirect (manual) — go to pending page
        return redirect()->route('user.payment.pending', $payment)
            ->with('success', 'Payment submitted. Admin will review your receipt shortly.');
    }

    public function handleReturn(Request $request, Payment $payment, PaymentGatewayManager $manager, WalletService $wallet)
    {
        $this->authorizePayment($payment);

        $driver = $manager->driver($request->gateway);
        $payment = $driver->handleReturn($payment, $request->all());

        if ($payment->isCompleted()) {
            $this->creditWallet($payment, $wallet);

            return redirect()->route('user.payment.success', $payment);
        }

        return redirect()->route('user.payment.pending', $payment);
    }

    public function cancel(Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->isPending()) {
            $payment->status = Payment::STATUS_CANCELLED;
            $payment->save();
        }

        return redirect()->route('user.payment.index')->with('info', 'Payment cancelled.');
    }

    public function pending(Payment $payment)
    {
        $this->authorizePayment($payment);

        return view('user.payment.pending', compact('payment'));
    }

    public function success(Payment $payment)
    {
        $this->authorizePayment($payment);

        return view('user.payment.success', compact('payment'));
    }

    /**
     * Called after a webhook or return marks a payment as completed — credit the wallet if not yet done.
     */
    public function creditWallet(Payment $payment, WalletService $wallet): void
    {
        if ($payment->transaction_id) {
            return; // already credited
        }

        $net = Money::of($payment->netAmount(), $payment->currency);
        $tx = $wallet->credit(
            $payment->user,
            $net,
            ucfirst($payment->gateway->driver)." payment (#{$payment->id})",
            ['payment_id' => $payment->id],
            null,
            Payment::class,
            $payment->id,
        );

        $payment->transaction_id = $tx->id;
        $payment->save();

        // Activate subscription if this payment was for a plan
        $planId = $payment->meta['plan_id'] ?? null;
        if ($planId) {
            $plan = Plan::find($planId);
            if ($plan) {
                app(SubscriptionService::class)->subscribe($payment->user, $plan, $payment);
            }
        }
    }

    private function authorizePayment(Payment $payment): void
    {
        if ($payment->user_id !== auth('user')->id()) {
            abort(403);
        }
    }
}
