@php
    $emails = \App\Models\SimulatedEmail::orderByDesc('sent_at')->take(15)->get();
@endphp

<div x-data="{ open: false, selectedEmail: null, activeTab: 'list' }"
     @open-outbox.window="open = true"
     @keydown.escape.window="open = false"
     class="relative z-50">

    <!-- Drawer Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;"></div>

    <!-- Drawer Panel -->
    <div x-show="open"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 flex max-w-full pl-6"
         style="display: none;">

        <div class="w-screen max-w-xl bg-white shadow-2xl flex flex-col min-w-0">
            <!-- Header -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-700">
                            Simulated SMTP
                        </span>
                        <h2 class="text-lg font-bold text-slate-900 truncate">
                            {{ __('rooms.outbox') }}
                        </h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Inspect real rendered emails & click self-service management links
                    </p>
                </div>
                <button @click="open = false" type="button"
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200/60 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                @if($emails->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-900">No emails dispatched yet</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">Create a booking on /rooms or trigger a demo action to view simulated emails here.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($emails as $email)
                            @php
                                $badgeColor = match($email->event_type) {
                                    'booking_created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'booking_moved' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'booking_cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'booking_restored' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                $eventLabel = match($email->event_type) {
                                    'booking_created' => 'Confirmed',
                                    'booking_moved' => 'Rescheduled',
                                    'booking_cancelled' => 'Cancelled',
                                    'booking_restored' => 'Restored',
                                    default => $email->event_type,
                                };
                            @endphp
                            <div class="border border-slate-200 rounded-xl p-3.5 hover:border-slate-300 transition-colors bg-white shadow-xs">
                                <div class="flex items-center justify-between gap-2 mb-1.5 flex-wrap">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $badgeColor }}">
                                            {{ $eventLabel }}
                                        </span>
                                        <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">
                                            {{ $email->recipient_type }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-400">
                                        {{ $email->sent_at ? $email->sent_at->format('M d, H:i') : '' }}
                                    </span>
                                </div>

                                <h4 class="text-sm font-bold text-slate-900 truncate">
                                    {{ $email->subject }}
                                </h4>

                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 flex-wrap">
                                    <span class="truncate">To: <strong>{{ $email->recipient_name }}</strong> &lt;{{ $email->recipient_email }}&gt;</span>
                                </div>

                                <!-- Action bar & Magic Links -->
                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex flex-wrap items-center gap-2">
                                    @if($email->manage_url)
                                        <a href="{{ $email->manage_url }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 transition-colors">
                                            <span>Manage / Cancel Link &rarr;</span>
                                        </a>
                                    @endif

                                    @if($email->ics_url)
                                        <a href="{{ $email->ics_url }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 rounded-md hover:bg-slate-200 transition-colors">
                                            <span>.ics</span>
                                        </a>
                                    @endif

                                    @if($email->google_calendar_url)
                                        <a href="{{ $email->google_calendar_url }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 rounded-md hover:bg-slate-200 transition-colors">
                                            <span>Google Cal</span>
                                        </a>
                                    @endif

                                    <button type="button"
                                            @click="selectedEmail = {{ $email->id }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-900 ml-auto">
                                        <span>View HTML &darr;</span>
                                    </button>
                                </div>

                                <!-- Expandable Rendered HTML preview -->
                                <div x-show="selectedEmail === {{ $email->id }}"
                                     x-collapse
                                     class="mt-3 pt-3 border-t border-slate-200"
                                     style="display: none;">
                                    <div class="rounded-lg border border-slate-200 p-3 bg-slate-50/50 max-h-96 overflow-y-auto">
                                        {!! $email->body_html !!}
                                    </div>
                                    <div class="mt-2 text-right">
                                        <button type="button" @click="selectedEmail = null"
                                                class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                                            Close Preview &uarr;
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between text-xs text-slate-500">
                <span>Showing last 15 sent emails</span>
                @auth
                    <a href="{{ route('admin.emails.index') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">
                        View Full Outbox &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
