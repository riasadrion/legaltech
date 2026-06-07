<?php

namespace App\Http\Controllers\User;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Subscription\Models\CreditLog;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\SubscriptionService;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function checkout(Plan $plan, SubscriptionService $subscriptionService)
    {
        // Free plan — subscribe immediately, no gateway selection needed
        if ((float) $plan->price === 0.0) {
            $subscriptionService->subscribe(auth('user')->user(), $plan);

            return redirect()->route('user.subscription.index')->with('success', __('Subscription activated!'));
        }

        $gateways = Gateway::enabled()->orderBy('sort')->get();

        return view('theme::user.subscription.checkout', compact('plan', 'gateways'));
    }

    public function index()
    {
        $user = auth('user')->user();
        $subscription = $user->subscription()->with('plan')->first();
        $creditHistory = CreditLog::where('user_id', $user->id)->latest()->paginate(15);

        return view('theme::user.subscription.index', compact('subscription', 'creditHistory'));
    }

    public function plans()
    {
        $plans = Plan::active()->orderBy('sort')->get();

        return view('theme::user.subscription.plans', compact('plans'));
    }

}
