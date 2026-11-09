<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyEvent;
use App\Models\Holiday;
use App\Models\Setting;
use App\Models\WorkingHour;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function index(): View
    {
        $settings = $this->settingService->all();
        $workingHours = WorkingHour::orderBy('day_of_week')->get();
        $holidays = Holiday::orderBy('holiday_date')->get();
        $companyEvents = CompanyEvent::orderByDesc('event_date')->get();

        return view('admin.settings.index', [
            'settings' => $settings,
            'workingHours' => $workingHours,
            'holidays' => $holidays,
            'companyEvents' => $companyEvents,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'time_step_minutes' => ['required', 'integer', 'in:15,30,60'],
            'min_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
            'max_duration_minutes' => ['required', 'integer', 'in:60,90,120,180,240,480'],
            'max_days_ahead' => ['required', 'integer', 'min:1', 'max:365'],
            'office_email' => ['required', 'email', 'max:150'],
            'office_timezone' => ['required', 'string', 'timezone'],
            'show_company_calendar' => ['boolean'],
            'company_calendar_title_en' => ['nullable', 'string', 'max:120'],
            'company_calendar_title_sr' => ['nullable', 'string', 'max:120'],
            'working_hours' => ['required', 'array'],
        ]);

        if ($validated['min_duration_minutes'] < $validated['time_step_minutes']) {
            return back()->withErrors(['min_duration_minutes' => 'Shortest duration cannot be smaller than time step.']);
        }
        if ($validated['max_duration_minutes'] < $validated['min_duration_minutes']) {
            return back()->withErrors(['max_duration_minutes' => 'Longest duration cannot be smaller than shortest duration.']);
        }

        Setting::set('time_step_minutes', (int) $validated['time_step_minutes'], 'integer');
        Setting::set('min_duration_minutes', (int) $validated['min_duration_minutes'], 'integer');
        Setting::set('max_duration_minutes', (int) $validated['max_duration_minutes'], 'integer');
        Setting::set('max_days_ahead', (int) $validated['max_days_ahead'], 'integer');
        Setting::set('office_email', $validated['office_email'], 'string');
        Setting::set('office_timezone', $validated['office_timezone'], 'string');
        Setting::set('show_company_calendar', $request->boolean('show_company_calendar'), 'boolean');
        Setting::set('company_calendar_title_en', $validated['company_calendar_title_en'] ?? 'Company Events', 'string');
        Setting::set('company_calendar_title_sr', $validated['company_calendar_title_sr'] ?? 'Događaji kompanije', 'string');

        // Update working hours
        foreach ($validated['working_hours'] as $day => $h) {
            WorkingHour::where('day_of_week', $day)->update([
                'is_open' => isset($h['is_open']),
                'open_time' => $h['open_time'] ?? '08:00:00',
                'close_time' => $h['close_time'] ?? '18:00:00',
            ]);
        }

        return redirect()->route('admin.settings.index')
            ->with('status', 'System settings and working hours saved successfully.');
    }
}
