<?php

namespace App\Http\Middleware;

use App\Domain\Settings\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireKyc
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Settings::string('controls.kyc_enabled', '1') !== '1') {
            return $next($request);
        }

        $user = auth('user')->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->kyc_status !== 'approved') {
            return redirect()->route('user.kyc.index')
                ->with('warning', 'You must complete identity verification before requesting payouts.');
        }

        return $next($request);
    }
}
