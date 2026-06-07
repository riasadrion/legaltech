<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\Models\UserSubscription;
use App\Domain\Subscription\SubscriptionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserSubscription::with(['user', 'plan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan_id', $request->plan);
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $subscriptions = $query->paginate(25)->withQueryString();

        $statusPills = [
            ['value' => '', 'label' => __('All'), 'count' => UserSubscription::count()],
            ['value' => UserSubscription::STATUS_ACTIVE, 'label' => __('Active'), 'count' => UserSubscription::where('status', UserSubscription::STATUS_ACTIVE)->count()],
            ['value' => UserSubscription::STATUS_PAST_DUE, 'label' => __('Past Due'), 'count' => UserSubscription::where('status', UserSubscription::STATUS_PAST_DUE)->count()],
            ['value' => UserSubscription::STATUS_CANCELLED, 'label' => __('Cancelled'), 'count' => UserSubscription::where('status', UserSubscription::STATUS_CANCELLED)->count()],
            ['value' => UserSubscription::STATUS_EXPIRED, 'label' => __('Expired'), 'count' => UserSubscription::where('status', UserSubscription::STATUS_EXPIRED)->count()],
        ];

        $plans = Plan::orderBy('name')->get(['id', 'name']);

        return view('admin.subscriptions.index', compact('subscriptions', 'statusPills', 'plans'));
    }

    public function show(UserSubscription $subscription): View
    {
        $subscription->load(['user', 'plan', 'payment.gateway', 'creditLogs' => fn ($q) => $q->latest()->limit(50)]);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(UserSubscription $subscription, SubscriptionService $service)
    {
        if ($subscription->isExpired()) {
            return back()->withErrors(['error' => __('Subscription is already inactive.')]);
        }

        $service->cancel($subscription);

        return back()->with('success', __('Subscription cancelled.'));
    }
}
