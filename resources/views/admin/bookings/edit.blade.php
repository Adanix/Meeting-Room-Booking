<x-layouts.admin :title="__('rooms.admin.edit_booking') . ' ' . $booking->reference_code">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('rooms.common.back') }}
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('rooms.admin.edit_booking') }}</h1>
                    <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                        {{ $booking->reference_code }}
                    </span>
                    @if ($booking->isConfirmed())
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            {{ __('rooms.admin.confirmed') }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                            {{ __('rooms.admin.cancelled') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Cancel / Restore Actions -->
            <div class="flex items-center gap-2">
                @if ($booking->isConfirmed())
                    <button type="button" @click="$dispatch('open-cancel-modal')" class="px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition">
                        {{ __('rooms.admin.cancel_booking') }}
                    </button>
                @else
                    <form action="{{ route('admin.bookings.restore', $booking) }}" method="POST" onsubmit="return confirm('Restore this booking?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition">
                            {{ __('rooms.admin.restore_booking') }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('bookings.confirmation', $booking->reference_code) }}" target="_blank" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Public View
                </a>
            </div>
        </div>

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

        <!-- Edit Form -->
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Room -->
                <div class="md:col-span-2">
                    <label for="room_id" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.room') }} *</label>
                    <select id="room_id" name="room_id" required class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->localized_name }} (Cap: {{ $room->capacity }}, {{ $room->location }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label for="booking_date" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.date') }} *</label>
                    <input type="date" id="booking_date" name="booking_date" value="{{ old('booking_date', $booking->booking_date->toDateString()) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Start Time -->
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.start_time') }} (HH:MM) *</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $booking->start_at->format('H:i')) }}" step="900" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.duration') }} *</label>
                    <select id="duration_minutes" name="duration_minutes" required class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        @php $currentDur = $booking->start_at->diffInMinutes($booking->end_at); @endphp
                        <option value="15" {{ old('duration_minutes', $currentDur) == 15 ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('duration_minutes', $currentDur) == 30 ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ old('duration_minutes', $currentDur) == 45 ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ old('duration_minutes', $currentDur) == 60 ? 'selected' : '' }}>1 hour</option>
                        <option value="90" {{ old('duration_minutes', $currentDur) == 90 ? 'selected' : '' }}>1.5 hours</option>
                        <option value="120" {{ old('duration_minutes', $currentDur) == 120 ? 'selected' : '' }}>2 hours</option>
                        <option value="180" {{ old('duration_minutes', $currentDur) == 180 ? 'selected' : '' }}>3 hours</option>
                        <option value="240" {{ old('duration_minutes', $currentDur) == 240 ? 'selected' : '' }}>4 hours</option>
                    </select>
                </div>

                <!-- Attendees -->
                <div>
                    <label for="attendees_count" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.attendees') }} *</label>
                    <input type="number" id="attendees_count" name="attendees_count" value="{{ old('attendees_count', $booking->attendees_count) }}" min="1" max="100" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.meeting_title') }} *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $booking->title) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Organizer Name -->
                <div>
                    <label for="organizer_name" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.organizer_name') }} *</label>
                    <input type="text" id="organizer_name" name="organizer_name" value="{{ old('organizer_name', $booking->organizer_name) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Organizer Email -->
                <div>
                    <label for="organizer_email" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.organizer_email') }} *</label>
                    <input type="email" id="organizer_email" name="organizer_email" value="{{ old('organizer_email', $booking->organizer_email) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Agenda -->
                <div class="md:col-span-2">
                    <label for="agenda" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.agenda') }}</label>
                    <textarea id="agenda" name="agenda" rows="3"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('agenda', $booking->agenda) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    {{ __('rooms.common.cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                    {{ __('rooms.common.save') }}
                </button>
            </div>
        </form>

        <!-- Status History Timeline -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ __('rooms.admin.history') }}</h2>
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse ($booking->statusHistories as $idx => $history)
                        <li>
                            <div class="relative pb-8">
                                @if (! $loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                            @if ($history->action === 'created') bg-emerald-100 text-emerald-600
                                            @elseif ($history->action === 'rescheduled') bg-amber-100 text-amber-600
                                            @elseif ($history->action === 'cancelled') bg-rose-100 text-rose-600
                                            @else bg-blue-100 text-blue-600 @endif">
                                            @if ($history->action === 'created')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            @elseif ($history->action === 'cancelled')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 flex justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">
                                                <span class="capitalize">{{ $history->action }}</span>
                                                <span class="font-normal text-slate-500">by {{ $history->actorUser?->name ?? 'Self-Service Employee' }}</span>
                                            </p>
                                            @if ($history->notes)
                                                <p class="text-xs text-slate-600 mt-0.5 bg-slate-50 p-2 rounded-lg border border-slate-100 font-mono">{{ $history->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-slate-400">
                                            {{ $history->created_at->format('M j, Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500 italic">No history records logged yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Cancel Modal via Alpine -->
        <div x-data="{ open: false }"
             @open-cancel-modal.window="open = true"
             x-show="open"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="open = false"></div>
                <div class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                        @csrf
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ __('rooms.admin.cancel_booking') }}</h3>
                        <p class="text-sm text-slate-500 mb-4">Are you sure you want to cancel booking <strong>{{ $booking->reference_code }}</strong>? Notification emails will be simulated to organizer and office.</p>
                        <div class="mb-4">
                            <label for="modal_reason" class="block text-xs font-semibold text-slate-700 mb-1">Cancellation Reason (optional)</label>
                            <input type="text" id="modal_reason" name="reason" placeholder="e.g. Facilities maintenance or emergency schedule shift"
                                   class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" @click="open = false" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-xl border border-slate-200">
                                {{ __('rooms.common.cancel') }}
                            </button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition">
                                Confirm Cancellation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
