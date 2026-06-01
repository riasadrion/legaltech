<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
    /**
     * Routes that remain writable even in demo mode (e.g. logout),
     * matched against the route name.
     */
    private array $allowedRoutes = [
        'admin.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.demo')) {
            return $next($request);
        }

        // Read-only requests are always allowed.
        if ($request->isMethodSafe()) {
            return $next($request);
        }

        // Allow-listed write routes (logout, etc.).
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->allowedRoutes, true)) {
            return $next($request);
        }

        $message = __('This action is disabled in demo mode.');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 403);
        }

        return back()->with('warning', $message);
    }
}
