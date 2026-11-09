<x-layouts.app>
    <x-slot:title>Booking Confirmed — {{ $booking->reference_code }}</x-slot:title>

    <div class="max-w-3xl mx-auto py-6 sm:py-10">
        <!-- Success Banner -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ __('rooms.status_confirmed') }}</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ __('rooms.booking_confirmed') }}
            </h1>

            <div class="mt-3 inline-block bg-slate-100 border border-slate-200 px-4 py-1.5 rounded-xl font-mono text-xs sm:text-sm font-bold text-slate-800 tracking-wider">
                {{ $booking->reference_code }}
            </div>

            <p class="mt-4 text-xs sm:text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
                A confirmation email has been dispatched to <strong>{{ $booking->organizer_email }}</strong> and the facilities office.
            </p>

            <!-- Calendar Actions (flex flex-wrap gap-3) -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                <a href="{{ $googleCalUrl }}" target="_blank" rel="noopener"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                    </svg>
                    <span>{{ __('rooms.google_calendar') }}</span>
                </a>

                <a href="{{ route('bookings.ics', $booking->reference_code) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>{{ __('rooms.download_ics') }}</span>
                </a>
            </div>
        </div>

        <!-- Meeting Summary Details Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4 mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">
                Meeting Details
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 block">Meeting Title:</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $booking->title }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Room & Location:</span>
                    <span class="font-semibold text-slate-900">
                        {{ $booking->room ? $booking->room->localizedName() : 'Room' }}
                        <span class="text-slate-500 font-normal">({{ $booking->room ? $booking->room->location : '' }})</span>
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Date & Time:</span>
                    <span class="font-semibold text-slate-900">
                        {{ $booking->booking_date->format('l, d M Y') }} &bull; {{ $booking->timeRangeString() }} ({{ $booking->duration_minutes }} min)
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Attendees:</span>
                    <span class="font-semibold text-slate-900">{{ $booking->attendees_count }} people</span>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-slate-500 block">Organizer:</span>
                    <span class="font-semibold text-slate-900">{{ $booking->organizer_name }} &lt;{{ $booking->organizer_email }}&gt;</span>
                </div>
                @if($booking->agenda)
                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <span class="text-slate-500 block mb-1">Agenda / Notes:</span>
                        <p class="text-slate-700 bg-slate-50 p-3 rounded-lg leading-relaxed">{{ $booking->agenda }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Self-Service & Demo Transparency Box -->
        <div class="bg-indigo-50/70 rounded-2xl border border-indigo-100 p-6 space-y-4">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">
                        {{ __('rooms.self_service_link') }}
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">
                        {{ __('rooms.self_service_help') }}
                    </p>
                    <a href="{{ route('bookings.manage', $booking->management_token) }}"
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 break-all underline">
                        {{ route('bookings.manage', $booking->management_token) }}
                    </a>
                </div>
                <button type="button" @click="$dispatch('open-outbox')"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50 shadow-xs shrink-0">
                    {{ __('rooms.open_email_outbox') }} &rarr;
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
