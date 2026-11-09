<x-layouts.app>
    <x-slot:title>Live Meeting Rooms & Quick Booking — ROOMS</x-slot:title>

    <!-- Page Header (Appendix B Anti-Overflow Pattern) -->
    <header class="w-full mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                    {{ __('rooms.nav_rooms') }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600 break-words">
                    {{ __('Check live room availability, filter by headcount and equipment, and reserve slots in seconds.') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0 w-full sm:w-auto">
                <a href="{{ route('schedule.index', ['date' => $selectedDate]) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors w-full sm:w-auto shadow-xs">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('rooms.daily_schedule') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Quick Search & Filter Bar (Anti-Overflow responsive grid) -->
    <section class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs mb-8">
        <form method="GET" action="{{ route('rooms.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            <!-- Room Filter -->
            <div>
                <label for="room_id" class="block text-xs font-semibold text-slate-700 mb-1">Room</label>
                <select name="room_id" id="room_id"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                    <option value="">All Active Rooms</option>
                    @foreach($allRooms as $r)
                        <option value="{{ $r->id }}" {{ (string)$selectedRoomId === (string)$r->id ? 'selected' : '' }}>
                            {{ $r->localizedName() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div>
                <label for="date" class="block text-xs font-semibold text-slate-700 mb-1">Target Date</label>
                <input type="date" name="date" id="date" value="{{ $selectedDate }}" min="{{ $todayDate }}" max="{{ $maxDate }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
            </div>

            <!-- Capacity Filter -->
            <div>
                <label for="capacity" class="block text-xs font-semibold text-slate-700 mb-1">Min Capacity</label>
                <select name="capacity" id="capacity"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                    <option value="">Any Capacity</option>
                    <option value="2" {{ (string)$selectedCapacity === '2' ? 'selected' : '' }}>2+ people</option>
                    <option value="4" {{ (string)$selectedCapacity === '4' ? 'selected' : '' }}>4+ people</option>
                    <option value="6" {{ (string)$selectedCapacity === '6' ? 'selected' : '' }}>6+ people</option>
                    <option value="8" {{ (string)$selectedCapacity === '8' ? 'selected' : '' }}>8+ people</option>
                    <option value="12" {{ (string)$selectedCapacity === '12' ? 'selected' : '' }}>12+ people</option>
                    <option value="16" {{ (string)$selectedCapacity === '16' ? 'selected' : '' }}>16+ people</option>
                </select>
            </div>

            <!-- Equipment Filter -->
            <div>
                <label for="equipment" class="block text-xs font-semibold text-slate-700 mb-1">Equipment</label>
                <select name="equipment" id="equipment"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 outline-hidden">
                    <option value="">All Equipment</option>
                    @foreach($allEquipments as $eq)
                        <option value="{{ $eq->code }}" {{ $selectedEquipment === $eq->code ? 'selected' : '' }}>
                            {{ $eq->localizedLabel() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                    <span>Filter</span>
                </button>
                @if($selectedRoomId || $selectedCapacity || $selectedEquipment || $selectedDate !== $todayDate)
                    <a href="{{ route('rooms.index') }}"
                       class="px-3 py-2.5 rounded-lg text-xs font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- Rooms Cards Grid (3 Columns on Desktop, 2 on Tablet, 1 on Mobile) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roomsWithStatus as $item)
            @php
                $room = $item['room'];
                $status = $item['status'];
                $freeSlots = $item['free_slots_count'];
                $photo = $room->photoUrl();
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <!-- Photo Header / Fallback SVG Illustration -->
                    <div class="relative w-full aspect-video bg-gradient-to-tr from-slate-800 to-indigo-950 overflow-hidden flex items-center justify-center text-white">
                        @if($photo)
                            <img src="{{ $photo }}" alt="{{ $room->localizedName() }}" class="w-full h-full object-cover">
                        @else
                            <!-- Clean Architectural Line Art SVG Fallback -->
                            <div class="text-center p-4">
                                <svg class="w-12 h-12 mx-auto text-indigo-400/80 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-xs font-semibold tracking-wider uppercase text-slate-300">{{ $room->localizedName() }}</span>
                            </div>
                        @endif

                        <!-- Floating Live Status Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border backdrop-blur-md bg-white/95 {{ $status['badge_class'] }} shadow-xs">
                                <span class="w-2 h-2 rounded-full {{ $status['dot_class'] }}"></span>
                                <span>{{ $status['label'] }}</span>
                            </span>
                        </div>

                        <!-- Capacity Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-900/80 text-white backdrop-blur-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>{{ $room->capacity }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Room Content -->
                    <div class="p-5">
                        <h2 class="text-lg font-bold text-slate-900 truncate">
                            {{ $room->localizedName() }}
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">
                            {{ $room->location }}
                        </p>

                        <!-- Live Status Subtitle -->
                        <div class="mt-2.5 p-2 rounded-lg bg-slate-50 border border-slate-100 text-xs font-medium text-slate-700 flex items-center justify-between gap-2">
                            <span class="truncate">{{ $status['subtitle'] }}</span>
                            @if($freeSlots > 0)
                                <span class="text-[11px] font-semibold text-emerald-700 shrink-0">
                                    {{ $freeSlots }} slots open
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        @if($desc = $room->localizedDescription())
                            <p class="mt-3 text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $desc }}
                            </p>
                        @endif

                        <!-- Equipment Badges -->
                        @if($room->equipments->isNotEmpty())
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach($room->equipments as $eq)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                        {{ $eq->localizedLabel() }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="p-5 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a href="{{ route('schedule.index', ['date' => $selectedDate]) }}"
                           class="text-xs font-semibold text-slate-600 hover:text-indigo-600">
                            View Timeline
                        </a>

                        <a href="{{ route('rooms.book', ['room' => $room->slug, 'date' => $selectedDate, 'people' => $selectedCapacity ?: 2]) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                            <span>{{ __('rooms.book_room') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <p class="text-sm font-semibold text-slate-700">No rooms match your filter criteria.</p>
                <a href="{{ route('rooms.index') }}" class="mt-2 inline-block text-xs font-bold text-indigo-600 hover:underline">
                    Reset all filters
                </a>
            </div>
        @endforelse
    </div>
</x-layouts.app>
