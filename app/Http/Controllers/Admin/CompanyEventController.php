<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyEventController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_en' => ['required', 'string', 'max:120'],
            'title_sr' => ['required', 'string', 'max:120'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);

        CompanyEvent::create($validated);

        return back()->with('status', 'Company event added successfully.');
    }

    public function destroy(CompanyEvent $companyEvent): RedirectResponse
    {
        $companyEvent->delete();

        return back()->with('status', 'Company event deleted.');
    }
}
