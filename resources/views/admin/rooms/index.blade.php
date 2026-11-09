<x-layouts.admin :title="__('rooms.nav.rooms')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('rooms.nav.rooms') }}</h1>
                <p class="text-sm text-slate-500 mt-1">Configure meeting spaces, capacity, audio/video equipment, and public portal visibility.</p>
            </div>
            <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('rooms.admin.new_room') }}
            </a>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 font-medium">
                {{ session('status') }}
            </div>
        @endif

        <!-- Rooms List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($rooms as $room)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between transition hover:shadow-md">
                    <div>
                        <!-- Room Header / Image -->
                        <div class="relative h-44 bg-slate-100 overflow-hidden">
                            @if ($room->photo_path)
                                <img src="{{ asset('storage/' . $room->photo_path) }}" alt="{{ $room->name_en }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-inner" style="background-color: {{ $room->color_hex }}">
                                        {{ Str::substr($room->name_en, 0, 2) }}
                                    </div>
                                </div>
                            @endif

                            <!-- Visibility Badge -->
                            <div class="absolute top-3 right-3">
                                @if ($room->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/90 text-white backdrop-blur-sm shadow-sm">
                                        {{ __('rooms.admin.active') }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-800/90 text-white backdrop-blur-sm shadow-sm">
                                        {{ __('rooms.admin.hidden') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Color indicator & Capacity -->
                            <div class="absolute bottom-3 left-3 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/95 text-slate-800 shadow-sm backdrop-blur-sm flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $room->color_hex }}"></span>
                                    {{ $room->capacity }} {{ __('rooms.portal.people') }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 space-y-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $room->localized_name }}</h3>
                                <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $room->location }}
                                </p>
                            </div>

                            @if ($room->localized_description)
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                    {{ $room->localized_description }}
                                </p>
                            @endif

                            <!-- Equipment Tags -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @forelse ($room->equipments as $eq)
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
                                        {{ $eq->localized_label }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">No equipment listed</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                        <!-- Toggle active form -->
                        <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition {{ $room->is_active ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-emerald-300 text-emerald-700 hover:bg-emerald-50' }}">
                                {{ $room->is_active ? 'Hide from Portal' : 'Show on Portal' }}
                            </button>
                        </form>

                        <a href="{{ route('admin.rooms.edit', $room) }}" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            {{ __('rooms.common.edit') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-slate-500 text-sm">No rooms configured in the system.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
