<x-app-layout>
    <x-slot name="header">
        {{ __('Secure Access Required') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8 text-center relative overflow-hidden group">
                <!-- Decorative element -->
                <div
                    class="absolute -top-12 -right-12 w-32 h-32 bg-rose-500/10 rounded-full blur-3xl group-hover:bg-rose-500/20 transition-all duration-700">
                </div>

                <div class="mb-8">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-500/10 border border-rose-500/20 mb-6 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-wider mb-2">Access
                        Protected</h3>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-relaxed">
                        This area contains sensitive church credentials. Please confirm your administrator password to
                        continue.
                    </p>
                </div>

                <form method="POST" action="{{ route('credentials.verify') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2 text-left">
                        <x-input-label for="password" :value="__('Admin Password')" class="ml-1" />
                        <x-text-input id="password"
                            class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/5 text-slate-900 dark:text-white focus:border-rose-500 focus:ring-rose-500/20 rounded-xl py-3.5 transition-all text-center tracking-[4px]"
                            type="password" name="password" required placeholder="••••••••" autofocus />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-center" />
                    </div>

                    <div class="pt-2">
                        <x-primary-button
                            class="w-full justify-center py-4 bg-rose-600 hover:bg-rose-500 shadow-xl shadow-rose-900/10">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            {{ __('Unlock Access') }}
                        </x-primary-button>
                    </div>

                    <p class="text-[9px] text-slate-600 font-bold uppercase tracking-[2px] mt-4">
                        Secure TLS Encryption Active
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>