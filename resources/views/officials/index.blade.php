<x-app-layout>
    <x-slot name="header">
        {{ __('Officials') }}
    </x-slot>

    <div class="py-6" x-data="{ 
        editingOfficial: null,
        viewingOfficial: null,
        editForm: {
            name: '',
            email: '',
            is_default: false,
            pcf_ids: [],
            action: ''
        },
        openEditModal(official) {
            this.editingOfficial = official;
            this.editForm.name = official.name;
            this.editForm.email = official.email;
            this.editForm.is_default = !!official.is_default;
            this.editForm.pcf_ids = official.pcfs ? official.pcfs.map(p => p.id) : [];
            this.editForm.action = `/officials/${official.id}`;
            $dispatch('open-modal', 'edit-official-modal');
        },
        openViewModal(official) {
            this.viewingOfficial = official;
            $dispatch('open-modal', 'view-pcfs-modal');
        }
    }">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header Section -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        Officials Management
                        @php
                            $officialCount = \App\Models\User::role('Official')->count();
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm shadow-indigo-500/10">
                            {{ $officialCount }}
                        </span>
                    </h3>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                        Zonal unit oversight and PCF assignment management
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="$dispatch('open-modal', 'add-official-modal')"
                        class="flex items-center justify-center gap-2.5 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-2xl transition-all active:scale-95 shadow-xl shadow-indigo-600/30 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Official
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Officials List -->
                <div
                    class="glass-card overflow-hidden border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                        <h3
                            class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Registered Officials
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-200 dark:divide-white/5">
                        @forelse($officials as $official)
                            <div @click="openViewModal(@js($official))"
                                class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group hover:border-indigo-500/30 transition-all cursor-pointer {{ $official->is_default ? 'ring-1 ring-amber-500/30 border-amber-500/20 bg-amber-50/10 dark:bg-amber-500/5' : '' }}">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-12 h-12 rounded-xl {{ $official->is_default ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400' : 'bg-slate-100 dark:bg-white/5 text-indigo-600 dark:text-indigo-400' }} flex items-center justify-center font-bold text-sm group-hover:scale-105 transition-transform">
                                        @if($official->is_default)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors flex items-center gap-2">
                                            {{ $official->name }}
                                            @if($official->is_default)
                                                <span
                                                    class="text-[8px] font-black bg-amber-500/20 text-amber-600 dark:text-amber-400 px-2 py-0.5 rounded-full uppercase tracking-widest">Default</span>
                                            @endif
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                                            <span
                                                class="text-[10px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $official->email }}
                                            </span>
                                            @if($official->pcfs->count() > 0)
                                                <span
                                                    class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    {{ $official->pcfs->count() }} PCF(s) Assigned
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2" @click.stop>
                                    <button type="button" @click="openEditModal(@js($official))"
                                        class="p-2 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                        title="Edit Official">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @if(!$official->is_default)
                                        <form action="{{ route('officials.set-default', $official) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="p-2 text-slate-500 hover:text-amber-400 transition-colors"
                                                title="Set as Default">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('officials.destroy', $official) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to remove this official?')"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-slate-500 hover:text-rose-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-slate-500 italic">No officials found. Create one to get started.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Official Modal -->
        <x-modal name="add-official-modal" focusable>
            <div class="p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3
                            class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                            <span
                                class="w-10 h-10 rounded-2xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </span>
                            Register New Official
                        </h3>
                        <p
                            class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[2px] mt-2">
                            Create a new zonal official and assign PCFs</p>
                    </div>
                    <button @click="$dispatch('close-modal', 'add-official-modal')"
                        class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('officials.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input type="text" name="name" id="name" placeholder="e.g. John Doe" required
                                    value="{{ old('name') }}" class="block w-full" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input type="email" name="email" id="email" placeholder="official@example.com"
                                    required value="{{ old('email') }}" class="block w-full" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input type="password" name="password" id="password"
                                    placeholder="Min 6 characters" required class="block w-full" />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="pcf_ids" :value="__('Assign PCFs (Hold Ctrl to select)')" />
                                <select name="pcf_ids[]" id="pcf_ids" multiple
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl text-slate-900 dark:text-white py-3 px-4 transition-all min-h-[160px] cursor-pointer custom-scrollbar">
                                    @foreach($pcfs as $pcf)
                                        <option value="{{ $pcf->id }}">{{ $pcf->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div
                                class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-white/10">
                                <input type="checkbox" name="is_default" id="is_default" value="1"
                                    class="w-5 h-5 rounded-lg bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 transition-all">
                                <label for="is_default"
                                    class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px] cursor-pointer select-none">Set
                                    as Default Official</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-white/10">
                        <button type="button" @click="$dispatch('close-modal', 'add-official-modal')"
                            class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white text-[10px] font-black uppercase tracking-[2px] rounded-2xl transition-all">
                            Cancel
                        </button>
                        <x-primary-button type="submit" class="px-10 py-3">
                            {{ __('Register Official') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>

        <!-- Edit Official Modal -->
        <x-modal name="edit-official-modal" focusable>
            <div class="p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </span>
                            Adjust Official Details
                        </h3>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[2px] mt-2">Modify credentials and PCF oversight assignments</p>
                    </div>
                    <button @click="$dispatch('close-modal', 'edit-official-modal')" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="editForm.action" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="edit_name" :value="__('Full Name')" />
                                <x-text-input type="text" name="name" id="edit_name" x-model="editForm.name" required
                                    class="block w-full" />
                            </div>

                            <div>
                                <x-input-label for="edit_email" :value="__('Email Address')" />
                                <x-text-input type="email" name="email" id="edit_email" x-model="editForm.email" required
                                    class="block w-full" />
                            </div>

                            <div>
                                <x-input-label for="edit_password" :value="__('Password (Leave blank to keep current)')" />
                                <x-text-input type="password" name="password" id="edit_password" placeholder="Min 6 characters"
                                    class="block w-full" />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="edit_pcf_ids" :value="__('Assign PCFs (Hold Ctrl to select)')" />
                                <select name="pcf_ids[]" id="edit_pcf_ids" x-model="editForm.pcf_ids" multiple
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl text-slate-900 dark:text-white py-3 px-4 transition-all min-h-[160px] cursor-pointer custom-scrollbar">
                                    @foreach($pcfs as $pcf)
                                        <option value="{{ $pcf->id }}">{{ $pcf->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-white/10">
                                <input type="checkbox" name="is_default" id="edit_is_default" value="1"
                                    x-model="editForm.is_default"
                                    class="w-5 h-5 rounded-lg bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 transition-all">
                                <label for="edit_is_default" class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px] cursor-pointer select-none">Set as Default Official</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-white/10">
                        <button type="button" @click="$dispatch('close-modal', 'edit-official-modal')"
                            class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white text-[10px] font-black uppercase tracking-[2px] rounded-2xl transition-all">
                            Cancel
                        </button>
                        <x-primary-button type="submit" class="px-10 py-3">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>

        <!-- View PCFs Modal -->
        <x-modal name="view-pcfs-modal" focusable>
            <div class="p-8">
                <template x-if="viewingOfficial">
                    <div>
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-2xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </span>
                                    PCFs Assigned to <span x-text="viewingOfficial.name" class="text-indigo-600 dark:text-indigo-400 ml-1 underline decoration-indigo-500/30 underline-offset-8"></span>
                                </h3>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[2px] mt-2">Active service units under this official's oversight</p>
                            </div>
                            <button @click="$dispatch('close-modal', 'view-pcfs-modal')" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-if="viewingOfficial.pcfs && viewingOfficial.pcfs.length > 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="pcf in viewingOfficial.pcfs" :key="pcf.id">
                                        <div class="group p-5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 hover:border-indigo-500/30 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm group-hover:scale-110 transition-transform"
                                                    x-text="pcf.name.charAt(0)"></div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <h5 class="text-sm font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" x-text="pcf.name"></h5>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest"
                                                            x-text="pcf.leader_name"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            
                            <template x-if="!viewingOfficial.pcfs || viewingOfficial.pcfs.length === 0">
                                <div class="text-center py-16 bg-slate-50/50 dark:bg-white/5 rounded-3xl border-2 border-dashed border-slate-200 dark:border-white/10">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-4 text-slate-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">No PCFs assigned to this official.</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">Assignments can be managed via the edit menu</p>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-end gap-3 pt-8 mt-4 border-t border-slate-200 dark:border-white/10">
                            <button @click="$dispatch('open-modal', 'edit-official-modal'); setTimeout(() => openEditModal(viewingOfficial), 100); $dispatch('close-modal', 'view-pcfs-modal')"
                                class="px-6 py-3 bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-[2px] rounded-2xl transition-all">
                                Manage Assignments
                            </button>
                            <button type="button" @click="$dispatch('close-modal', 'view-pcfs-modal')"
                                class="px-8 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white text-[10px] font-black uppercase tracking-[2px] rounded-2xl transition-all">
                                Close
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-modal>
    </div>
</x-app-layout>