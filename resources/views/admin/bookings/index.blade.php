<x-layouts.admin>
    <x-slot:title>Manage Bookings — ROOMS Admin</x-slot:title>

    <!-- Header -->
    <header class="w-full mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                    {{ __('rooms.nav_bookings') }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 truncate">
                    Filter, edit, cancel, restore reservations and export to CSV
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('admin.bookings.create') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition-colors">
                    <span>+ New Booking</span>
                </a>
                <a href="{{ route('admin.bookings.export', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>{{ __('rooms.export_csv') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Filters Bar -->
    <section class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Ref, title, organizer, email..."
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
            </div>

            <!-- Room Filter -->
            <div>
                <label for="room_id" class="block text-xs font-semibold text-slate-700 mb-1">Room</label>
                <select name="room_id" id="room_id"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ (string)($filters['room_id'] ?? '') === (string)$r->id ? 'selected' : '' }}>
                            {{ $r->localizedName() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
                    <option value="">All Statuses</option>
                    <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label for="date_from" class="block text-xs font-semibold text-slate-700 mb-1">Date From</label>
                <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-hidden">
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 py-2 px-3 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs">
                    Filter
                </button>
                @if(!empty(array_filter($filters)))
                    <a href="{{ route('admin.bookings.index') }}"
                       class="py-2 px-3 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- Bookings Table Container -->
    <div class="w-full max-w-full overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xs">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[11px]">
                <tr>
                    <th class="py-3 px-4">Reference</th>
                    <th class="py-3 px-4">Room</th>
                    <th class="py-3 px-4">Date & Time</th>
                    <th class="py-3 px-4">Meeting Title</th>
                    <th class="py-3 px-4">Organizer</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $b)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-slate-800">
                            {{ $b->reference_code }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $b->room ? $b->room->localizedName() : 'N/A' }}
                            <div class="text-[10px] text-slate-400 font-normal">{{ $b->room ? $b->room->location : '' }}</div>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">{{ $b->booking_date->format('Y-m-d') }}</div>
                            <div class="text-[11px] text-slate-500">{{ $b->timeRangeString() }} ({{ $b->duration_minutes }}m)</div>
                        </td>
                        <td class="py-3 px-4 max-w-[200px]">
                            <div class="font-bold text-slate-900 truncate">{{ $b->title }}</div>
                            <div class="text-[11px] text-slate-400">{{ $b->attendees_count }} attendees</div>
                        </td>
                        <td class="py-3 px-4 max-w-[180px]">
                            <div class="font-semibold text-slate-900 truncate">{{ $b->organizer_name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">{{ $b->organizer_email }}</div>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $b->isConfirmed() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.bookings.edit', $b->id) }}"
                                   class="px-2.5 py-1 rounded-md text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                                    {{ __('rooms.edit') }}
                                </a>

                                @if($b->isConfirmed())
                                    <form action="{{ route('admin.bookings.cancel', $b->id) }}" method="POST"
                                          onsubmit="return confirm('Cancel reservation {{ $b->reference_code }}?');" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-md text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                                            {{ __('rooms.cancel') }}
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.bookings.restore', $b->id) }}" method="POST"
                                          onsubmit="return confirm('Restore reservation {{ $b->reference_code }}?');" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-md text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                            {{ __('rooms.restore') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            No reservations found matching active filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</x-layouts.admin>
