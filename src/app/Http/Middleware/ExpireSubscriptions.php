<?php

namespace App\Http\Middleware;

use App\Domain\Subscription\Models\UserSubscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpireSubscriptions
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('user');

        if ($user) {
            UserSubscription::where('user_id', $user->id)
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->where('current_period_end', '<', now())
                ->update(['status' => UserSubscription::STATUS_EXPIRED]);
        }

        return $next($request);
    }
}
