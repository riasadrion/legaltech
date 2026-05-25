<?php

namespace App\Http\Controllers\User;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Subscription\Models\CreditLog;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\SubscriptionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function checkout(Plan $plan)
    {
        $gateways = Gateway::enabled()->orderBy('sort')->get();

        return view('user.subscription.checkout', compact('plan', 'gateways'));
    }

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

        // Free plan — subscribe immediately
        if ((float) $plan->price === 0.0) {
            $subscriptionService->subscribe($user, $plan);

            return redirect()->route('user.subscription.index')->with('success', __('Subscription activated!'));
        }

        // Paid plan — go to gateway selection with plan context
        return redirect()->route('user.subscription.checkout', $plan);
    }
}
