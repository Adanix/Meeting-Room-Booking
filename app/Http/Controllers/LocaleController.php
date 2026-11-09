<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(['en', 'sr_Latn'])],
        ]);

        $locale = $validated['locale'];
        $request->session()->put('locale', $locale);

        if ($user = $request->user()) {
            $user->update(['preferred_locale' => $locale]);
        }

        Cookie::queue(Cookie::make('locale', $locale, 60 * 24 * 365));

        return back();
    }
}
