<x-layouts.marketing>
    <x-slot:title>Demo Persona Selector — ROOMS</x-slot:title>

    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-4">
                    Instant Demo Authentication
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    {{ __('Choose How to Explore the ROOMS Demo') }}
                </h1>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    {{ __('Select a predefined workplace persona below for instant one-click access. No username or password entry is required in demo mode.') }}
                </p>
            </div>

            <!-- 3 Persona Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <!-- Card 1: Facilities Admin -->
                <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 flex flex-col justify-between hover:border-indigo-500 hover:shadow-lg transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-700 font-black text-lg flex items-center justify-center border border-rose-200">
                                NJ
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider">
                                admin
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            Nikola Jovanović
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mb-4">
                            Facilities & Workspace Administrator
                        </p>

                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Full Admin Dashboard with live utilization KPIs</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create, edit, cancel, and <strong>restore</strong> bookings</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Manage meeting rooms, capacities, and photo uploads</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Configure working hours, holidays, and export CSV</span>
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('demo.quick-login') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="persona_key" value="admin">
                        <button type="submit"
                                class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-indigo-600 shadow-sm transition-colors text-center">
                            {{ __('Continue as Facilities Admin') }} &rarr;
                        </button>
                    </form>
                </div>

                <!-- Card 2: Office Coordinator -->
                <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 flex flex-col justify-between hover:border-indigo-500 hover:shadow-lg transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 font-black text-lg flex items-center justify-center border border-amber-200">
                                AM
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                coordinator
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            Ana Marković
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mb-4">
                            Office Reception & Schedule Coordinator
                        </p>

                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Manage daily reservations & monthly calendar grid</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create bookings on behalf of team members</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Export filtered reservation records to Excel CSV</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Inspect office notification email outbox</span>
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('demo.quick-login') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="persona_key" value="coordinator">
                        <button type="submit"
                                class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-indigo-600 shadow-sm transition-colors text-center">
                            {{ __('Continue as Office Coordinator') }} &rarr;
                        </button>
                    </form>
                </div>

                <!-- Card 3: Employee -->
                <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 flex flex-col justify-between hover:border-indigo-500 hover:shadow-lg transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 font-black text-lg flex items-center justify-center border border-emerald-200">
                                MP
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                employee
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            Marko Petrović
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mb-4">
                            Company Product Engineer
                        </p>

                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Instant room booking with name & email pre-filled</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>One-click Google Calendar & .ics calendar downloads</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Test self-service reschedule & cancellation via magic link</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Inspect personalized email notifications in the Outbox</span>
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('demo.quick-login') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="persona_key" value="employee">
                        <button type="submit"
                                class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors text-center">
                            {{ __('Continue as Employee') }} &rarr;
                        </button>
                    </form>
                </div>
            </div>

            <!-- Guest Callout -->
            <div class="mt-12 text-center text-xs text-slate-500">
                <p>
                    {{ __('Want to test as an unauthenticated employee or visitor?') }}
                    <a href="{{ route('rooms.index') }}" class="font-bold text-indigo-600 hover:underline ml-1">Open Public Room Booking Portal (/rooms)</a>
                    or
                    <a href="{{ route('public.docs') }}" class="font-bold text-indigo-600 hover:underline ml-1">Read Feature Documentation (/docs)</a>.
                </p>
            </div>
        </div>
    </div>
</x-layouts.marketing>
