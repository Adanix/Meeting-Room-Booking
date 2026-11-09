<x-layouts.app>
    <x-slot:title>{{ __('rooms.manage_title') }} — {{ $booking->reference_code }}</x-slot:title>

    <div class="max-w-4xl mx-auto py-6 sm:py-10" x-data="{
        cancelModalOpen: false,
        selectedDate: '{{ $booking->booking_date->toDateString() }}',
        selectedStartTime: '{{ $booking->start_at->format('H:i') }}',
        selectedDuration: {{ $booking->duration_minutes }},
        selectedRoomId: {{ $booking->room_id }},
        slots: {{ Js::from($slotData['slots'] ?? []) }},
        loadingSlots: false,

        async fetchSlots() {
            this.loadingSlots = true;
            try {
                const res = await fetch(`/api/rooms/${this.selectedRoomId}/available-slots?date=${this.selectedDate}&exclude_booking_id={{ $booking->id }}`);
                const data = await res.json();
                this.slots = data.slots || [];
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

        <!-- Header -->
        <header class="w-full mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
                <div class="min-w-0 flex-1">
                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold font-mono bg-slate-100 text-slate-700 mb-1">
                        {{ $booking->reference_code }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                        {{ __('rooms.manage_title') }}
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600 truncate">
                        {{ $booking->title }} &bull; {{ $booking->room ? $booking->room->localizedName() : 'Room' }}
                    </p>
                </div>

                @if($booking->isConfirmed() && $canManage)
                    <button type="button" @click="cancelModalOpen = true"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition-colors shrink-0">
                        {{ __('rooms.cancel_title') }}
                    </button>
                @endif
            </div>
        </header>

        <!-- Cutoff / Lock Notice -->
        @if(!$canManage)
            <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm leading-relaxed">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Modifications Locked</span>
                </div>
                <p>{{ __('rooms.locked_notice') }}</p>
                @if($booking->isCancelled())
                    <p class="mt-2 text-rose-700 font-semibold">
                        This reservation was cancelled on {{ $booking->cancelled_at ? $booking->cancelled_at->format('d M Y, H:i') : '' }}.
                        @if($booking->cancellation_reason)
                            (Reason: {{ $booking->cancellation_reason }})
                        @endif
                    </p>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: Current Booking Info (4 Cols) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                        Current Schedule
                    </h3>
                    <div class="text-xs space-y-2">
                        <div>
                            <span class="text-slate-500 block">Status:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $booking->isConfirmed() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Room:</span>
                            <span class="font-bold text-slate-900">{{ $booking->room ? $booking->room->localizedName() : '' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Date:</span>
                            <span class="font-semibold text-slate-800">{{ $booking->booking_date->format('l, d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Time:</span>
                            <span class="font-semibold text-slate-800">{{ $booking->timeRangeString() }} ({{ $booking->duration_minutes }} min)</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Attendees:</span>
                            <span class="font-semibold text-slate-800">{{ $booking->attendees_count }} people</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                        <a href="{{ $googleCalUrl }}" target="_blank" rel="noopener"
                           class="w-full text-center py-2 px-3 rounded-lg text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                            {{ __('rooms.google_calendar') }}
                        </a>
                        <a href="{{ route('bookings.ics', $booking->reference_code) }}"
                           class="w-full text-center py-2 px-3 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                            {{ __('rooms.download_ics') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Reschedule Form (8 Cols) -->
            <div class="lg:col-span-8">
                @if($canManage)
                    <form method="POST" action="{{ route('bookings.manage.update', $booking->management_token) }}"
                          class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-5">
                        @csrf
                        @method('PUT')

                        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                            {{ __('rooms.reschedule_title') }}
                        </h2>

                        <!-- Room Selector -->
                        <div>
                            <label for="room_id" class="block text-xs font-semibold text-slate-700 mb-1">Target Room</label>
                            <select name="room_id" id="room_id" x-model="selectedRoomId" @change="fetchSlots()"
                                    class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                                @foreach($allRooms as $r)
                                    <option value="{{ $r->id }}" {{ $r->id === $booking->room_id ? 'selected' : '' }}>
                                        {{ $r->localizedName() }} (Capacity: {{ $r->capacity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Picker -->
                        <div>
                            <label for="booking_date" class="block text-xs font-semibold text-slate-700 mb-1">New Date</label>
                            <input type="date" name="booking_date" id="booking_date"
                                   x-model="selectedDate"
                                   @change="fetchSlots()"
                                   min="{{ $todayDate }}" max="{{ $maxDate }}" required
                                   class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                        </div>

                        <!-- Start Time Grid -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('rooms.choose_start_time') }} <span x-show="loadingSlots" class="text-indigo-600 text-[10px] animate-pulse">(recalculating...)</span>
                            </label>
                            <input type="hidden" name="start_time" :value="selectedStartTime" required>

                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 mt-2">
                                <template x-for="slot in slots" :key="slot.time">
                                    <button type="button"
                                            @click="selectStart(slot.time)"
                                            :class="selectedStartTime === slot.time
                                                ? 'bg-indigo-600 text-white font-bold border-indigo-600 ring-2 ring-indigo-200'
                                                : 'bg-slate-50 text-slate-800 hover:bg-indigo-50 border-slate-200'"
                                            class="py-2 px-2.5 rounded-xl border text-xs font-semibold text-center transition-all">
                                        <span x-text="slot.time"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Duration Selector -->
                        <div x-show="selectedStartTime">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">{{ __('rooms.choose_duration') }}</label>
                            <input type="hidden" name="duration_minutes" :value="selectedDuration" required>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                                <template x-for="dur in validDurations" :key="dur.minutes">
                                    <button type="button"
                                            @click="selectedDuration = dur.minutes"
                                            :class="selectedDuration === dur.minutes
                                                ? 'bg-indigo-600 text-white font-bold border-indigo-600'
                                                : 'bg-slate-50 text-slate-800 hover:bg-indigo-50 border-slate-200'"
                                            class="p-2.5 rounded-xl border text-left transition-all">
                                        <div class="text-xs font-bold" x-text="dur.minutes + ' min'"></div>
                                        <div class="text-[10px] opacity-80" x-text="'until ' + dur.end_time"></div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Title & Organizer Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                            <div>
                                <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">Meeting Title</label>
                                <input type="text" name="title" id="title" required value="{{ old('title', $booking->title) }}"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                            </div>
                            <div>
                                <label for="attendees_count" class="block text-xs font-semibold text-slate-700 mb-1">Number of People</label>
                                <input type="number" name="attendees_count" id="attendees_count" required min="1"
                                       value="{{ old('attendees_count', $booking->attendees_count) }}"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                            </div>
                            <div>
                                <label for="organizer_name" class="block text-xs font-semibold text-slate-700 mb-1">Your Name</label>
                                <input type="text" name="organizer_name" id="organizer_name" required value="{{ old('organizer_name', $booking->organizer_name) }}"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                            </div>
                            <div>
                                <label for="organizer_email" class="block text-xs font-semibold text-slate-700 mb-1">Your Email</label>
                                <input type="email" name="organizer_email" id="organizer_email" required value="{{ old('organizer_email', $booking->organizer_email) }}"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="agenda" class="block text-xs font-semibold text-slate-700 mb-1">Agenda / Description</label>
                                <textarea name="agenda" id="agenda" rows="2"
                                          class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">{{ old('agenda', $booking->agenda) }}</textarea>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit"
                                    :disabled="!selectedStartTime || !selectedDuration"
                                    class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 transition-colors shadow-xs">
                                {{ __('rooms.save_changes') }} &rarr;
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Cancel Confirmation Modal -->
        <div x-show="cancelModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="cancelModalOpen = false"
                 class="w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-slate-900 mb-1">
                    {{ __('rooms.cancel_title') }}
                </h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    {{ __('rooms.cancel_modal_desc') }}
                </p>

                <form method="POST" action="{{ route('bookings.manage.cancel', $booking->management_token) }}" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label for="cancellation_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ __('rooms.cancellation_reason') }}
                        </label>
                        <input type="text" name="cancellation_reason" id="cancellation_reason"
                               placeholder="e.g. Rescheduled to next week / Meeting postponed"
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500 outline-hidden">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="cancelModalOpen = false"
                                class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                            Back
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-xs">
                            {{ __('rooms.confirm_cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
