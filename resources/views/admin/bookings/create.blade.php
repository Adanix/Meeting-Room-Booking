<x-layouts.admin :title="__('rooms.admin.new_booking')">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('rooms.common.back') }}
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('rooms.admin.new_booking') }}</h1>
                <p class="text-sm text-slate-500 mt-1">Create an internal or employee reservation directly in the facilities schedule.</p>
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

        <form action="{{ route('admin.bookings.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Room -->
                <div class="md:col-span-2">
                    <label for="room_id" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.room') }} *</label>
                    <select id="room_id" name="room_id" required class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        <option value="">-- {{ __('rooms.portal.all_rooms') }} --</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', request('room_id')) == $room->id ? 'selected' : '' }}>
                                {{ $room->localized_name }} (Cap: {{ $room->capacity }}, {{ $room->location }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label for="booking_date" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.date') }} *</label>
                    <input type="date" id="booking_date" name="booking_date" value="{{ old('booking_date', $todayDate) }}" min="{{ $todayDate }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Start Time -->
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.start_time') }} (HH:MM) *</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" step="900" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.duration') }} *</label>
                    <select id="duration_minutes" name="duration_minutes" required class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        <option value="15" {{ old('duration_minutes') == '15' ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('duration_minutes', '30') == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ old('duration_minutes') == '45' ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ old('duration_minutes') == '60' ? 'selected' : '' }}>1 hour</option>
                        <option value="90" {{ old('duration_minutes') == '90' ? 'selected' : '' }}>1.5 hours</option>
                        <option value="120" {{ old('duration_minutes') == '120' ? 'selected' : '' }}>2 hours</option>
                        <option value="180" {{ old('duration_minutes') == '180' ? 'selected' : '' }}>3 hours</option>
                        <option value="240" {{ old('duration_minutes') == '240' ? 'selected' : '' }}>4 hours</option>
                    </select>
                </div>

                <!-- Attendees -->
                <div>
                    <label for="attendees_count" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.attendees') }} *</label>
                    <input type="number" id="attendees_count" name="attendees_count" value="{{ old('attendees_count', 2) }}" min="1" max="100" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.meeting_title') }} *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Q3 Operations Sync" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Organizer Name -->
                <div>
                    <label for="organizer_name" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.organizer_name') }} *</label>
                    <input type="text" id="organizer_name" name="organizer_name" value="{{ old('organizer_name', auth()->user()->name ?? '') }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Organizer Email -->
                <div>
                    <label for="organizer_email" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.organizer_email') }} *</label>
                    <input type="email" id="organizer_email" name="organizer_email" value="{{ old('organizer_email', auth()->user()->email ?? '') }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Agenda -->
                <div class="md:col-span-2">
                    <label for="agenda" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('rooms.portal.agenda') }}</label>
                    <textarea id="agenda" name="agenda" rows="3" placeholder="Brief topics, special equipment requirements..."
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('agenda') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    {{ __('rooms.common.cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                    {{ __('rooms.admin.new_booking') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
