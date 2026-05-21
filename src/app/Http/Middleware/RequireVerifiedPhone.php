<?php

namespace App\Http\Middleware;

use App\Domain\Settings\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireVerifiedPhone
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Settings::string('controls.sms_verification') !== '1') {
            return $next($request);
        }

        $user = $request->user('user');

        if ($user && ! $user->mobile_verified_at) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Phone verification required.'], 403);
            }

            return redirect()->route('phone.verify.show');
        }

        return $next($request);
    }
}
