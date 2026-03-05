<x-app-layout :show-back="true">
    <x-slot name="header">
        {{ __('Edit User') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Edit System User
                        </h3>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Update user
                            profile and permissions</p>
                    </div>
                    <a href="{{ route('users.index') }}"
                        class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>

                @if(session('success'))
                    <div
                        class="flash-alert mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 text-emerald-400 text-sm font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- Personal Info Section --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="title" :value="__('Title')" />
                                <select name="title" id="title"
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/5 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 transition-all sm:text-sm">
                                    <option value="">No Title</option>
                                    @foreach(['Brother', 'Sister', 'Pastor', 'Dcn', 'Dcns', 'Mr', 'Mrs', 'Bro', 'Sis'] as $t)
                                        <option value="{{ $t }}" {{ old('title', $user->title) == $t ? 'selected' : '' }}>
                                            {{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                    class="w-full" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input type="email" name="email" id="email"
                                    value="{{ old('email', $user->email) }}" class="w-full" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Role Assignment Section --}}
                    <div class="pt-6 border-t border-white/5 space-y-4">
                        <x-input-label :value="__('Assign Roles')" />
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($roles as $role)
                                <label
                                    class="relative flex items-center p-4 rounded-xl border border-white/5 bg-slate-900/50 cursor-pointer hover:border-indigo-500/30 transition-all group">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                        class="w-4 h-4 rounded border-white/10 bg-white/5 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900"
                                        {{ in_array($role->name, $user->roles->pluck('name')->toArray()) ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span
                                            class="block text-xs font-bold text-slate-900 dark:text-white group-hover:text-indigo-400 transition-colors uppercase tracking-wider">{{ $role->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                    </div>

                    {{-- Security Section --}}
                    <div class="pt-6 border-t border-white/5 space-y-6">
                        <div>
                            <h4
                                class="text-[11px] font-black text-rose-400 uppercase tracking-[2px] flex items-center gap-2 mb-4">
                                <span class="w-4 h-px bg-rose-500/20"></span> Security & Credentials
                            </h4>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-6 italic">
                                Leave blank if you do not want to change the password
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="password" :value="__('New Password')" />
                                <x-text-input type="password" name="password" id="password" class="w-full"
                                    autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                <x-text-input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <x-primary-button type="submit" class="w-full justify-center py-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Save User Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>