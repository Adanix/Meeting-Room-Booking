<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function timeStepMinutes(): int
    {
        return (int) Setting::get('time_step_minutes', 15);
    }

    public function minDurationMinutes(): int
    {
        return (int) Setting::get('min_duration_minutes', 15);
    }

    public function maxDurationMinutes(): int
    {
        return (int) Setting::get('max_duration_minutes', 240);
    }

    public function maxDaysAhead(): int
    {
        return (int) Setting::get('max_days_ahead', 30);
    }

    public function officeEmail(): string
    {
        return (string) Setting::get('office_email', config('demo.office_email', 'office@rooms-demo.local'));
    }

    public function officeTimezone(): string
    {
        return (string) Setting::get('office_timezone', config('app.timezone', 'Europe/Belgrade'));
    }

    public function showCompanyCalendar(): bool
    {
        return (bool) Setting::get('show_company_calendar', true);
    }

    public function companyCalendarTitle(string $locale = 'en'): string
    {
        if ($locale === 'sr_Latn') {
            return (string) Setting::get('company_calendar_title_sr', 'Događaji kompanije');
        }

        return (string) Setting::get('company_calendar_title_en', 'Company Events');
    }

    public function all(): array
    {
        return [
            'time_step_minutes' => $this->timeStepMinutes(),
            'min_duration_minutes' => $this->minDurationMinutes(),
            'max_duration_minutes' => $this->maxDurationMinutes(),
            'max_days_ahead' => $this->maxDaysAhead(),
            'office_email' => $this->officeEmail(),
            'office_timezone' => $this->officeTimezone(),
            'show_company_calendar' => $this->showCompanyCalendar(),
            'company_calendar_title_en' => $this->companyCalendarTitle('en'),
            'company_calendar_title_sr' => $this->companyCalendarTitle('sr_Latn'),
        ];
    }
}
