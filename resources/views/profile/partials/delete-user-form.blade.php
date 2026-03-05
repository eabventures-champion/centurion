<section class="space-y-6">
    <header>
        <h2 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 font-medium">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 hover:text-rose-400 border border-rose-500/20 hover:border-rose-500/30 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <!-- Warning Icon -->
            <div class="flex justify-center mb-6">
                <div class="p-4 bg-rose-500/10 rounded-2xl border border-rose-500/20">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-lg font-black text-slate-900 dark:text-white text-center tracking-tight">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 font-medium text-center leading-relaxed">
                {{ __('Your account deletion request will be sent for approval. Please enter your password to confirm.') }}
            </p>

            <div class="mt-6">
                <label for="delete-password"
                    class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px] mb-2">
                    {{ __('Confirm Password') }}
                </label>

                <input id="delete-password" name="password" type="password"
                    class="w-full px-4 py-3 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50 transition-all"
                    placeholder="{{ __('Enter your password') }}" />

                @if($errors->userDeletion->has('password'))
                    <p class="mt-2 text-xs text-rose-500 font-bold">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest transition-all duration-200">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-500 hover:bg-rose-600 border border-rose-600 rounded-xl text-xs font-black text-white uppercase tracking-widest transition-all duration-200 shadow-lg shadow-rose-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>