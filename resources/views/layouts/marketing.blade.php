<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ROOMS — Enterprise Workspace & Meeting Room Scheduling' }}</title>

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

    <!-- Public Marketing Navigation Header -->
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
                        <span class="hidden sm:inline-block ml-1.5 text-xs text-slate-500 font-medium">| Enterprise Scheduling</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden lg:flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('rooms.index') }}"
                       class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('rooms.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        {{ __('rooms.nav_rooms') }}
                    </a>
                    <a href="{{ route('schedule.index') }}"
                       class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('schedule.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        {{ __('rooms.nav_schedule') }}
                    </a>
                    <a href="{{ route('public.docs') }}"
                       class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.docs') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        {{ __('rooms.nav_docs') }}
                    </a>
                </nav>

                <!-- Right CTAs & Language Switcher -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <x-language-switcher />

                    @auth
                        <a href="{{ route('admin.dashboard') }}"
                           class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                            <span>{{ __('rooms.nav_dashboard') }}</span>
                        </a>
                    @else
                        <a href="{{ route('demo.login') }}"
                           class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                            <span>{{ __('Explore Interactive Demo') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @endauth

                    <!-- Mobile Hamburger Button (Min 44x44px touch target) -->
                    <button type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="lg:hidden p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                            aria-label="Toggle Navigation Menu">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Slide-Down Menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2"
             style="display: none;">
            <a href="{{ route('public.home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_home') }}
            </a>
            <a href="{{ route('rooms.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_rooms') }}
            </a>
            <a href="{{ route('schedule.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_schedule') }}
            </a>
            <a href="{{ route('public.docs') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                {{ __('rooms.nav_docs') }}
            </a>
            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('demo.login') }}" class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    {{ __('Explore Interactive Demo') }}
                </a>
            </div>
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

    <!-- Main Content Canvas -->
    <main class="flex-1 w-full min-w-0">
        {{ $slot }}
    </main>

    <!-- Public Footer -->
    <footer class="w-full bg-slate-900 text-slate-400 border-t border-slate-800 mt-16 py-12 text-sm antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Col 1: Brand -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2 text-white">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-sm">R</div>
                        <span class="text-xl font-bold tracking-tight">ROOMS</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                        Enterprise meeting room scheduling & workspace management system. Built with Laravel 13, PHP 8.5, Tailwind CSS v4, and Alpine.js.
                    </p>
                    <div class="pt-2 text-xs text-slate-400">
                        <span>Contact & Customization: </span>
                        <a href="mailto:nixresponding@gmail.com" class="text-indigo-400 hover:text-indigo-300 font-semibold underline">
                            nixresponding@gmail.com
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Workspace Portal</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('public.home') }}" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('rooms.index') }}" class="hover:text-white transition-colors">Live Room Portal</a></li>
                        <li><a href="{{ route('schedule.index') }}" class="hover:text-white transition-colors">Daily Schedule Board</a></li>
                        <li><a href="{{ route('public.docs') }}" class="hover:text-white transition-colors">Product Documentation</a></li>
                    </ul>
                </div>

                <!-- Col 3: Demo Access -->
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Interactive Demo</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('demo.login') }}" class="hover:text-white transition-colors">Choose Demo Persona</a></li>
                        <li><button type="button" @click="$dispatch('open-outbox')" class="hover:text-white transition-colors">Simulated Email Outbox</button></li>
                        @auth
                            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Admin Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Admin Password Login</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} ROOMS. All rights reserved.</p>
                <p>Public Demo Mode active. Outgoing emails are stored locally in the Simulated Email Outbox.</p>
            </div>
        </div>
    </footer>

    <!-- Simulated Email Outbox Drawer -->
    <x-email-outbox-drawer />

</body>
</html>
