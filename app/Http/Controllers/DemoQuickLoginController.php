<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoQuickLoginController extends Controller
{
    public function show(): View
    {
        $personas = config('demo.personas', []);

        return view('pages.demo_login', compact('personas'));
    }

    public function login(Request $request): RedirectResponse
    {
        $personas = config('demo.personas', []);

        $validated = $request->validate([
            'persona_key' => ['required', 'string', Rule::in(array_keys($personas))],
        ]);

        $personaKey = $validated['persona_key'];
        $personaConfig = $personas[$personaKey];

        $user = User::where('email', $personaConfig['email'])
            ->where('is_active', true)
            ->first();

        if (! $user) {
            return back()->withErrors(['persona_key' => 'Demo user account not found. Please reset demo data.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $roleName = $user->name;
        $toastMsg = app()->getLocale() === 'sr_Latn'
            ? "Prijavljeni ste kao: {$roleName} ({$personaConfig['role']})"
            : "Successfully signed in as: {$roleName} ({$personaConfig['role']})";

        return redirect()->intended(route($personaConfig['redirect_route']))
            ->with('status', $toastMsg);
    }

    public function resetDemoData(Request $request): RedirectResponse
    {
        Artisan::call('db:seed', ['--force' => true]);

        $msg = app()->getLocale() === 'sr_Latn'
            ? 'Demo podaci su uspešno resetovani na početno stanje.'
            : 'Demo dataset has been successfully reset to factory defaults.';

        return back()->with('status', $msg);
    }
}
