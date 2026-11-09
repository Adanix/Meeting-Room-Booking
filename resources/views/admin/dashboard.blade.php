<x-layouts.admin>
    <x-slot:title>Admin Dashboard — ROOMS</x-slot:title>

    <!-- Header -->
    <header class="w-full mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                    {{ __('rooms.admin_dashboard') }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 truncate">
                    Real-time workspace utilization overview for {{ date('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('admin.bookings.create') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition-colors">
                    <span>+ New Booking</span>
                </a>
                <a href="{{ route('admin.bookings.export') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs transition-colors">
                    <span>{{ __('rooms.export_csv') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- 4 KPI Cards (Anti-Overflow Responsive Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <!-- KPI 1 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rooms.today_bookings') }}</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $todayBookingsCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rooms.active_rooms') }}</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $activeRoomsCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rooms.busy_now') }}</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $currentlyBusyCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rooms.cancelled_today') }}</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $cancelledTodayCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Today's Upcoming Meetings & Recent Simulated Emails -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <!-- Today's Schedule Column -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Today's Schedule & Upcoming
                </h2>
                <a href="{{ route('admin.bookings.index', ['date_from' => $todayDate, 'date_to' => $todayDate]) }}"
                   class="text-xs font-bold text-indigo-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @if($upcomingToday->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center italic">No upcoming meetings scheduled for the rest of today.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($upcomingToday as $b)
                        <div class="py-3 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-indigo-600">{{ $b->timeRangeString() }}</span>
                                    <span class="text-xs font-bold text-slate-900 truncate">{{ $b->title }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                    {{ $b->room ? $b->room->localizedName() : '' }} &bull; {{ $b->organizer_name }}
                                </div>
                            </div>
                            <a href="{{ route('admin.bookings.edit', $b->id) }}"
                               class="px-2.5 py-1 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg shrink-0">
                                Edit
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Simulated Emails Column -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Recent Dispatched Emails (Outbox)
                </h2>
                <a href="{{ route('admin.emails.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    Full Outbox &rarr;
                </a>
            </div>

            @if($recentEmails->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center italic">No simulated emails recorded yet.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentEmails as $em)
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold uppercase {{ $em->event_type === 'booking_created' ? 'bg-emerald-50 text-emerald-700' : ($em->event_type === 'booking_cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ str_replace('booking_', '', $em->event_type) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-900 truncate">{{ $em->subject }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                    To: {{ $em->recipient_name }} ({{ $em->recipient_email }})
                                </div>
                            </div>
                            <button type="button" @click="$dispatch('open-outbox')"
                                    class="text-[11px] font-semibold text-indigo-600 hover:underline shrink-0">
                                View
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
