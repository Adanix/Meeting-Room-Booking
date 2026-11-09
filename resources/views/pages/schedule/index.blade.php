<x-layouts.app>
    <x-slot:title>Daily Schedule — {{ $currentDate->format('l, d M Y') }} — ROOMS</x-slot:title>

    <!-- Header & Navigation Bar -->
    <header class="w-full mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                    {{ __('rooms.daily_schedule') }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600 truncate">
                    {{ __('rooms.all_rooms_schedule') }} &bull; {{ $currentDate->format('l, d F Y') }}
                </p>
            </div>

            <!-- Date Switcher Controls -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('schedule.index', ['date' => $prevDate]) }}"
                   class="px-3 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-xs">
                    &larr; {{ __('rooms.prev_day') }}
                </a>

                <a href="{{ route('schedule.index', ['date' => $todayDate]) }}"
                   class="px-3 py-2 rounded-xl text-xs font-bold {{ $selectedDate === $todayDate ? 'bg-indigo-600 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' }} shadow-xs">
                    {{ __('rooms.today') }}
                </a>

                <a href="{{ route('schedule.index', ['date' => $nextDate]) }}"
                   class="px-3 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-xs">
                    {{ __('rooms.next_day') }} &rarr;
                </a>

                <form method="GET" action="{{ route('schedule.index') }}" class="inline-block">
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                           class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 shadow-xs outline-hidden">
                </form>
            </div>
        </div>
    </header>

    <!-- Holiday or Weekend Closure Notice -->
    @if($holiday)
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span><strong>Company Holiday:</strong> {{ $holiday->localizedName() }}. The office is officially closed on this date.</span>
        </div>
    @elseif($workingHour && !$workingHour->is_open)
        <div class="mb-6 p-4 rounded-2xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Non-Working Day:</strong> The office is closed on weekends ({{ $workingHour->localizedDayName() }}).</span>
        </div>
    @endif

    <!-- Optional Company Events Strip -->
    @if(!empty($companyEvents) && $companyEvents->isNotEmpty())
        <section class="mb-8 p-4 sm:p-5 rounded-2xl bg-indigo-50/60 border border-indigo-100">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-950">
                    {{ __('rooms.company_events') }} ({{ $currentDate->format('M d') }})
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($companyEvents as $event)
                    <div class="bg-white p-3 rounded-xl border border-indigo-100/80 shadow-xs text-xs">
                        <div class="font-bold text-slate-900 truncate">{{ $event->localizedTitle() }}</div>
                        <div class="text-slate-500 text-[11px] mt-0.5">
                            {{ substr($event->start_time, 0, 5) }} – {{ substr($event->end_time, 0, 5) }} &bull; {{ $event->location }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Desktop / Tablet Horizontal Multi-Room Matrix (Hidden on < 768px) -->
    <div class="hidden md:block w-full max-w-full overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="min-w-[900px]">
            <!-- Timeline Time Header Axis (08:00 to 18:00) -->
            <div class="grid grid-cols-12 border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold text-slate-500 py-3">
                <div class="col-span-3 px-4 font-bold uppercase tracking-wider text-slate-600">
                    Meeting Room
                </div>
                <div class="col-span-9 grid grid-cols-10 text-center font-mono text-slate-600 divide-x divide-slate-200/60">
                    <span>08:00</span>
                    <span>09:00</span>
                    <span>10:00</span>
                    <span>11:00</span>
                    <span>12:00</span>
                    <span>13:00</span>
                    <span>14:00</span>
                    <span>15:00</span>
                    <span>16:00</span>
                    <span>17:00</span>
                </div>
            </div>

            <!-- Rooms Rows -->
            <div class="divide-y divide-slate-100">
                @foreach($rooms as $room)
                    <div class="grid grid-cols-12 items-center py-4 hover:bg-slate-50/50 transition-colors">
                        <!-- Sticky Room Label (3 cols) -->
                        <div class="col-span-3 px-4 min-w-0 flex items-center gap-3">
                            @if($room->photoUrl())
                                <img src="{{ $room->photoUrl() }}" alt="{{ $room->localizedName() }}"
                                     class="w-10 h-10 rounded-lg object-cover shrink-0 border border-slate-200 shadow-2xs">
                            @else
                                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $room->color_hex }};"></span>
                            @endif
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold text-slate-900 truncate">
                                    {{ $room->localizedName() }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                    Cap: {{ $room->capacity }} &bull; {{ $room->location }}
                                </p>
                            </div>
                        </div>

                        <!-- Schedule Bar (9 cols) -->
                        <div class="col-span-9 px-2 relative h-12 flex items-center bg-slate-50/40 rounded-xl border border-slate-100 mr-4">
                            @php
                                $dayStartMinutes = 8 * 60; // 08:00
                                $dayTotalMinutes = 10 * 60; // 10 hours (08:00 to 18:00 = 600 mins)
                            @endphp

                            <!-- Confirmed Bookings Chips positioned by percentage -->
                            @foreach($room->bookings as $b)
                                @php
                                    $bStartMinutes = ($b->start_at->hour * 60) + $b->start_at->minute;
                                    $bEndMinutes = ($b->end_at->hour * 60) + $b->end_at->minute;

                                    $leftPercent = max(0, min(100, (($bStartMinutes - $dayStartMinutes) / $dayTotalMinutes) * 100));
                                    $widthPercent = max(2, min(100 - $leftPercent, ($b->duration_minutes / $dayTotalMinutes) * 100));
                                @endphp
                                <div style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%;"
                                     title="{{ $b->title }} ({{ $b->timeRangeString() }}) by {{ $b->organizer_name }}"
                                     class="absolute h-9 rounded-lg bg-indigo-600 text-white p-1 text-[10px] font-semibold flex items-center justify-between overflow-hidden shadow-xs border border-indigo-700/60 hover:z-20 hover:scale-102 transition-all cursor-pointer">
                                    <span class="truncate pl-1">{{ $b->timeRangeString() }} {{ $b->title }}</span>
                                </div>
                            @endforeach

                            <!-- Quick Book CTA inside open slot -->
                            <a href="{{ route('rooms.book', ['room' => $room->slug, 'date' => $selectedDate]) }}"
                               class="absolute inset-0 flex items-center justify-end pr-3 opacity-0 hover:opacity-100 transition-opacity text-[11px] font-bold text-indigo-600">
                                <span>+ {{ __('rooms.click_to_book') }}</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Mobile Stacked Room Cards View (Active on < 768px for Anti-Overflow Guarantee) -->
    <div class="md:hidden space-y-4">
        @foreach($rooms as $room)
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $room->color_hex }};"></span>
                        <h3 class="text-sm font-bold text-slate-900 truncate">{{ $room->localizedName() }}</h3>
                    </div>
                    <span class="text-xs font-medium text-slate-500">Max {{ $room->capacity }} ppl</span>
                </div>

                <!-- Bookings on this day -->
                @if($room->bookings->isEmpty())
                    <p class="text-xs text-slate-400 italic py-2">No meetings booked on this date.</p>
                @else
                    <div class="space-y-2 mb-3">
                        @foreach($room->bookings as $b)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                <div class="flex items-center justify-between text-slate-500 text-[11px] mb-0.5">
                                    <span class="font-bold text-indigo-700">{{ $b->timeRangeString() }}</span>
                                    <span>{{ $b->duration_minutes }} min</span>
                                </div>
                                <div class="font-semibold text-slate-900 truncate">{{ $b->title }}</div>
                                <div class="text-[11px] text-slate-500 truncate">Organizer: {{ $b->organizer_name }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                    <a href="{{ route('rooms.book', ['room' => $room->slug, 'date' => $selectedDate]) }}"
                       class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        <span>Book this room</span> &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
