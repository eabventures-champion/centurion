<x-guest-layout>
    <div class="max-w-md w-full mx-auto">
        <!-- Glassmorphic Card -->
        <div class="glass-card p-8 md:p-10 relative overflow-hidden group">
            <!-- Header -->
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-black text-white mb-2 tracking-tight">Welcome Back</h2>
                <p class="text-slate-400 text-sm font-medium">Access your ministerial dashboard</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email"
                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] ml-1">
                        Email or Phone Number
                    </label>
                    <div class="relative group/input">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within/input:text-indigo-500 text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            placeholder="Enter your credentials"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border-white/5 text-white placeholder-slate-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl transition-all sm:text-sm shadow-inner">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex justify-between items-end mb-1 px-1">
                        <label for="password"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px]">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-wider"
                                href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative group/input">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within/input:text-indigo-500 text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border-white/5 text-white placeholder-slate-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl transition-all sm:text-sm shadow-inner">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-3 px-1">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" id="remember_me" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white">
                        </div>
                    </label>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Stay signed in</span>
                </div>

                <!-- Action Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-sm uppercase tracking-[3px] rounded-2xl shadow-2xl shadow-indigo-600/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2 group-hover:shadow-indigo-600/40">
                        Log in
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Note -->
        <p class="mt-8 text-center text-[11px] font-bold text-slate-600 uppercase tracking-[2px]">
            Centurion Campaign &copy; {{ date('Y') }}
        </p>
    </div>
</x-guest-layout>