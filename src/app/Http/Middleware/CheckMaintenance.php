<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $enabled = Settings::bool('site.maintenance_mode', false);
        } catch (\Throwable) {
            return $next($request);
        }

        if (! $enabled) {
            return $next($request);
        }

        // Admin panel always accessible
        if ($request->is('admin*')) {
            return $next($request);
        }

        // Allow-listed IPs
        $allowedIps = array_filter(
            explode(',', Settings::string('site.maintenance_allowed_ips', ''))
        );
        if (in_array($request->ip(), $allowedIps, true)) {
            return $next($request);
        }

        // Bypass token via query string
        $token = Settings::string('site.maintenance_token', '');
        if ($token && $request->query('bypass') === $token) {
            return $next($request);
        }

        return response()->view('theme::errors.503', [], 503);
    }
}
