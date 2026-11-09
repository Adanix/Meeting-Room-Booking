<x-layouts.marketing>
    <x-slot:title>Documentation — ROOMS Enterprise Meeting Room Booking System</x-slot:title>

    <div class="py-10 sm:py-14 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wider mb-3">
                    Product Documentation & Guide
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    ROOMS Documentation & Workflow Specification
                </h1>
                <p class="mt-3 text-sm sm:text-base text-slate-600 leading-relaxed">
                    Complete functional guide covering the live room status board, conflict-free scheduling engine, self-service cancellation links, calendar integrations, and administrative controls.
                </p>
            </div>
        </div>
    </div>

    <!-- Docs Body with Sticky Sidebar on Desktop -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ activeSection: 'overview' }">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            <!-- Sidebar Table of Contents -->
            <aside class="lg:w-64 shrink-0">
                <div class="sticky top-24 space-y-1 text-xs font-semibold">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">Sections</p>
                    <a href="#overview" @click="activeSection = 'overview'"
                       :class="activeSection === 'overview' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                       class="block px-3 py-2 rounded-lg transition-colors">
                        1. Product Overview & Purpose
                    </a>
                    <a href="#personas" @click="activeSection = 'personas'"
                       :class="activeSection === 'personas' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                       class="block px-3 py-2 rounded-lg transition-colors">
                        2. Demo Personas & Roles
                    </a>
                    <a href="#modules" @click="activeSection = 'modules'"
                       :class="activeSection === 'modules' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                       class="block px-3 py-2 rounded-lg transition-colors">
                        3. Complete Module Guide
                    </a>
                    <a href="#rules" @click="activeSection = 'rules'"
                       :class="activeSection === 'rules' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                       class="block px-3 py-2 rounded-lg transition-colors">
                        4. Scheduling & Conflict Math
                    </a>
                    <a href="#simulations" @click="activeSection = 'simulations'"
                       :class="activeSection === 'simulations' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                       class="block px-3 py-2 rounded-lg transition-colors">
                        5. Simulated Email & Calendar
                    </a>
                    <a href="#customization" @click="activeSection = 'customization'"
                       :class="activeSection === 'customization' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                       class="block px-3 py-2 rounded-lg transition-colors">
                        6. Customization & Contact
                    </a>
                </div>
            </aside>

            <!-- Main Docs Content -->
            <article class="flex-1 min-w-0 prose prose-slate max-w-none text-slate-700">

                <!-- Section 1 -->
                <section id="overview" class="mb-12 scroll-mt-24">
                    <h2 class="text-2xl font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4">
                        1. Product Overview & Purpose
                    </h2>
                    <p class="text-sm leading-relaxed mb-4">
                        <strong>ROOMS</strong> is an enterprise meeting room scheduling web application built on <strong>Laravel 13</strong>, <strong>PHP 8.5</strong>, <strong>Tailwind CSS v4</strong>, and <strong>Alpine.js</strong>.
                        It is engineered specifically to eliminate double-bookings, avoid mobile horizontal overflow, and provide a self-service experience where employees can reserve rooms in under 30 seconds without requiring external paid API keys.
                    </p>
                    <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-600">
                        <strong>Quick Links:</strong>
                        <a href="{{ route('demo.login') }}" class="text-indigo-600 font-semibold underline ml-2">Open Demo Persona Selector (/demo/login)</a> &bull;
                        <a href="{{ route('rooms.index') }}" class="text-indigo-600 font-semibold underline ml-1">Live Employee Room Portal (/rooms)</a> &bull;
                        <a href="{{ route('schedule.index') }}" class="text-indigo-600 font-semibold underline ml-1">Daily Schedule Timeline (/schedule)</a>
                    </div>
                </section>

                <!-- Section 2 -->
                <section id="personas" class="mb-12 scroll-mt-24">
                    <h2 class="text-2xl font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4">
                        2. Demo Personas & Roles
                    </h2>
                    <p class="text-sm leading-relaxed mb-4">
                        In demo mode, three distinct personas are provided with instant one-click login so evaluators can explore each perspective without manual credential typing:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 not-prose mb-6">
                        <div class="border border-slate-200 rounded-xl p-4 bg-white shadow-xs">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 uppercase mb-2">admin</span>
                            <h4 class="text-sm font-bold text-slate-900">Nikola Jovanović</h4>
                            <p class="text-xs text-slate-500 mb-3">Facilities Administrator</p>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Full administrative access: dashboard KPIs, room CRUD & photo uploads, working hours, holiday management, booking editing/restoration, and CSV export.
                            </p>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-4 bg-white shadow-xs">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 uppercase mb-2">coordinator</span>
                            <h4 class="text-sm font-bold text-slate-900">Ana Marković</h4>
                            <p class="text-xs text-slate-500 mb-3">Office Coordinator</p>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Front-desk coordination: manages the daily schedule board, cancels or edits bookings on behalf of employees, exports CSV reports, and inspects office notification emails.
                            </p>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-4 bg-white shadow-xs">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 uppercase mb-2">employee</span>
                            <h4 class="text-sm font-bold text-slate-900">Marko Petrović</h4>
                            <p class="text-xs text-slate-500 mb-3">Company Employee</p>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Standard employee: browses live room status, books meetings with pre-filled name/email, downloads .ics calendar invites, and manages bookings via email magic links.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Section 3 -->
                <section id="modules" class="mb-12 scroll-mt-24">
                    <h2 class="text-2xl font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4">
                        3. Complete Module Guide
                    </h2>

                    <div class="space-y-6 text-sm">
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 1 — Live Room Portal (/rooms)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                Displays real-time status cards (<strong>Free</strong>, <strong>Busy</strong>, <strong>Closed</strong>) dynamically calculated against working hours, holiday calendar, and active meetings.
                                Filter by room, date, minimum capacity, and equipment tags. Hidden rooms (<code>is_active = false</code>) are excluded from public views.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 2 — Step-by-Step Booking Form (/rooms/{room}/book)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                Follows the 4-step selection: <strong>Room &rarr; Date &rarr; Free Start Time &rarr; Duration</strong>.
                                Only valid, conflict-free time slots and durations are offered. Duration automatically caps at the beginning of the next confirmed booking, permitting back-to-back meetings seamlessly.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 3 — Confirmation, Google Calendar & .ics</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                On submission, generates a unique reference code (<code>RM-YYYYMMDD-XXXX</code>) and provides immediate one-click calendar actions:
                                "Add to Google Calendar" (pure URL template, zero API keys) and "Download .ics" (standards-compliant RFC 5545 attachment for Outlook / Apple Calendar).
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 4 — Self-Service Move or Cancel (/bookings/manage/{token})</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                Every booking generates a 48-character cryptographic token embedded in confirmation emails.
                                Organizers can move the booking to another available time or cancel it until the meeting starts.
                                Once the meeting start time has arrived (<code>now &ge; start_at</code>), modifications are strictly locked.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 5 — Daily All-Rooms Schedule (/schedule)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                Single unified schedule board displaying all rooms and their confirmed meetings for any selected day.
                                On mobile viewports (&lt;768px), it defaults to clean stacked room cards, preventing horizontal page overflow.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 6 — Bilingual Localization (EN / SR)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                Instant switching between English and Serbian Latin (<code>sr_Latn</code>) with localized date formats, UI strings, and bilingual email notifications.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 mb-2">Module 7 — Facilities Admin Panel (/admin/*)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                Comprehensive administration: dashboard metrics, filterable booking ledger, monthly calendar view, booking editing/restoration, room CRUD with safe photo upload, working hours configuration, holiday list, and UTF-8 BOM Excel CSV export.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Section 4 -->
                <section id="rules" class="mb-12 scroll-mt-24">
                    <h2 class="text-2xl font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4">
                        4. Scheduling & Conflict Math
                    </h2>
                    <p class="text-sm leading-relaxed mb-3">
                        Every reservation interval is treated as a half-open interval <code>[start_at, end_at)</code> (inclusive of start, exclusive of end).
                        Two bookings A and B overlap if and only if:
                    </p>
                    <div class="p-3 bg-slate-900 text-slate-100 rounded-lg font-mono text-xs mb-3">
                        A.start_at &lt; B.end_at AND A.end_at &gt; B.start_at
                    </div>
                    <ul class="text-xs text-slate-600 space-y-1 mb-4">
                        <li>&bull; <strong>Back-to-Back Allowed:</strong> If Meeting 1 ends at 11:00 and Meeting 2 starts at 11:00, <code>11:00 &gt; 11:00</code> is False. Zero conflict!</li>
                        <li>&bull; <strong>Concurrency Lock:</strong> Requests run inside a database transaction with <code>Room::lockForUpdate()</code>, guaranteeing serial execution at the row-lock level so two simultaneous requests can never double-book a room.</li>
                    </ul>
                </section>

                <!-- Section 5 -->
                <section id="simulations" class="mb-12 scroll-mt-24">
                    <h2 class="text-2xl font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4">
                        5. Simulated Email & Calendar Disclosures
                    </h2>
                    <p class="text-sm leading-relaxed mb-3">
                        Per client specifications and zero-cost constraints:
                    </p>
                    <ul class="text-xs text-slate-600 space-y-2 mb-4">
                        <li>&bull; <strong>No Paid Google Cloud or Microsoft Graph APIs:</strong> Calendar integration uses standard parameterized deep links and RFC 5545 .ics generation.</li>
                        <li>&bull; <strong>Simulated Email Outbox:</strong> Instead of relying on external SMTP relays that may fail or be rate-limited in sandboxes, outgoing confirmation and cancellation emails are rendered and saved in the <code>simulated_emails</code> table. Evaluators can click the top demo bar's <strong>Email Outbox</strong> button anytime to inspect real rendered emails and test magic links.</li>
                    </ul>
                </section>

                <!-- Section 6: Mandatory Customization Notice -->
                <section id="customization" class="mb-12 scroll-mt-24">
                    <div class="p-6 sm:p-8 rounded-2xl bg-indigo-50 border-2 border-indigo-200 shadow-sm not-prose">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                            <h3 class="text-lg font-black text-slate-900">
                                {{ __('Need something more tailored?') }}
                            </h3>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed mb-5">
                            {{ __('This demo showcases the current application features and workflows. The web application can be further developed, extended, and customized to fit your business processes and specific requirements. Features, workflows, interface, and integrations can be discussed and tailored to your needs.') }}
                        </p>
                        <div class="pt-4 border-t border-indigo-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-sm">
                            <div class="font-semibold text-slate-900">
                                {{ __('Contact:') }}
                                <a href="mailto:nixresponding@gmail.com" class="text-indigo-600 hover:text-indigo-800 underline font-bold ml-1">
                                    nixresponding@gmail.com
                                </a>
                            </div>
                            <a href="mailto:nixresponding@gmail.com"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition-colors shadow-xs">
                                <span>Send an Email</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </section>
            </article>
        </div>
    </div>
</x-layouts.marketing>
