<x-app-layout>
    <x-slot name="header">
        {{ __('PCFs (Zonal Units)') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <!-- Full-width PCFs List -->
            <div class="glass-card p-5">
                @if(session('success'))
                    <div
                        class="flash-alert mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div
                        class="flash-alert mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Existing PCFs</h3>
                        <p
                            class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                            Zonal church
                            layout management</p>
                    </div>
                    <button type="button" onclick="document.getElementById('createPcfModal').classList.remove('hidden')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-xl transition-all active:scale-95 shadow-xl shadow-indigo-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New PCF
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($groupedPcfs as $groupId => $pcfsInGroup)
                        @php $group = $pcfsInGroup->first()->churchGroup; @endphp
                        <div class="rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
                            <button type="button"
                                onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                                class="w-full flex items-center justify-between p-5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-all cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $group->group_name }}
                                        </h4>
                                        <span
                                            class="text-[10px] text-slate-500 dark:text-slate-400">{{ $pcfsInGroup->count() }}
                                            {{ Str::plural('PCF', $pcfsInGroup->count()) }}</span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 chevron" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="divide-y divide-slate-200 dark:divide-white/5">
                                @foreach($pcfsInGroup as $pcf)
                                    <div
                                        class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-sm font-bold text-slate-900 dark:text-white">{{ $pcf->name }}
                                                </h5>
                                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                                    <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $pcf->leader_name }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-500 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ $pcf->leader_contact }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button type="button" onclick="viewPcf({{ $pcf->id }})"
                                                class="p-2 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"
                                                title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('pcfs.edit', $pcf) }}"
                                                class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                                title="Edit PCF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('pcfs.destroy', $pcf) }}" method="POST"
                                                onsubmit="return confirm('Are you sure?')" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-slate-500 italic">No PCFs found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Create PCF Modal -->
    <div id="createPcfModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm"
                onclick="document.getElementById('createPcfModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 shadow-2xl rounded-3xl sm:my-8 border border-slate-200 dark:border-white/10 relative z-10">

                <!-- Modal Header -->
                <div
                    class="px-8 py-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between bg-slate-50 dark:bg-white/5">
                    <div>
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            Add New PCF
                        </h3>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1 ml-10">Create a
                            new zonal unit</p>
                    </div>
                    <button onclick="document.getElementById('createPcfModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form action="{{ route('pcfs.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="modal_church_group_id" :value="__('Church Group (Zonal)')" />
                            <select name="church_group_id" id="modal_church_group_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                <option value="">Select Group</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('church_group_id') == $group->id ? 'selected' : '' }}>{{ $group->group_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('church_group_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modal_name" :value="__('PCF Name')" />
                            <x-text-input type="text" name="name" id="modal_name" placeholder="e.g. PCF ALPHA" required
                                value="{{ old('name') }}" class="block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modal_leader_name" :value="__('Leader Name')" />
                            <x-text-input type="text" name="leader_name" id="modal_leader_name" placeholder="Full Name"
                                required value="{{ old('leader_name') }}" class="block w-full" />
                            <x-input-error :messages="$errors->get('leader_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modal_leader_contact" :value="__('Leader Contact')" />
                            <x-text-input type="text" name="leader_contact" id="modal_leader_contact"
                                placeholder="Phone Number" required value="{{ old('leader_contact') }}"
                                class="block w-full" />
                            <div id="modal-contact-warning"
                                class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden italic">
                            </div>
                            <x-input-error :messages="$errors->get('leader_contact')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="modal_gender" :value="__('Gender')" />
                                <select name="gender" id="modal_gender" required
                                    class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="modal_marital_status" :value="__('Marital Status')" />
                                <select name="marital_status" id="modal_marital_status" required
                                    class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                    <option value="">Select Status</option>
                                    @foreach(['Single', 'Married'] as $status)
                                        <option value="{{ $status }}" {{ old('marital_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="modal_occupation" :value="__('Occupation')" />
                            <x-text-input type="text" name="occupation" id="modal_occupation"
                                placeholder="e.g. Engineer" required value="{{ old('occupation') }}"
                                class="block w-full" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modal_official_id" :value="__('Official In Charge')" />
                            <select name="official_id" id="modal_official_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                <option value="">Select Official</option>
                                @foreach($officials as $official)
                                    <option value="{{ $official->id }}" {{ old('official_id', $defaultOfficialId) == $official->id ? 'selected' : '' }}>{{ $official->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('official_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-secondary-button class="flex-1"
                                onclick="document.getElementById('createPcfModal').classList.add('hidden')">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit" id="modal-submit-btn" class="flex-1 justify-center">
                                {{ __('Create PCF') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View PCF Modal -->
    <div id="viewPcfModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm"
                onclick="document.getElementById('viewPcfModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 shadow-2xl rounded-3xl sm:my-8 border border-slate-200 dark:border-white/10 relative z-10">

                <!-- Modal Header -->
                <div
                    class="px-8 py-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between bg-slate-50 dark:bg-white/5">
                    <div>
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-600/20 text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                            PCF Details
                        </h3>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1 ml-10">Detailed
                            zonal unit information</p>
                    </div>
                    <button onclick="document.getElementById('viewPcfModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <span
                                class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">PCF
                                Name</span>
                            <p id="view_pcf_name" class="text-sm font-bold text-slate-900 dark:text-white"></p>
                        </div>
                        <div class="space-y-1">
                            <span
                                class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Church
                                Group</span>
                            <p id="view_church_group" class="text-sm font-bold text-slate-900 dark:text-white"></p>
                        </div>
                    </div>

                    <div class="h-px bg-slate-200 dark:bg-white/5"></div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Leader
                                Name</span>
                            <p id="view_leader_name" class="text-sm font-bold text-indigo-400"></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Contact</span>
                            <p id="view_leader_contact" class="text-sm font-bold text-slate-900 dark:text-white"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div
                            class="space-y-1 p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-tight">Gender</span>
                            <p id="view_gender" class="text-xs font-bold text-slate-900 dark:text-white"></p>
                        </div>
                        <div
                            class="space-y-1 p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-tight">Marital
                                Status</span>
                            <p id="view_marital_status" class="text-xs font-bold text-slate-900 dark:text-white"></p>
                        </div>
                        <div
                            class="space-y-1 p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
                            <span
                                class="text-[9px] font-black text-slate-500 uppercase tracking-tight">Occupation</span>
                            <p id="view_occupation" class="text-xs font-bold text-slate-900 dark:text-white truncate">
                            </p>
                        </div>
                    </div>

                    <div class="h-px bg-slate-200 dark:bg-white/5"></div>

                    <div class="p-4 bg-indigo-600/10 rounded-2xl border border-indigo-500/20">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Official in
                            Charge</span>
                        <p id="view_official_name" class="text-sm font-bold text-slate-900 dark:text-white mt-1"></p>
                    </div>

                    <div class="pt-4">
                        <x-secondary-button class="w-full justify-center"
                            onclick="document.getElementById('viewPcfModal').classList.add('hidden')">
                            {{ __('Close Viewer') }}
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const modalContactInput = document.getElementById('modal_leader_contact');
        const modalWarningDiv = document.getElementById('modal-contact-warning');
        const modalSubmitBtn = document.getElementById('modal-submit-btn');

        const checkContact = debounce(async (contact) => {
            if (contact.length < 5) {
                modalWarningDiv.classList.add('hidden');
                modalSubmitBtn.disabled = false;
                modalSubmitBtn.style.opacity = '1';
                return;
            }

            try {
                const response = await fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_type=pcf`);
                const data = await response.json();

                if (data.exists) {
                    modalWarningDiv.textContent = `⚠️ This contact belongs to ${data.owner} in ${data.entity}`;
                    modalWarningDiv.classList.remove('hidden');
                    modalSubmitBtn.disabled = true;
                    modalSubmitBtn.style.opacity = '0.5';
                } else {
                    modalWarningDiv.classList.add('hidden');
                    modalSubmitBtn.disabled = false;
                    modalSubmitBtn.style.opacity = '1';
                }
            } catch (error) {
                console.error('Error checking contact:', error);
            }
        }, 500);

        modalContactInput.addEventListener('input', (e) => checkContact(e.target.value));

        async function viewPcf(id) {
            try {
                const response = await fetch(`/pcfs/${id}`);
                const pcf = await response.json();

                document.getElementById('view_pcf_name').textContent = pcf.name;
                document.getElementById('view_church_group').textContent = pcf.church_group.group_name;
                document.getElementById('view_leader_name').textContent = pcf.leader_name;
                document.getElementById('view_leader_contact').textContent = pcf.leader_contact;
                document.getElementById('view_gender').textContent = pcf.gender || '—';
                document.getElementById('view_marital_status').textContent = pcf.marital_status || '—';
                document.getElementById('view_occupation').textContent = pcf.occupation || '—';
                document.getElementById('view_official_name').textContent = pcf.official ? pcf.official.name : 'No official assigned';

                document.getElementById('viewPcfModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error fetching PCF details:', error);
                alert('Failed to load PCF details.');
            }
        }

        // Auto-open modal if there are validation errors
        @if($errors->any())
            document.getElementById('createPcfModal').classList.remove('hidden');
        @endif
    </script>
</x-app-layout>