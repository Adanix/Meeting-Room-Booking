@if(config('demo.enabled', false))
    @php
        $simulatedEmailCount = \App\Models\SimulatedEmail::count();
        $currentUser = auth()->user();
    @endphp

    <div class="w-full overflow-hidden bg-slate-900 text-slate-100 text-xs py-2 px-3 sm:px-6 border-b border-slate-800 antialiased">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2.5">
            <!-- Left Info -->
            <div class="flex items-center gap-2 min-w-0 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-400 text-slate-950 uppercase tracking-wider shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-950 animate-pulse"></span>
                    Demo Mode
                </span>
                <span class="text-slate-300 truncate">
                    @if($currentUser)
                        Active Persona: <strong class="text-white">{{ $currentUser->name }}</strong> (<span class="text-amber-300 font-semibold uppercase text-[10px]">{{ $currentUser->role }}</span>)
                    @else
                        Active: <strong class="text-white">Guest Visitor</strong> (Public Portal)
                    @endif
                </span>
            </div>

            <!-- Right Controls -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <!-- Outbox Button -->
                <button type="button"
                        @click="$dispatch('open-outbox')"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white border border-slate-700 transition-colors">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('rooms.outbox') }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-indigo-500 text-white">
                        {{ $simulatedEmailCount }}
                    </span>
                </button>

                <!-- Persona Dropdown / Quick Links -->
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click="open = !open" @click.outside="open = false" type="button"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-500 font-medium transition-colors">
                        <span>{{ __('rooms.switch_persona') }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition
                         class="origin-top-right absolute right-0 mt-1.5 w-60 rounded-xl bg-white text-slate-800 shadow-xl border border-slate-200 z-50 p-1.5 divide-y divide-slate-100"
                         style="display: none;">
                        <div class="py-1">
                            <form action="{{ route('demo.quick-login') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persona_key" value="admin">
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-xs hover:bg-slate-50 flex items-center justify-between group">
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-indigo-600">Nikola Jovanović</div>
                                        <div class="text-[10px] text-slate-500">Facilities Admin &rarr; Dashboard</div>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700">admin</span>
                                </button>
                            </form>
                            <form action="{{ route('demo.quick-login') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persona_key" value="coordinator">
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-xs hover:bg-slate-50 flex items-center justify-between group">
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-indigo-600">Ana Marković</div>
                                        <div class="text-[10px] text-slate-500">Office Coordinator &rarr; Bookings</div>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700">coordinator</span>
                                </button>
                            </form>
                            <form action="{{ route('demo.quick-login') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persona_key" value="employee">
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-xs hover:bg-slate-50 flex items-center justify-between group">
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-indigo-600">Marko Petrović</div>
                                        <div class="text-[10px] text-slate-500">Employee &rarr; Rooms Portal</div>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">employee</span>
                                </button>
                            </form>
                        </div>
                        <div class="pt-1">
                            <a href="{{ route('rooms.index') }}" class="block px-3 py-1.5 text-[11px] text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg">
                                &bull; Browse Public Rooms Portal (/rooms)
                            </a>
                            <a href="{{ route('public.docs') }}" class="block px-3 py-1.5 text-[11px] text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg">
                                &bull; Product Documentation (/docs)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reset Demo Form -->
                <form action="{{ route('demo.reset') }}" method="POST" onsubmit="return confirm('Reset all demo data and reseed initial dataset?');">
                    @csrf
                    <button type="submit"
                            title="Reset bookings and reseed demo dataset"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 hover:bg-rose-900/40 hover:text-rose-200 border border-slate-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>{{ __('rooms.reset_demo') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
