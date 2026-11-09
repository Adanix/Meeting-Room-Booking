<x-layouts.app>
    <x-slot:title>Book {{ $room->localizedName() }} — ROOMS</x-slot:title>

    <div x-data="{
        selectedDate: '{{ $selectedDate }}',
        selectedStartTime: '{{ $selectedStartTime }}',
        selectedDuration: null,
        selectedAttendees: {{ $selectedPeople ?: 2 }},
        title: '',
        agenda: '',
        slots: {{ Js::from($slotData['slots'] ?? []) }},
        isHoliday: {{ $slotData['is_holiday'] ? 'true' : 'false' }},
        isClosed: {{ $slotData['is_closed'] ? 'true' : 'false' }},
        holidayName: '{{ $slotData['holiday_name_' . (app()->getLocale() === 'sr_Latn' ? 'sr' : 'en')] ?? '' }}',
        closedReason: '{{ $slotData['closed_reason'] ?? '' }}',
        loadingSlots: false,

        async fetchSlots() {
            this.loadingSlots = true;
            this.selectedStartTime = null;
            this.selectedDuration = null;
            try {
                const res = await fetch(`/api/rooms/{{ $room->id }}/available-slots?date=${this.selectedDate}`);
                const data = await res.json();
                this.slots = data.slots || [];
                this.isHoliday = data.is_holiday || false;
                this.isClosed = data.is_closed || false;
                this.holidayName = data.holiday_name_en || '';
                this.closedReason = data.closed_reason || '';
            } catch (e) {
                console.error('Failed to load slots', e);
            } finally {
                this.loadingSlots = false;
            }
        },

        get currentSlot() {
            return this.slots.find(s => s.time === this.selectedStartTime);
        },

        get validDurations() {
            return this.currentSlot ? this.currentSlot.valid_durations : [];
        },

        selectStart(time) {
            this.selectedStartTime = time;
            const slot = this.slots.find(s => s.time === time);
            if (slot && slot.valid_durations.length > 0) {
                this.selectedDuration = slot.valid_durations[0].minutes;
            }
        }
    }">

        <!-- Anti-Overflow Page Header -->
        <header class="w-full mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
                <div class="min-w-0 flex-1">
                    <a href="{{ route('rooms.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline inline-flex items-center gap-1 mb-1.5">
                        &larr; Back to all rooms
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                        {{ __('rooms.book_room') }}: {{ $room->localizedName() }}
                    </h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-600 truncate">
                        {{ $room->location }} &bull; Max {{ $room->capacity }} people
                    </p>
                </div>
            </div>
        </header>

        <!-- 12-Column Layout (8 Cols Steps Form, 4 Cols Sticky Summary) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Form Steps (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                <form id="booking-form" method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <!-- Honeypot spam guard -->
                    <input type="text" name="website_url" value="" class="hidden" tabindex="-1" autocomplete="off">

                    <!-- Step 1: Room & Date -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h2 class="text-base font-bold text-slate-900">{{ __('rooms.choose_date') }} & Room</h2>
                        </div>

                        @if($room->photoUrl())
                            <div class="mb-5 relative h-36 sm:h-44 rounded-xl overflow-hidden border border-slate-200 shadow-xs">
                                <img src="{{ $room->photoUrl() }}" alt="{{ $room->localizedName() }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/20 to-transparent"></div>
                                <div class="absolute bottom-3 left-4 text-white">
                                    <span class="text-base font-bold block">{{ $room->localizedName() }}</span>
                                    <span class="text-xs text-slate-300">{{ $room->location }} &bull; Max {{ $room->capacity }} people</span>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Room Switcher -->
                            <div>
                                <label for="room_slug" class="block text-xs font-semibold text-slate-700 mb-1">Room</label>
                                <select id="room_slug"
                                        onchange="window.location.href = '/rooms/' + this.value + '/book?date=' + encodeURIComponent(document.getElementById('booking_date').value)"
                                        class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                                    @foreach($allRooms as $r)
                                        <option value="{{ $r->slug }}" {{ $r->id === $room->id ? 'selected' : '' }}>
                                            {{ $r->localizedName() }} (Max {{ $r->capacity }} ppl)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Target Date Picker -->
                            <div>
                                <label for="booking_date" class="block text-xs font-semibold text-slate-700 mb-1">Reservation Date</label>
                                <input type="date" name="booking_date" id="booking_date"
                                       x-model="selectedDate"
                                       @change="fetchSlots()"
                                       min="{{ $todayDate }}" max="{{ $maxDate }}" required
                                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                            </div>
                        </div>

                        <!-- Holiday or Closure Alert -->
                        <div x-show="isHoliday" class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium" style="display: none;">
                            <strong>Office Closure / Holiday:</strong> The office is closed on this date (<span x-text="holidayName"></span>). Please pick another business day.
                        </div>

                        <div x-show="isClosed && !isHoliday" class="mt-4 p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium" style="display: none;">
                            <strong>Non-Working Day:</strong> The office is closed on this day of the week. Please choose a Monday–Friday date.
                        </div>
                    </div>

                    <!-- Step 2: Choose Free Start Time -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                                <h2 class="text-base font-bold text-slate-900">{{ __('rooms.choose_start_time') }}</h2>
                            </div>
                            <span x-show="loadingSlots" class="text-xs text-indigo-600 animate-pulse font-medium">Updating slots...</span>
                        </div>

                        <input type="hidden" name="start_time" :value="selectedStartTime" required>

                        <!-- Time Slots Grid -->
                        <template x-if="!isClosed && slots.length > 0">
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                <template x-for="slot in slots" :key="slot.time">
                                    <button type="button"
                                            @click="selectStart(slot.time)"
                                            :class="selectedStartTime === slot.time
                                                ? 'bg-indigo-600 text-white font-bold border-indigo-600 ring-2 ring-indigo-200 shadow-sm'
                                                : 'bg-slate-50 text-slate-800 hover:bg-indigo-50 hover:text-indigo-700 border-slate-200'"
                                            class="py-2.5 px-3 rounded-xl border text-xs font-semibold text-center transition-all">
                                        <span x-text="slot.time"></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="!isClosed && slots.length === 0 && !loadingSlots">
                            <div class="p-6 text-center text-xs text-slate-500 bg-slate-50 rounded-xl border border-slate-200">
                                No available start times remaining on this date. All slots are fully booked or past working hours.
                            </div>
                        </template>
                    </div>

                    <!-- Step 3: Choose Duration (Length) -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs" x-show="selectedStartTime">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h2 class="text-base font-bold text-slate-900">{{ __('rooms.choose_duration') }}</h2>
                        </div>

                        <input type="hidden" name="duration_minutes" :value="selectedDuration" required>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <template x-for="dur in validDurations" :key="dur.minutes">
                                <button type="button"
                                        @click="selectedDuration = dur.minutes"
                                        :class="selectedDuration === dur.minutes
                                            ? 'bg-indigo-600 text-white font-bold border-indigo-600 ring-2 ring-indigo-200 shadow-sm'
                                            : 'bg-slate-50 text-slate-800 hover:bg-indigo-50 hover:text-indigo-700 border-slate-200'"
                                        class="p-3 rounded-xl border text-left transition-all">
                                    <div class="text-xs font-bold" x-text="dur.minutes + ' min'"></div>
                                    <div class="text-[10px] opacity-80" x-text="'until ' + dur.end_time"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Step 4: Meeting & Organizer Details -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                            <h2 class="text-base font-bold text-slate-900">{{ __('rooms.meeting_details') }}</h2>
                        </div>

                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">
                                    {{ __('rooms.title') }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" x-model="title" required minlength="3" maxlength="120"
                                       placeholder="e.g. Mobile Architecture Sprint Review"
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                            </div>

                            <!-- Agenda -->
                            <div>
                                <label for="agenda" class="block text-xs font-semibold text-slate-700 mb-1">
                                    {{ __('rooms.agenda') }}
                                </label>
                                <textarea name="agenda" id="agenda" x-model="agenda" rows="3" maxlength="1000"
                                          placeholder="Key discussion topics or preparation notes for attendees..."
                                          class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden"></textarea>
                            </div>

                            <!-- Name, Email, Attendees -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label for="organizer_name" class="block text-xs font-semibold text-slate-700 mb-1">
                                        {{ __('rooms.organizer_name') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="organizer_name" id="organizer_name" required maxlength="100"
                                           value="{{ old('organizer_name', $prefilledName) }}"
                                           placeholder="Full Name"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                                </div>

                                <div>
                                    <label for="organizer_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                        {{ __('rooms.organizer_email') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="email" name="organizer_email" id="organizer_email" required maxlength="150"
                                           value="{{ old('organizer_email', $prefilledEmail) }}"
                                           placeholder="name@company.com"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                                </div>

                                <div>
                                    <label for="attendees_count" class="block text-xs font-semibold text-slate-700 mb-1">
                                        {{ __('rooms.attendees_count') }} (Max {{ $room->capacity }}) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" name="attendees_count" id="attendees_count" required
                                           min="1" max="{{ $room->capacity }}"
                                           x-model="selectedAttendees"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <span class="text-xs text-slate-400">
                                Protected by atomic concurrency locking (zero double-bookings)
                            </span>
                            <button type="submit"
                                    :disabled="!selectedStartTime || !selectedDuration || isClosed"
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed transition-colors shadow-sm">
                                {{ __('rooms.confirm_booking') }} &rarr;
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right: Sticky Live Booking Summary Card (4 Cols) -->
            <div class="lg:col-span-4 sticky top-24">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-3">
                        Reservation Summary
                    </h3>

                    <!-- Room Mini-Card -->
                    <div class="flex items-center gap-3">
                        @if($room->photoUrl())
                            <img src="{{ $room->photoUrl() }}" alt="{{ $room->localizedName() }}" class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-200 shadow-xs">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center shrink-0">
                                {{ substr($room->name_en, 0, 1) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-slate-900 truncate">{{ $room->localizedName() }}</div>
                            <div class="text-xs text-slate-500 truncate">{{ $room->location }}</div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-500">Date:</span>
                            <span class="font-semibold text-slate-800" x-text="selectedDate"></span>
                        </div>
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-500">Start Time:</span>
                            <span class="font-semibold text-slate-800" x-text="selectedStartTime || 'Not selected'"></span>
                        </div>
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-500">Duration:</span>
                            <span class="font-semibold text-slate-800" x-text="selectedDuration ? (selectedDuration + ' min') : 'Not selected'"></span>
                        </div>
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-500">Attendees:</span>
                            <span class="font-semibold text-slate-800" x-text="selectedAttendees + ' / ' + {{ $room->capacity }} + ' max'"></span>
                        </div>
                    </div>

                    <!-- Rule Badges -->
                    <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 space-y-1">
                        <div class="flex items-center gap-1.5 text-emerald-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Back-to-back booking allowed</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-emerald-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Instant .ics & Google Cal links</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-indigo-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Self-service cancel until start time</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
