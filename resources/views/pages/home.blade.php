<x-layouts.marketing>
    <x-slot:title>ROOMS — Enterprise Meeting Room Booking & Workspace Scheduling</x-slot:title>

    <!-- Hero Section -->
    <section class="py-12 sm:py-20 bg-gradient-to-b from-white to-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 border border-indigo-200 text-indigo-700 mb-6 flex-wrap justify-center">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    <span>{{ __('Bilingual (EN / SR) • Zero Paid APIs • Conflict-Free Booking Engine') }}</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-slate-900 leading-tight">
                    {{ __('Book Company Meeting Rooms in Seconds—Without Double Bookings.') }}
                </h1>

                <p class="mt-5 text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                    {{ __('Check real-time room availability, reserve slots that fit your team, add meetings to Google Calendar or Outlook in one click, and manage changes via self-service email links.') }}
                </p>

                <!-- CTA Button Group -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('demo.login') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition-all hover:-translate-y-0.5">
                        <span>{{ __('Try Demo with Instant Personas') }}</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <a href="{{ route('rooms.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl shadow-xs transition-colors">
                        <span>{{ __('Open Live Booking Portal') }}</span>
                    </a>

                    <a href="{{ route('public.docs') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                        <span>{{ __('Read Feature Guide') }} &rarr;</span>
                    </a>
                </div>

                <!-- Hero Workspace Visual Mockup Banner -->
                <div class="mt-12 max-w-5xl mx-auto">
                    <div class="relative rounded-3xl overflow-hidden border border-slate-200/80 shadow-2xl bg-slate-900 group">
                        <!-- Browser Window Top Bar Chrome -->
                        <div class="h-10 bg-slate-900/90 border-b border-slate-800 px-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                                <span class="ml-2 text-[11px] font-mono text-slate-400">rooms.enterprise.local/rooms</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                    Enterprise Scheduling Engine
                                </span>
                            </div>
                        </div>

                        <!-- Hero Image & Interactive Overlay -->
                        <div class="relative h-64 sm:h-96 md:h-[420px] overflow-hidden">
                            <img src="{{ asset('images/hero-workspace.jpg') }}" alt="ROOMS Enterprise Workspace Atrium"
                                 class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-slate-950/20"></div>

                            <!-- Floating Live UI Cards inside Hero -->
                            <div class="absolute inset-x-4 bottom-6 sm:inset-x-8 sm:bottom-8 flex flex-col sm:flex-row items-end sm:items-center justify-between gap-4">
                                <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 sm:p-5 shadow-lg border border-white/40 max-w-md text-left">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">6 Spaces Live & Synchronized</span>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-black text-slate-900">
                                        Frictionless Meeting Room Scheduling
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-1">
                                        Self-service reservations with Google Calendar & Outlook .ics sync, strict overlap prevention, and bilingual support.
                                    </p>
                                </div>

                                <div class="hidden md:flex flex-col gap-2 shrink-0">
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/90 backdrop-blur-md border border-slate-700 text-white text-xs font-semibold shadow-md">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span>Nikola Tesla Boardroom &bull; Available</span>
                                    </div>
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/90 backdrop-blur-md border border-slate-700 text-white text-xs font-semibold shadow-md">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        <span>Pupin Lab &bull; In Use until 14:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Room Status Preview Widget (3 Real Rooms with Photos) -->
            <div class="mt-16 max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Live Workspace Status</h2>
                        <p class="text-xs text-slate-400">Real-time availability preview calculated from server clock and active bookings</p>
                    </div>
                    <a href="{{ route('rooms.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        View All {{ $totalActiveRooms }} Rooms &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($roomsWithStatus as $item)
                        @php
                            $room = $item['room'];
                            $status = $item['status'];
                            $photo = $room->photoUrl();
                        @endphp
                        <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <!-- Room Photo Header with Overlay Badges -->
                                <div class="relative w-full aspect-video bg-slate-900 overflow-hidden">
                                    @if($photo)
                                        <img src="{{ $photo }}" alt="{{ $room->localizedName() }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent pointer-events-none"></div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white">
                                            <span class="text-xs font-bold uppercase">{{ $room->localizedName() }}</span>
                                        </div>
                                    @endif

                                    <!-- Floating Live Status Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border backdrop-blur-md bg-white/95 {{ $status['badge_class'] }} shadow-xs">
                                            <span class="w-2 h-2 rounded-full {{ $status['dot_class'] }}"></span>
                                            <span>{{ $status['label'] }}</span>
                                        </span>
                                    </div>

                                    <!-- Capacity Floating Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-900/80 text-white backdrop-blur-xs">
                                            {{ $room->capacity }} ppl
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <h3 class="text-base font-bold text-slate-900 truncate group-hover:text-indigo-600 transition-colors">
                                        {{ $room->localizedName() }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                                        {{ $room->location }}
                                    </p>
                                    <p class="text-xs font-medium text-slate-600 mt-2">
                                        {{ $status['subtitle'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-1 text-[11px] text-slate-400 truncate max-w-[150px]">
                                    @foreach($room->equipments->take(2) as $eq)
                                        <span class="truncate">{{ $eq->localizedLabel() }}@if(!$loop->last),@endif</span>
                                    @endforeach
                                </div>
                                <a href="{{ route('rooms.book', $room->slug) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors shrink-0">
                                    <span>{{ __('rooms.book_now') }}</span>
                                    &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Core Capability Grid (6 Cards Mapped to PRD) -->
    <section class="py-16 sm:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Engineered for Frictionless Workspace Scheduling
                </h2>
                <p class="mt-3 text-sm text-slate-600">
                    Eliminating spreadsheet confusion, double-booked conference rooms, and mobile layout overflow.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Live Room Status & Quick Search</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Real-time Free, Busy, and Closed indicators with capacity filters, room photos, equipment tags, and next available slot preview.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Guided 4-Step Booking Engine</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Choose Room &rarr; Date &rarr; Free Start Time &rarr; Duration. Supports back-to-back meetings while strictly blocking overlaps via InnoDB locking.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Zero-API Calendar Export</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Direct "Add to Google Calendar" links alongside downloadable RFC 5545 .ics files for Outlook and Apple Calendar—zero paid keys required.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Self-Service Move & Cancel</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Cryptographically signed magic link in every confirmation email allows employees to reschedule or cancel before meeting start time.
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Admin Panel & Excel Export</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Monthly calendar view, filterable booking ledger, one-click restore for cancelled bookings, and Excel-ready UTF-8 BOM CSV exports.
                    </p>
                </div>

                <!-- Card 6 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Full Bilingual (EN / SR)</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Instant switching between English and Serbian Latin across all public screens, admin management, validation messages, and generated emails.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4-Step How It Works -->
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    How Room Booking Works in ROOMS
                </h2>
                <p class="mt-3 text-sm text-slate-600">
                    A streamlined workflow designed to reserve a workspace in under 30 seconds.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center mb-3">1</div>
                    <h4 class="text-sm font-bold text-slate-900">Check Room Availability</h4>
                    <p class="mt-1 text-xs text-slate-500">Inspect real-time Free, Busy, and Closed indicators on /rooms or view the daily schedule grid.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center mb-3">2</div>
                    <h4 class="text-sm font-bold text-slate-900">Select Date & Time</h4>
                    <p class="mt-1 text-xs text-slate-500">Pick an available start time and duration that respects working hours, holidays, and room capacity.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center mb-3">3</div>
                    <h4 class="text-sm font-bold text-slate-900">Receive Confirmation</h4>
                    <p class="mt-1 text-xs text-slate-500">Get your reference number, add to Google Calendar, download .ics, and review simulated emails.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center mb-3">4</div>
                    <h4 class="text-sm font-bold text-slate-900">Self-Service Manage</h4>
                    <p class="mt-1 text-xs text-slate-500">Use the email magic link to move or cancel your meeting anytime until the scheduled start time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Transparency Notice -->
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-6 sm:p-8 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-600 text-white uppercase tracking-wider">Demo Transparency</span>
                        <h3 class="text-base font-bold text-slate-900">Live Database • Simulated Outbox</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        This is a fully functional database-backed demo. Bookings, concurrency rules, capacity checks, and .ics files are 100% operational. Outgoing emails are captured in the in-app Simulated Email Outbox so you can test magic links without configuring external SMTP relays.
                    </p>
                </div>
                <div class="shrink-0 flex flex-wrap gap-2 w-full sm:w-auto">
                    <a href="{{ route('demo.login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs">
                        Launch Demo &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.marketing>
