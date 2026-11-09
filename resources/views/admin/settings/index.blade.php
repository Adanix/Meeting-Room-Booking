<x-layouts.admin :title="__('rooms.nav.settings')">
    <div class="space-y-8 max-w-5xl">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('rooms.nav.settings') }}</h1>
            <p class="text-sm text-slate-500 mt-1">Manage global scheduling parameters, working hours, company holidays, and internal events.</p>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">
                <div class="font-semibold mb-1">Please fix the following errors:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- General & Working Hours Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-8">
            @csrf

            <!-- Section 1: Scheduling Parameters -->
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Scheduling Rules & Durations
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <label for="time_step_minutes" class="block text-xs font-semibold text-slate-700 mb-1.5">Slot Interval (Time Step) *</label>
                        <select id="time_step_minutes" name="time_step_minutes" class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="15" {{ old('time_step_minutes', $settings['time_step_minutes'] ?? 30) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('time_step_minutes', $settings['time_step_minutes'] ?? 30) == 30 ? 'selected' : '' }}>30 minutes (Default)</option>
                            <option value="60" {{ old('time_step_minutes', $settings['time_step_minutes'] ?? 30) == 60 ? 'selected' : '' }}>60 minutes</option>
                        </select>
                    </div>

                    <div>
                        <label for="min_duration_minutes" class="block text-xs font-semibold text-slate-700 mb-1.5">Minimum Meeting Length *</label>
                        <select id="min_duration_minutes" name="min_duration_minutes" class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="15" {{ old('min_duration_minutes', $settings['min_duration_minutes'] ?? 15) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('min_duration_minutes', $settings['min_duration_minutes'] ?? 15) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('min_duration_minutes', $settings['min_duration_minutes'] ?? 15) == 45 ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('min_duration_minutes', $settings['min_duration_minutes'] ?? 15) == 60 ? 'selected' : '' }}>60 minutes</option>
                        </select>
                    </div>

                    <div>
                        <label for="max_duration_minutes" class="block text-xs font-semibold text-slate-700 mb-1.5">Maximum Meeting Length *</label>
                        <select id="max_duration_minutes" name="max_duration_minutes" class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="60" {{ old('max_duration_minutes', $settings['max_duration_minutes'] ?? 240) == 60 ? 'selected' : '' }}>1 hour</option>
                            <option value="90" {{ old('max_duration_minutes', $settings['max_duration_minutes'] ?? 240) == 90 ? 'selected' : '' }}>1.5 hours</option>
                            <option value="120" {{ old('max_duration_minutes', $settings['max_duration_minutes'] ?? 240) == 120 ? 'selected' : '' }}>2 hours</option>
                            <option value="180" {{ old('max_duration_minutes', $settings['max_duration_minutes'] ?? 240) == 180 ? 'selected' : '' }}>3 hours</option>
                            <option value="240" {{ old('max_duration_minutes', $settings['max_duration_minutes'] ?? 240) == 240 ? 'selected' : '' }}>4 hours (Default)</option>
                            <option value="480" {{ old('max_duration_minutes', $settings['max_duration_minutes'] ?? 240) == 480 ? 'selected' : '' }}>8 hours</option>
                        </select>
                    </div>

                    <div>
                        <label for="max_days_ahead" class="block text-xs font-semibold text-slate-700 mb-1.5">Advance Booking Window (Days) *</label>
                        <input type="number" id="max_days_ahead" name="max_days_ahead" value="{{ old('max_days_ahead', $settings['max_days_ahead'] ?? 60) }}" min="1" max="365" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>

                    <div>
                        <label for="office_email" class="block text-xs font-semibold text-slate-700 mb-1.5">Office Notification Email *</label>
                        <input type="email" id="office_email" name="office_email" value="{{ old('office_email', $settings['office_email'] ?? 'office@rooms-demo.local') }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>

                    <div>
                        <label for="office_timezone" class="block text-xs font-semibold text-slate-700 mb-1.5">Office Timezone *</label>
                        <select id="office_timezone" name="office_timezone" class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="Europe/Belgrade" {{ old('office_timezone', $settings['office_timezone'] ?? 'Europe/Belgrade') == 'Europe/Belgrade' ? 'selected' : '' }}>Europe/Belgrade (UTC+1)</option>
                            <option value="UTC" {{ old('office_timezone', $settings['office_timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Europe/London" {{ old('office_timezone', $settings['office_timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            <option value="Europe/Paris" {{ old('office_timezone', $settings['office_timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="America/New_York" {{ old('office_timezone', $settings['office_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                            <option value="Asia/Jakarta" {{ old('office_timezone', $settings['office_timezone'] ?? '') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (UTC+7)</option>
                        </select>
                    </div>
                </div>

                <!-- Company Calendar Banner Toggle -->
                <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="sm:col-span-1">
                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input type="checkbox" name="show_company_calendar" value="1" {{ old('show_company_calendar', $settings['show_company_calendar'] ?? true) ? 'checked' : '' }}
                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-semibold text-slate-800">Display Company Events on Schedule</span>
                        </label>
                    </div>
                    <div>
                        <label for="company_calendar_title_en" class="block text-xs font-semibold text-slate-700 mb-1">Company Calendar Title (EN)</label>
                        <input type="text" id="company_calendar_title_en" name="company_calendar_title_en" value="{{ old('company_calendar_title_en', $settings['company_calendar_title_en'] ?? 'Company Events') }}"
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    </div>
                    <div>
                        <label for="company_calendar_title_sr" class="block text-xs font-semibold text-slate-700 mb-1">Company Calendar Title (SR)</label>
                        <input type="text" id="company_calendar_title_sr" name="company_calendar_title_sr" value="{{ old('company_calendar_title_sr', $settings['company_calendar_title_sr'] ?? 'Događaji kompanije') }}"
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    </div>
                </div>
            </div>

            <!-- Section 2: Working Hours -->
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Facility Working Hours
                </h2>

                <div class="space-y-3">
                    @php
                        $dayLabels = [
                            1 => 'Monday (Ponedeljak)',
                            2 => 'Tuesday (Utorak)',
                            3 => 'Wednesday (Sreda)',
                            4 => 'Thursday (Četvrtak)',
                            5 => 'Friday (Petak)',
                            6 => 'Saturday (Subota)',
                            7 => 'Sunday (Nedelja)',
                            0 => 'Sunday (Nedelja)',
                        ];
                    @endphp

                    @foreach ($workingHours as $wh)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-xl border border-slate-200 gap-3 {{ $wh->is_open ? 'bg-white' : 'bg-slate-50/70' }}">
                            <div class="flex items-center gap-3 min-w-[200px]">
                                <input type="checkbox" name="working_hours[{{ $wh->day_of_week }}][is_open]" value="1" id="wh_{{ $wh->day_of_week }}"
                                       {{ old("working_hours.{$wh->day_of_week}.is_open", $wh->is_open) ? 'checked' : '' }}
                                       class="rounded text-indigo-600 focus:ring-indigo-500">
                                <label for="wh_{{ $wh->day_of_week }}" class="text-sm font-semibold text-slate-800 cursor-pointer">
                                    {{ $dayLabels[$wh->day_of_week] }}
                                </label>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500">Open:</span>
                                    <input type="time" name="working_hours[{{ $wh->day_of_week }}][open_time]"
                                           value="{{ old("working_hours.{$wh->day_of_week}.open_time", substr($wh->open_time, 0, 5)) }}"
                                           class="rounded-lg border-slate-200 text-xs py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <span class="text-slate-400 text-xs">to</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500">Close:</span>
                                    <input type="time" name="working_hours[{{ $wh->day_of_week }}][close_time]"
                                           value="{{ old("working_hours.{$wh->day_of_week}.close_time", substr($wh->close_time, 0, 5)) }}"
                                           class="rounded-lg border-slate-200 text-xs py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                    Save Global Settings & Working Hours
                </button>
            </div>
        </form>

        <!-- Section 3: Holidays Management -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Office Holidays (Facilities Closed)
                </h2>
            </div>

            <!-- Add Holiday Form -->
            <form action="{{ route('admin.holidays.store') }}" method="POST" class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                @csrf
                <div>
                    <label for="holiday_date" class="block text-xs font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" id="holiday_date" name="holiday_date" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <label for="h_name_en" class="block text-xs font-semibold text-slate-700 mb-1">Name (EN) *</label>
                    <input type="text" id="h_name_en" name="name_en" placeholder="e.g. Statehood Day" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <label for="h_name_sr" class="block text-xs font-semibold text-slate-700 mb-1">Name (SR) *</label>
                    <input type="text" id="h_name_sr" name="name_sr" placeholder="npr. Dan državnosti" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div class="flex items-center h-10">
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1" class="rounded text-indigo-600 focus:ring-indigo-500">
                        Annual recurring
                    </label>
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition">
                        + Add Holiday
                    </button>
                </div>
            </form>

            <!-- Holidays List -->
            <div class="divide-y divide-slate-100">
                @forelse ($holidays as $h)
                    <div class="py-3 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-semibold">
                                {{ $h->holiday_date->format('Y-m-d') }}
                            </span>
                            <div>
                                <span class="font-semibold text-slate-800">{{ $h->name_en }}</span>
                                <span class="text-xs text-slate-500 ml-2">({{ $h->name_sr }})</span>
                                @if ($h->is_recurring)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold ml-2">Recurring</span>
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('admin.holidays.destroy', $h) }}" method="POST" onsubmit="return confirm('Remove holiday {{ $h->name_en }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1">
                                Delete
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-4 text-center text-sm text-slate-400 italic">No holidays configured.</div>
                @endforelse
            </div>
        </div>

        <!-- Section 4: Company Events -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    Company Events Schedule
                </h2>
            </div>

            <!-- Add Company Event Form -->
            <form action="{{ route('admin.company-events.store') }}" method="POST" class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                @csrf
                <div>
                    <label for="event_date" class="block text-xs font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" id="event_date" name="event_date" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <label for="ev_start" class="block text-xs font-semibold text-slate-700 mb-1">Start *</label>
                    <input type="time" id="ev_start" name="start_time" value="09:00" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <label for="ev_end" class="block text-xs font-semibold text-slate-700 mb-1">End *</label>
                    <input type="time" id="ev_end" name="end_time" value="17:00" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <label for="ev_title_en" class="block text-xs font-semibold text-slate-700 mb-1">Title (EN) *</label>
                    <input type="text" id="ev_title_en" name="title_en" placeholder="e.g. All-Hands" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <label for="ev_title_sr" class="block text-xs font-semibold text-slate-700 mb-1">Title (SR) *</label>
                    <input type="text" id="ev_title_sr" name="title_sr" placeholder="npr. Kolektivni sastanak" required class="w-full rounded-lg border-slate-200 text-xs py-2">
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition">
                        + Add Event
                    </button>
                </div>
            </form>

            <!-- Events List -->
            <div class="divide-y divide-slate-100">
                @forelse ($companyEvents as $ev)
                    <div class="py-3 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 font-semibold">
                                {{ $ev->event_date->format('Y-m-d') }}
                            </span>
                            <div>
                                <span class="font-semibold text-slate-800">{{ $ev->title_en }}</span>
                                <span class="text-xs text-slate-500 ml-2">({{ $ev->title_sr }})</span>
                                <span class="text-xs text-slate-400 ml-2">
                                    {{ substr($ev->start_time, 0, 5) }} - {{ substr($ev->end_time, 0, 5) }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('admin.company-events.destroy', $ev) }}" method="POST" onsubmit="return confirm('Remove event {{ $ev->title_en }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1">
                                Delete
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-4 text-center text-sm text-slate-400 italic">No company events scheduled.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
