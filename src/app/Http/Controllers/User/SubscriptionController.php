<?php

namespace App\Http\Controllers\User;

use App\Domain\Subscription\Models\CreditLog;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\SubscriptionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth('user')->user();
        $subscription = $user->subscription()->with('plan')->first();
        $creditHistory = CreditLog::where('user_id', $user->id)->latest()->paginate(15);

        return view('user.subscription.index', compact('subscription', 'creditHistory'));
    }

    public function plans()
    {
        $plans = Plan::active()->orderBy('sort')->get();

        return view('user.subscription.plans', compact('plans'));
    }

    public function subscribe(Request $request, SubscriptionService $subscriptionService)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $user = auth('user')->user();
        $plan = Plan::findOrFail($data['plan_id']);

        if ((float) $plan->price === 0.0 && $plan->hasTrialPeriod()) {
            $subscriptionService->startTrial($user, $plan);

            return redirect()->route('user.subscription.index')->with('success', 'Trial started successfully!');
        }

        return redirect()->route('user.payment.index', ['plan_id' => $plan->id]);
    }
}
