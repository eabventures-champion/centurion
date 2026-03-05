<x-app-layout>
    <x-slot name="header">
        {{ __('Churches') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <!-- Full-width Churches List -->
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
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Existing Churches
                        </h3>
                        <p
                            class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                            Local
                            assemblies management</p>
                    </div>
                    <button type="button"
                        onclick="document.getElementById('createChurchModal').classList.remove('hidden')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-xl transition-all active:scale-95 shadow-xl shadow-indigo-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Church
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($groupedChurches as $groupId => $churchesInGroup)
                        @php $group = $churchesInGroup->first()->churchGroup; @endphp
                        <div class="rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
                            <button type="button"
                                onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                                class="w-full flex items-center justify-between p-5 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-all cursor-pointer">
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
                                            class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">{{ $churchesInGroup->count() }}
                                            {{ Str::plural('church', $churchesInGroup->count()) }}</span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 chevron" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="divide-y divide-slate-200 dark:divide-white/5">
                                @foreach($churchesInGroup as $church)
                                    <div
                                        class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5
                                                    class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $church->name }}
                                                </h5>
                                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                                    <span
                                                        class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $church->title }} {{ $church->leader_name }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] text-slate-400 dark:text-slate-500 font-medium flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ $church->leader_contact }}
                                                    </span>
                                                    @if($church->location)
                                                        <span
                                                            class="text-[10px] text-slate-400 dark:text-slate-500 font-medium flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            {{ $church->location }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1" x-data="{}">
                                            <button type="button"
                                                @click="$dispatch('open-church-modal', { id: {{ $church->id }} })"
                                                class="p-2 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                                title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('churches.edit', $church) }}"
                                                class="p-2 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                                title="Edit Church">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('churches.destroy', $church) }}" method="POST"
                                                onsubmit="return confirm('Are you sure?')" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-500 transition-colors"
                                                    title="Delete Church">
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
                            <p class="text-slate-500 italic">No churches found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Create Church Modal -->
    <div id="createChurchModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm"
                onclick="document.getElementById('createChurchModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 shadow-2xl rounded-3xl sm:my-8 border border-slate-200 dark:border-white/10 relative z-10">

                <!-- Modal Header -->
                <div
                    class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex items-center justify-between bg-slate-50 dark:bg-white/5">
                    <div>
                        <h3
                            class="text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            Add New Church
                        </h3>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1 ml-10">Create a
                            new local assembly</p>
                    </div>
                    <button onclick="document.getElementById('createChurchModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form action="{{ route('churches.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="modal_church_group_id" :value="__('Church Group')" />
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
                            <x-input-label for="modal_name" :value="__('Church Name')" />
                            <x-text-input type="text" name="name" id="modal_name" placeholder="e.g. CE Atomic" required
                                value="{{ old('name') }}" class="block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-1">
                                <x-input-label for="modal_title" :value="__('Title')" />
                                <select name="title" id="modal_title" required
                                    class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                    @foreach(['Bro', 'Sis', 'Pastor', 'Dcn', 'Dcns', 'Mr', 'Mrs'] as $title)
                                        <option value="{{ $title }}" {{ old('title') == $title ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="modal_leader_name" :value="__('Leader Name')" />
                                <x-text-input type="text" name="leader_name" id="modal_leader_name"
                                    placeholder="Full Name" required value="{{ old('leader_name') }}"
                                    class="block w-full" />
                                <x-input-error :messages="$errors->get('leader_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                            <div>
                                <x-input-label for="modal_location" :value="__('Location')" />
                                <x-text-input type="text" name="location" id="modal_location"
                                    placeholder="e.g. East Legon" value="{{ old('location') }}" class="block w-full" />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-secondary-button class="flex-1"
                                onclick="document.getElementById('createChurchModal').classList.add('hidden')">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit" id="modal-submit-btn" class="flex-1 justify-center">
                                {{ __('Create Church') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Church Details Modal -->
    <div x-data="{
            open: false,
            loading: false,
            church: null,
            pastor: null,
            async fetchDetails(id) {
                this.loading = true;
                this.open = true;
                try {
                    const response = await fetch(`/churches/${id}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();
                    this.church = data.church;
                    this.pastor = data.pastor;
                } catch (e) {
                    console.error('Failed to fetch details:', e);
                    alert('Could not load church details. Please try again.');
                    this.open = false;
                } finally {
                    this.loading = false;
                }
            }
        }" @open-church-modal.window="fetchDetails($event.detail.id)" x-show="open"
        class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm" @click="open = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-2xl overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 shadow-2xl rounded-3xl sm:my-8 border border-slate-200 dark:border-white/10">

                <div class="relative">
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex items-center justify-between bg-slate-50 dark:bg-white/5">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight"
                                x-text="church ? church.name : 'Loading...'"></h3>
                            <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mt-1"
                                x-text="church ? church.church_group.group_name : ''"></p>
                        </div>
                        <button @click="open = false"
                            class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <template x-if="loading">
                            <div class="flex flex-col items-center justify-center py-12 space-y-4">
                                <div
                                    class="w-12 h-12 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin">
                                </div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Fetching
                                    details...</p>
                            </div>
                        </template>

                        <template x-if="!loading && church">
                            <div class="space-y-8">
                                <!-- Church Details Section -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-6">
                                        <h4
                                            class="text-[11px] font-black text-slate-500 uppercase tracking-[2px] flex items-center gap-2">
                                            <span class="w-4 h-px bg-slate-200 dark:bg-white/10"></span> Church Info
                                        </h4>
                                        <div class="space-y-4">
                                            <div>
                                                <p
                                                    class="text-[9px] font-black text-slate-500 uppercase tracking-wider mb-1">
                                                    Venue</p>
                                                <p class="text-sm text-slate-900 dark:text-white font-medium"
                                                    x-text="church.venue || 'N/A'"></p>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[9px] font-black text-slate-500 uppercase tracking-wider mb-1">
                                                    Location</p>
                                                <p class="text-sm text-slate-900 dark:text-white font-medium"
                                                    x-text="church.location || 'N/A'"></p>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[9px] font-black text-slate-500 uppercase tracking-wider mb-1">
                                                    Category</p>
                                                <p class="text-sm text-slate-900 dark:text-white font-medium"
                                                    x-text="church.church_group.church_category.name"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <h4
                                            class="text-[11px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[2px] flex items-center gap-2">
                                            <span class="w-4 h-px bg-emerald-500/20"></span> Pastor Profile
                                        </h4>
                                        <div
                                            class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5">
                                            <div
                                                class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-white/10 flex items-center justify-center">
                                                <template x-if="pastor && pastor.profile_picture">
                                                    <img :src="'/storage/' + pastor.profile_picture"
                                                        class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!pastor || !pastor.profile_picture">
                                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold text-lg"
                                                        x-text="church.leader_name ? church.leader_name.substring(0, 2).toUpperCase() : '??'"></span>
                                                </template>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-[9px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-wider mb-1"
                                                    x-text="church.title"></p>
                                                <p class="text-sm text-slate-900 dark:text-white font-bold truncate"
                                                    x-text="church.leader_name"></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"
                                                    x-text="church.leader_contact">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="space-y-4 px-2" x-show="pastor">
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Email</span>
                                                <span class="text-xs text-slate-900 dark:text-white"
                                                    x-text="pastor ? pastor.email : 'N/A'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Gender</span>
                                                <span class="text-xs text-slate-900 dark:text-white"
                                                    x-text="pastor ? pastor.gender : 'N/A'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Birthday</span>
                                                <span class="text-xs text-slate-900 dark:text-white"
                                                    x-text="pastor ? pastor.birth_day : 'N/A'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Occupation</span>
                                                <span class="text-xs text-slate-900 dark:text-white"
                                                    x-text="pastor ? pastor.occupation : 'N/A'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="px-8 py-5 border-t border-slate-200 dark:border-white/5 bg-slate-100/50 dark:bg-white/5 flex justify-end">
                        <x-secondary-button @click="open = false">
                            {{ __('Close') }}
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
                const response = await fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_type=church`);
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

        // Auto-open modal if there are validation errors
        @if($errors->any())
            document.getElementById('createChurchModal').classList.remove('hidden');
        @endif
    </script>
</x-app-layout>