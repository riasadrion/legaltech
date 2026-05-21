<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Models\Payment;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\SubscriptionService;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\ValueObjects\Money;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'gateway'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->whereHas('gateway', fn ($q) => $q->where('driver', $request->gateway));
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('user', fn ($sq) => $sq->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"));
        }

        $payments = $query->paginate(25)->withQueryString();

        $counts = Payment::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        $statusPills = [
            ['value' => '',                  'label' => 'All',               'count' => $counts->sum()],
            ['value' => Payment::STATUS_PENDING,            'label' => 'Pending',           'count' => $counts[Payment::STATUS_PENDING] ?? 0],
            ['value' => Payment::STATUS_AWAITING_APPROVAL,  'label' => 'Awaiting Approval', 'count' => $counts[Payment::STATUS_AWAITING_APPROVAL] ?? 0],
            ['value' => Payment::STATUS_COMPLETED,          'label' => 'Completed',         'count' => $counts[Payment::STATUS_COMPLETED] ?? 0],
            ['value' => Payment::STATUS_FAILED,             'label' => 'Failed',            'count' => $counts[Payment::STATUS_FAILED] ?? 0],
            ['value' => Payment::STATUS_CANCELLED,          'label' => 'Cancelled',         'count' => $counts[Payment::STATUS_CANCELLED] ?? 0],
        ];

        return view('admin.payments.index', compact('payments', 'statusPills'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'gateway', 'reviewer', 'transaction']);

        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Payment $payment, WalletService $wallet, SubscriptionService $subscriptionService)
    {
        if (! $payment->isAwaitingApproval()) {
            return back()->withErrors(['error' => 'Payment is not awaiting approval.']);
        }

        $admin = auth('admin')->user();
        $net = Money::of($payment->netAmount(), $payment->currency);

        $tx = $wallet->credit(
            $payment->user,
            $net,
            "Manual payment approved (Payment #{$payment->id})",
            ['payment_id' => $payment->id, 'approved_by' => $admin->email],
            $admin->id,
            Payment::class,
            $payment->id,
        );

        $payment->status = Payment::STATUS_COMPLETED;
        $payment->transaction_id = $tx->id;
        $payment->reviewed_by = $admin->id;
        $payment->reviewed_at = now();
        $payment->save();

        // If this payment was for a plan, activate the subscription
        $planId = $payment->meta['plan_id'] ?? null;
        if ($planId) {
            $plan = Plan::find($planId);
            if ($plan) {
                $subscriptionService->subscribe($payment->user, $plan, $payment);
            }
        }

        return back()->with('success', 'Payment approved and wallet credited.');
    }

    public function reject(Request $request, Payment $payment)
    {
        if (! $payment->isAwaitingApproval()) {
            return back()->withErrors(['error' => 'Payment is not awaiting approval.']);
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $admin = auth('admin')->user();

        $payment->status = Payment::STATUS_FAILED;
        $payment->rejection_reason = $data['rejection_reason'];
        $payment->reviewed_by = $admin->id;
        $payment->reviewed_at = now();
        $payment->save();

        return back()->with('success', 'Payment rejected.');
    }

    public function receipt(Payment $payment)
    {
        if (! $payment->receipt_path) {
            abort(404);
        }

        if (! Storage::disk('private')->exists($payment->receipt_path)) {
            abort(404);
        }

        return Storage::disk('private')->response($payment->receipt_path);
    }
}
