<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $force = Settings::string('controls.force_https') === '1';
        } catch (\Throwable) {
            return $next($request);
        }

        if ($force) {
            URL::forceScheme('https');

            if (! $request->secure() && ! $request->isMethod('OPTIONS')) {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }
}
