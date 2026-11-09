<x-layouts.marketing>
    <x-slot:title>Admin Login — ROOMS</x-slot:title>

    <div class="py-16 sm:py-24">
        <div class="max-w-md mx-auto px-4 sm:px-6 w-full">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="text-center mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-bold text-lg flex items-center justify-center mx-auto mb-3">
                        R
                    </div>
                    <h1 class="text-xl font-bold text-slate-900">Sign in to ROOMS Admin</h1>
                    <p class="text-xs text-slate-500 mt-1">Enter your staff email or username and password</p>
                </div>

                @if($demoMode)
                    <div class="mb-6 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between gap-3">
                        <div>
                            <strong>Demo Mode Active:</strong> You can sign in with one click without entering a password.
                        </div>
                        <a href="{{ route('demo.login') }}" class="font-bold underline shrink-0 text-amber-950">
                            Quick Login &rarr;
                        </a>
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-700 mb-1">
                            Email Address or Username
                        </label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-hidden">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Password
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-hidden">
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                        Sign In
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                    <a href="{{ route('rooms.index') }}" class="hover:text-slate-900">
                        &larr; Return to Public Room Booking Portal
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
