<x-layouts.admin :title="__('rooms.nav.calendar')">
    <div class="space-y-6" x-data="{ selectedDay: null, selectedDayBookings: [], selectedDayDate: '' }">
        <!-- Calendar Header Controls -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.calendar', ['month' => $prevMonth, 'room_id' => $selectedRoomId]) }}"
                   class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Previous Month">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight min-w-[180px]">
                    {{ $currentMonth->isoFormat('MMMM YYYY') }}
                </h1>
                <a href="{{ route('admin.calendar', ['month' => $nextMonth, 'room_id' => $selectedRoomId]) }}"
                   class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Next Month">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('admin.calendar', ['month' => now()->format('Y-m'), 'room_id' => $selectedRoomId]) }}"
                   class="ml-2 px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 hover:bg-slate-50 text-slate-700 transition">
                    {{ __('rooms.schedule.today') }}
                </a>
            </div>

            <!-- Filters & Create -->
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('admin.calendar') }}" class="flex items-center gap-2">
                    <input type="hidden" name="month" value="{{ $monthInput }}">
                    <label for="room_filter" class="sr-only">Room</label>
                    <select id="room_filter" name="room_id" onchange="this.form.submit()"
                            class="rounded-xl border-slate-200 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                        <option value="">{{ __('rooms.portal.all_rooms') }}</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ $selectedRoomId == $room->id ? 'selected' : '' }}>
                                {{ $room->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('rooms.admin.new_booking') }}
                </a>
            </div>
        </div>

        <!-- 7-Column Month Grid -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Day of Week Headers (Mon - Sun) -->
            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50/80 text-center py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div class="text-rose-600">Sat</div>
                <div class="text-rose-600">Sun</div>
            </div>

            <!-- Days Grid -->
            <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-slate-100">
                @foreach ($days as $day)
                    <div class="min-h-[110px] sm:min-h-[130px] p-1.5 sm:p-2 flex flex-col justify-between transition hover:bg-slate-50/60 {{ ! $day['is_current_month'] ? 'bg-slate-50/40 text-slate-300' : 'bg-white' }} {{ $day['is_today'] ? 'ring-2 ring-indigo-500 ring-inset bg-indigo-50/10' : '' }}">
                        <div class="flex items-center justify-between mb-1">
                            @if ($day['holiday'])
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 truncate max-w-[70px] sm:max-w-[100px]" title="{{ $day['holiday']->localized_name }}">
                                    {{ $day['holiday']->localized_name }}
                                </span>
                            @else
                                <span></span>
                            @endif

                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold rounded-full {{ $day['is_today'] ? 'bg-indigo-600 text-white' : ($day['is_current_month'] ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $day['date']->day }}
                            </span>
                        </div>

                        <!-- Bookings preview in cell -->
                        <div class="space-y-1 flex-1 overflow-hidden">
                            @foreach ($day['bookings']->take(2) as $booking)
                                <a href="{{ route('admin.bookings.edit', $booking) }}"
                                   class="block text-[11px] leading-tight px-1.5 py-1 rounded truncate border transition {{ $booking->isConfirmed() ? 'border-transparent text-slate-900' : 'line-through opacity-60 border-slate-200 text-slate-400' }}"
                                   style="background-color: {{ $booking->room->color_hex }}15; border-left: 3px solid {{ $booking->room->color_hex }};"
                                   title="{{ $booking->start_at->format('H:i') }} - {{ $booking->end_at->format('H:i') }} | {{ $booking->room->localized_name }} | {{ $booking->title }} ({{ $booking->organizer_name }})">
                                    <span class="font-bold">{{ $booking->start_at->format('H:i') }}</span>
                                    <span>{{ $booking->title }}</span>
                                </a>
                            @endforeach

                            @if ($day['bookings']->count() > 2)
                                <button type="button"
                                        @click="selectedDay = '{{ $day['date']->isoFormat('dddd, MMMM D, YYYY') }}'; selectedDayDate = '{{ $day['date_string'] }}'; selectedDayBookings = {{ $day['bookings']->map(fn ($b) => [
                                            'id' => $b->id,
                                            'reference_code' => $b->reference_code,
                                            'title' => $b->title,
                                            'room_name' => $b->room->localized_name,
                                            'room_color' => $b->room->color_hex,
                                            'time' => $b->start_at->format('H:i') . ' - ' . $b->end_at->format('H:i'),
                                            'organizer' => $b->organizer_name,
                                            'is_confirmed' => $b->isConfirmed(),
                                            'edit_url' => route('admin.bookings.edit', $b),
                                        ])->toJson() }}"
                                        class="w-full text-left text-[10px] font-bold text-indigo-600 hover:text-indigo-800 px-1 py-0.5 rounded bg-indigo-50/60">
                                    +{{ $day['bookings']->count() - 2 }} more...
                                </button>
                            @endif
                        </div>

                        <!-- Cell Quick Add -->
                        <div class="mt-1 pt-1 flex justify-end">
                            <a href="{{ route('admin.bookings.create', ['booking_date' => $day['date_string'], 'room_id' => $selectedRoomId]) }}"
                               class="text-[10px] text-slate-300 hover:text-indigo-600 transition" title="Add reservation on this day">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Day Drill-down Modal via Alpine -->
        <div x-show="selectedDay !== null"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;"
             @keydown.escape.window="selectedDay = null">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="selectedDay = null"></div>
                <div class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900" x-text="selectedDay"></h3>
                            <p class="text-xs text-slate-500">Reservations scheduled on this date</p>
                        </div>
                        <button type="button" @click="selectedDay = null" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="py-4 space-y-2.5 max-h-96 overflow-y-auto">
                        <template x-for="item in selectedDayBookings" :key="item.id">
                            <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3"
                                 :style="'border-left-width: 4px; border-left-color: ' + item.room_color">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700" x-text="item.reference_code"></span>
                                        <span class="text-xs font-bold text-slate-600" x-text="item.room_name"></span>
                                        <template x-if="!item.is_confirmed">
                                            <span class="text-[10px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">Cancelled</span>
                                        </template>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-900 mt-1" x-text="item.title"></h4>
                                    <p class="text-xs text-slate-500">
                                        <span x-text="item.time"></span> • Organizer: <span class="text-slate-700 font-medium" x-text="item.organizer"></span>
                                    </p>
                                </div>
                                <a :href="item.edit_url" class="px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    {{ __('rooms.common.edit') }}
                                </a>
                            </div>
                        </template>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a :href="'{{ route('admin.bookings.create') }}?booking_date=' + selectedDayDate"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Book a room on this date
                        </a>
                        <button type="button" @click="selectedDay = null" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-xl">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
