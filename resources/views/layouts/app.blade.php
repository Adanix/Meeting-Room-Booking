<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ROOMS — Corporate Meeting Room Booking' }}</title>

    <!-- Tailwind CSS v4 & Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Local Alpine.js for deterministic offline & sandbox execution -->
    <script defer src="/vendor/alpine.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen w-full overflow-x-hidden bg-slate-50 text-slate-900 antialiased flex flex-col font-sans">

    <!-- Mandatory P0 Demo Banner -->
    <x-demo-banner />

    <!-- Application Top Navigation Bar -->
    <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-30" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Brand Logo -->
                <a href="{{ route('public.home') }}" class="flex items-center gap-3 shrink-0 group">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-sm group-hover:bg-indigo-700 transition-colors">
                        R
                    </div>
                    <div class="min-w-0">
                        <span class="text-lg font-black tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">ROOMS</span>
                        <span class="hidden sm:inline-block ml-1.5 text-xs text-slate-500 font-medium">| Booking Portal</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('rooms.index') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('rooms.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        {{ __('rooms.nav_rooms') }}
                    </a>
                    <a href="{{ route('schedule.index') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('schedule.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        {{ __('rooms.nav_schedule') }}
                    </a>
                    <a href="{{ route('public.docs') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.docs') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        {{ __('rooms.nav_docs') }}
                    </a>
                </nav>

                <!-- Right Tools: Switcher, User Menu -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <x-language-switcher />

                    @auth
                        <!-- Authenticated User Profile Menu -->
                        <div x-data="{ userMenuOpen: false }" class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false" type="button"
                                    class="inline-flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:inline-block text-xs font-semibold text-slate-700 max-w-[120px] truncate">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="userMenuOpen"
                                 x-transition
                                 class="origin-top-right absolute right-0 mt-1.5 w-48 rounded-xl bg-white shadow-xl border border-slate-200 z-50 p-1.5"
                                 style="display: none;">
                                @if(auth()->user()->hasRole(['admin', 'coordinator']))
                                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 rounded-lg">
                                        {{ __('rooms.nav_admin') }}
                                    </a>
                                    <div class="my-1 border-t border-slate-100"></div>
                                @endif
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg">
                                        {{ __('rooms.nav_logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('demo.login') }}"
                           class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                            <span>{{ __('rooms.nav_demo_login') }}</span>
                        </a>
                    @endauth

                    <!-- Mobile Hamburger -->
                    <button type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                            aria-label="Toggle navigation">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen"
             x-transition
             class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2"
             style="display: none;">
            <a href="{{ route('rooms.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_rooms') }}
            </a>
            <a href="{{ route('schedule.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_schedule') }}
            </a>
            <a href="{{ route('public.docs') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_docs') }}
            </a>
            @auth
                @if(auth()->user()->hasRole(['admin', 'coordinator']))
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                        {{ __('rooms.nav_admin') }}
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 rounded-lg">
                        {{ __('rooms.nav_logout') }}
                    </button>
                </form>
            @endauth
        </div>
    </header>

    <!-- Global Flash Toasts -->
    @if(session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-medium truncate">{{ session('status') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Content Slot -->
    <main class="flex-1 w-full min-w-0 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Simple Portal Footer -->
    <footer class="w-full bg-white border-t border-slate-200 py-6 text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900">ROOMS</span>
                <span>&bull;</span>
                <span>Contact: <a href="mailto:nixresponding@gmail.com" class="text-indigo-600 hover:underline">nixresponding@gmail.com</a></span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('public.home') }}" class="hover:text-slate-800">Home</a>
                <a href="{{ route('rooms.index') }}" class="hover:text-slate-800">Rooms</a>
                <a href="{{ route('schedule.index') }}" class="hover:text-slate-800">Schedule</a>
                <a href="{{ route('public.docs') }}" class="hover:text-slate-800">Documentation</a>
            </div>
        </div>
    </footer>

    <!-- Simulated Email Outbox Drawer -->
    <x-email-outbox-drawer />

</body>
</html>
