<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoModeEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled', false)) {
            abort(404, 'Demo mode is disabled on this environment.');
        }

        return $next($request);
    }
}
