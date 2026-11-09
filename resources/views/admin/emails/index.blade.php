<x-layouts.admin :title="__('rooms.nav.simulated_emails')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('rooms.nav.simulated_emails') }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Live demo outbox. In Demo Mode, all transaction emails to organizers and the office are captured in real-time here.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    SMTP Intercept Active
                </span>
            </div>
        </div>

        <!-- Filters Form -->
        <form method="GET" action="{{ route('admin.emails.index') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-700 mb-1">Search Recipient / Subject</label>
                <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search email or subject..."
                       class="w-full rounded-xl border-slate-200 text-xs py-2 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="event_type" class="block text-xs font-semibold text-slate-700 mb-1">Event Type</label>
                <select id="event_type" name="event_type" class="w-full rounded-xl border-slate-200 text-xs py-2 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Events</option>
                    <option value="booking_created" {{ ($filters['event_type'] ?? '') === 'booking_created' ? 'selected' : '' }}>Created (Booking Confirmation)</option>
                    <option value="booking_moved" {{ ($filters['event_type'] ?? '') === 'booking_moved' ? 'selected' : '' }}>Rescheduled</option>
                    <option value="booking_cancelled" {{ ($filters['event_type'] ?? '') === 'booking_cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="booking_restored" {{ ($filters['event_type'] ?? '') === 'booking_restored' ? 'selected' : '' }}>Restored</option>
                </select>
            </div>

            <div>
                <label for="recipient_type" class="block text-xs font-semibold text-slate-700 mb-1">Recipient</label>
                <select id="recipient_type" name="recipient_type" class="w-full rounded-xl border-slate-200 text-xs py-2 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Recipients</option>
                    <option value="organizer" {{ ($filters['recipient_type'] ?? '') === 'organizer' ? 'selected' : '' }}>Employee / Organizer</option>
                    <option value="office" {{ ($filters['recipient_type'] ?? '') === 'office' ? 'selected' : '' }}>Office Facilities</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                    {{ __('rooms.common.filter') }}
                </button>
                @if (! empty(array_filter($filters)))
                    <a href="{{ route('admin.emails.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl border border-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Two-Pane Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Pane: Email Table (5 cols on lg) -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="divide-y divide-slate-100 max-h-[680px] overflow-y-auto">
                    @forelse ($emails as $email)
                        @php
                            $isSelected = $selectedEmail && $selectedEmail->id === $email->id;
                        @endphp
                        <a href="{{ route('admin.emails.index', array_merge($filters, ['view_id' => $email->id])) }}"
                           class="block p-4 transition hover:bg-slate-50 {{ $isSelected ? 'bg-indigo-50/50 border-l-4 border-indigo-600' : '' }}">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-1.5 truncate">
                                    <span class="font-semibold text-xs text-slate-900 truncate">{{ $email->recipient_name }}</span>
                                    <span class="text-[11px] text-slate-400 truncate">&lt;{{ $email->recipient_email }}&gt;</span>
                                </div>
                                <span class="text-[10px] whitespace-nowrap text-slate-400 font-mono">
                                    {{ $email->sent_at->format('M j, H:i') }}
                                </span>
                            </div>

                            <p class="text-xs font-medium text-slate-800 truncate mb-1.5">
                                {{ $email->subject }}
                            </p>

                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                    @if ($email->event_type === 'booking_created') bg-emerald-100 text-emerald-800
                                    @elseif ($email->event_type === 'booking_moved') bg-amber-100 text-amber-800
                                    @elseif ($email->event_type === 'booking_cancelled') bg-rose-100 text-rose-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst(str_replace('booking_', '', $email->event_type)) }}
                                </span>

                                <span class="text-[10px] font-medium text-slate-500 uppercase tracking-wider">
                                    {{ $email->recipient_type }}
                                </span>

                                @if ($email->booking)
                                    <span class="text-[10px] font-mono text-slate-400 ml-auto">
                                        {{ $email->booking->reference_code }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-sm text-slate-400 italic">
                            No simulated emails match your search.
                        </div>
                    @endforelse
                </div>

                <div class="p-3 bg-slate-50 border-t border-slate-100">
                    {{ $emails->links() }}
                </div>
            </div>

            <!-- Right Pane: Email Body Viewer (7 cols on lg) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                @if ($selectedEmail)
                    <!-- Email Header Details -->
                    <div class="p-5 border-b border-slate-200 space-y-3 bg-slate-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900 leading-tight">
                                {{ $selectedEmail->subject }}
                            </h2>
                            <span class="text-xs font-mono text-slate-500">
                                {{ $selectedEmail->sent_at->isoFormat('dddd, MMMM D, YYYY [at] HH:mm:ss') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="font-semibold text-slate-500">To:</span>
                                <span class="text-slate-800 font-medium ml-1">{{ $selectedEmail->recipient_name }} &lt;{{ $selectedEmail->recipient_email }}&gt;</span>
                            </div>
                            <div>
                                <span class="font-semibold text-slate-500">Type:</span>
                                <span class="text-slate-800 font-medium ml-1 capitalize">{{ $selectedEmail->recipient_type }} ({{ $selectedEmail->event_type }})</span>
                            </div>
                            @if ($selectedEmail->booking)
                                <div>
                                    <span class="font-semibold text-slate-500">Reservation:</span>
                                    <a href="{{ route('admin.bookings.edit', $selectedEmail->booking) }}" class="text-indigo-600 font-mono font-medium ml-1 hover:underline">
                                        {{ $selectedEmail->booking->reference_code }} ({{ $selectedEmail->booking->room->localized_name }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Email Body Container -->
                    <div class="p-4 sm:p-6 bg-slate-100/50 min-h-[460px]">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <iframe srcdoc="{{ htmlspecialchars($selectedEmail->body_html) }}"
                                    sandbox="allow-popups allow-popups-to-escape-sandbox"
                                    class="w-full min-h-[500px] border-0"></iframe>
                        </div>
                    </div>
                @else
                    <div class="p-16 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="text-sm font-medium">Select an email from the left pane to preview its rendered HTML body.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
