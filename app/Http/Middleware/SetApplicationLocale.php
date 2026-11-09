<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApplicationLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['en', 'sr_Latn'];

        $locale = $request->session()->get('locale')
            ?: $request->cookie('locale')
            ?: ($request->user()?->preferred_locale)
            ?: config('app.locale', 'en');

        if (! in_array($locale, $allowedLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale === 'sr_Latn' ? 'sr_Latn_RS' : 'en');

        return $next($request);
    }
}
