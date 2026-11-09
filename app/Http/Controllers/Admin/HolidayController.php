<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date'],
            'name_en' => ['required', 'string', 'max:120'],
            'name_sr' => ['required', 'string', 'max:120'],
            'is_recurring' => ['boolean'],
        ]);

        $holiday = Holiday::create([
            'holiday_date' => $validated['holiday_date'],
            'name_en' => $validated['name_en'],
            'name_sr' => $validated['name_sr'],
            'is_recurring' => $request->boolean('is_recurring'),
        ]);

        return back()->with('status', "Holiday '{$holiday->name_en}' added.");
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $name = $holiday->name_en;
        $holiday->delete();

        return back()->with('status', "Holiday '{$name}' deleted.");
    }
}
