<x-app-layout>
    <x-slot name="header">
        {{ __('First Timers') }}
    </x-slot>

    <div class="py-6" x-data="{ 
        search: '', 
        status: 'all', 
        gender: 'all',
        churchFilter: 'all',
        pcfFilter: 'all',
        matches(ft) {
            const s = this.search.toLowerCase();
            const nameMatch = !s || ft.name.toLowerCase().includes(s) || ft.contact.includes(s) || (ft.pcf_name && ft.pcf_name.toLowerCase().includes(s));
            const statusMatch = this.status === 'all' || 
                               (this.status === 'retained' && ft.locked) || 
                               (this.status === 'pending' && !ft.locked);
            const genderMatch = this.gender === 'all' || ft.gender === this.gender;
            const churchMatch = this.churchFilter === 'all' || ft.church_id == this.churchFilter;
            const pcfMatch = this.pcfFilter === 'all' || ft.pcf_id == this.pcfFilter;
            return nameMatch && statusMatch && genderMatch && churchMatch && pcfMatch;
        },
        anyVisible(items) {
            return items.some(i => this.matches(i));
        }
    }">
        <div class="max-w-7xl mx-auto px-4" x-init="
            $watch('search', value => { if(value) open = true });
            $watch('churchFilter', value => { if(value !== 'all') open = true });
            $watch('pcfFilter', value => { if(value !== 'all') open = true });
        ">
            <!-- Header Section -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">First Timers
                            Register</h3>
                        @php
                            $user = auth()->user();
                            if ($user->hasRole('Super Admin')) {
                                $ftCount = \App\Models\FirstTimer::count();
                            } elseif ($user->hasRole('Official')) {
                                $officialPcfIds = $user->pcfs()->pluck('id');
                                $ftCount = \App\Models\FirstTimer::whereIn('pcf_id', $officialPcfIds)->count();
                            } else {
                                $ftCount = $user->church() ? \App\Models\FirstTimer::where('church_id', $user->church()->id)->orWhereIn('pcf_id', \App\Models\Pcf::where('church_group_id', $user->church()->church_group_id)->pluck('id'))->count() : 0;
                            }
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/20 text-[9px] font-black uppercase tracking-widest shadow-sm shadow-amber-500/10">
                            {{ $ftCount }}
                        </span>
                    </div>
                    <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                        Manage and track new first timer retention
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('first-timers.export.excel') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-emerald-600/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                    <a href="{{ route('first-timers.export.pdf') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-rose-600/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </a>
                    <a href="{{ route('first-timers.create') }}"
                        class="flex items-center justify-center gap-2.5 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-2xl transition-all active:scale-95 shadow-xl shadow-indigo-600/30 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        Register New
                    </a>
                </div>

            </div>

            <div class="glass-card mb-6 p-4 border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50">
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Search -->
                    <div class="relative flex-1 group min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-500 group-focus-within:text-indigo-400 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Search by name, phone number..."
                            class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl py-3 pl-12 text-sm text-slate-900 dark:text-white placeholder-slate-500 transition-all font-medium">
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap md:flex-nowrap gap-3 items-center">
                        <div class="relative min-w-[140px]">
                            <select x-model="status"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-2xl py-3 px-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                <option value="all">Status</option>
                                <option value="pending">Pending</option>
                                <option value="retained">Retained</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="relative min-w-[140px]">
                            <select x-model="gender"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-2xl py-3 px-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                <option value="all">Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Official'))
                            @if(auth()->user()->hasRole('Super Admin'))
                                <div class="relative min-w-[160px]">
                                    <select x-model="churchFilter"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-2xl py-3 px-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                        <option value="all">All Churches</option>
                                        @foreach($availableChurches as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endif
 
                            <div class="relative min-w-[160px]">
                                <select x-model="pcfFilter"
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-2xl py-3 px-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                    <option value="all">All PCFs</option>
                                    @foreach($availablePcfs as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif

                        <button
                            @click="search = ''; status = 'all'; gender = 'all'; churchFilter = 'all'; pcfFilter = 'all'"
                            title="Reset Filters"
                            class="px-4 py-3 bg-slate-200 dark:bg-slate-900 hover:bg-slate-300 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-white rounded-2xl transition-all active:scale-95 border border-slate-300 dark:border-white/5 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                @if(!auth()->user()->hasRole('Super Admin'))
                    {{-- Admin: Flat table without hierarchy --}}
                    @php
                        $allFlatFirstTimers = collect($groupedFirstTimers)->flatMap(function ($groups) {
                            return collect($groups)->flatMap(function ($entities) {
                                return collect($entities)->flatten(1);
                            });
                        });
                    @endphp
                    @if($allFlatFirstTimers->count() > 0)
                        <div class="glass-card overflow-hidden border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-white/5">
                                    <thead>
                                        <tr
                                            class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800/50 bg-slate-50/50 dark:bg-white/5">
                                            <th class="px-6 py-3 text-left">Name</th>
                                            <th class="px-6 py-3 text-left">PCF</th>
                                            <th class="px-6 py-3 text-left">Contact</th>
                                            <th class="px-6 py-3 text-left">Visits</th>
                                            <th class="px-6 py-3 text-left">Status</th>
                                            <th class="px-6 py-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                        @foreach($allFlatFirstTimers as $ft)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group" x-show="matches({
                                                                                        name: @js($ft->full_name),
                                                                                        contact: @js($ft->primary_contact),
                                                                                        locked: {{ $ft->locked ? 'true' : 'false' }},
                                                                                        gender: @js($ft->gender),
                                                                                        church_id: @js($ft->church_id),
                                                                                        pcf_id: @js($ft->pcf_id),
                                                                                        pcf_name: @js($ft->pcf->name ?? '')
                                                                                    })">
                                                <td
                                                    class="px-6 py-3 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $ft->full_name }}
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded-md bg-indigo-500/5 text-indigo-600 dark:text-indigo-400 border border-indigo-500/10 text-[10px] font-bold">
                                                        {{ $ft->pcf->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-mono">
                                                    {{ $ft->primary_contact }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <button @click="$dispatch('view-visit-history', {
                                                                                        name: '{{ $ft->full_name }}',
                                                                                        initial: '{{ $ft->earliest_visit_date ? \Carbon\Carbon::parse($ft->earliest_visit_date)->format('M d, Y') : 'N/A' }}',
                                                                                        logs: {{ $ft->attendanceLogs->sortBy('service_date')->map(fn($l) => ['date' => $l->service_date->format('M d, Y')])->values()->toJson() }}
                                                                                    })"
                                                        class="px-2.5 py-1 text-[9px] font-black rounded-lg transition-all {{ $ft->service_count >= 3 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500/20' }}">
                                                        {{ $ft->service_count }} / 3
                                                    </button>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap text-sm">
                                                    @if($ft->locked)
                                                        <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                    d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Retained
                                                        </span>
                                                    @else
                                                        <span class="text-amber-600 dark:text-amber-500/80 font-bold text-xs tracking-wide">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium">
                                                    <div class="flex items-center justify-end gap-3">
                                                        <button @click="$dispatch('view-visitor', {{ $ft->id }})"
                                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors font-bold uppercase tracking-wider text-[9px]">View</button>
                                                        <a href="{{ route('first-timers.edit', $ft) }}"
                                                            class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors font-bold uppercase tracking-wider text-[9px]">Edit</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="glass-card p-12 flex flex-col items-center justify-center text-center">
                            <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-bold text-white mb-1">No First Timers Found</h3>
                            <p class="text-sm text-slate-500">There are no first timers registered for your church yet.</p>
                        </div>
                    @endif
                @else
                    @forelse($groupedFirstTimers as $category => $groups)
                        @php
                            $categoryItems = collect($groups)->flatten(2)->map(fn($f) => [
                                'name' => $f->full_name,
                                'contact' => $f->primary_contact,
                                'locked' => $f->locked,
                                'gender' => $f->gender,
                                'church_id' => $f->church_id,
                                'pcf_id' => $f->pcf_id
                            ]);
                            $categoryFTCount = $categoryItems->count();
                        @endphp
                        <div class="glass-card overflow-hidden border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50" x-data="{ open: true, items: @js($categoryItems) }"
                            x-init="$watch('search', value => { if(value) open = true })" x-show="anyVisible(items)">
                            <button @click="open = !open"
                                class="w-full text-left bg-indigo-50 dark:bg-indigo-900/40 px-5 py-4 flex items-center justify-between transition-colors group/cat"
                                :class="open ? 'border-b border-indigo-200 dark:border-indigo-500/20' : 'rounded-b-2xl'">
                                <h4
                                    class="text-[11px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-3">
                                    <svg class="w-4 h-4 text-indigo-400 transition-transform duration-300"
                                        :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                    {{ $category }}
                                </h4>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-2.5 py-1 bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-black group-hover/cat:bg-indigo-500/20 transition-colors">{{ $categoryFTCount }}
                                        First Timers</span>
                                </div>
                            </button>

                            <div x-show="open" x-collapse>
                                <div class="p-4 space-y-6">
                                    @foreach($groups as $groupName => $entities)
                                        @php
                                            $groupItems = collect($entities)->flatten(1)->map(fn($f) => [
                                                'name' => $f->full_name,
                                                'contact' => $f->primary_contact,
                                                'locked' => $f->locked,
                                                'gender' => $f->gender,
                                                'church_id' => $f->church_id,
                                                'pcf_id' => $f->pcf_id
                                            ]);
                                            $groupFTCount = $groupItems->count();
                                        @endphp
                                        <div class="space-y-4" x-data="{ open: false, items: @js($groupItems) }"
                                            x-init="$watch('search', value => { if(value) open = true })"
                                            x-show="anyVisible(items)">
                                            <button @click="open = !open" class="w-full flex items-center gap-2 group/grp">
                                                <div
                                                    class="h-px bg-slate-200 dark:bg-slate-700 flex-1 transition-colors group-hover/grp:bg-emerald-500/50">
                                                </div>
                                                <div class="flex items-center gap-3 px-3">
                                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 transition-transform duration-300"
                                                        :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                            d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                    <h5 class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                                                        {{ $groupName }}
                                                    </h5>
                                                    <div class="flex items-center gap-2 ml-1 cursor-default" @click.stop>
                                                        <span
                                                            class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md text-[9px] font-black">{{ $groupFTCount }}
                                                            First Timers</span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="h-px bg-slate-200 dark:bg-slate-700 flex-1 transition-colors group-hover/grp:bg-emerald-500/50">
                                                </div>
                                            </button>

                                            <div x-show="open" x-collapse>
                                                <div class="space-y-4 pl-0 md:pl-4">
                                                    @foreach($entities as $entityName => $firstTimers)
                                                        @php
                                                            $entityItems = collect($firstTimers)->map(fn($f) => [
                                                                'name' => $f->full_name,
                                                                'contact' => $f->primary_contact,
                                                                'locked' => $f->locked,
                                                                'gender' => $f->gender,
                                                                'church_id' => $f->church_id,
                                                                'pcf_id' => $f->pcf_id
                                                            ]);
                                                            $entityFTCount = $entityItems->count();
                                                        @endphp
                                                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden"
                                                            x-data="{ open: true, items: @js($entityItems) }"
                                                            x-init="$watch('search', value => { if(value) open = true })"
                                                            x-show="anyVisible(items)">
                                                            <button @click="open = !open"
                                                                class="w-full bg-slate-100 dark:bg-slate-800/50 px-4 py-2 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center focus:outline-none transition-colors group/ent">
                                                                <div class="flex items-center gap-2.5">
                                                                    <svg class="w-3 h-3 text-slate-400 transition-transform duration-300 group-hover/ent:text-slate-900 dark:group-hover:ent:text-white"
                                                                        :class="open ? 'rotate-90' : ''" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="3" d="M9 5l7 7-7 7"></path>
                                                                    </svg>
                                                                    <h6
                                                                        class="text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider group-hover/ent:text-slate-900 dark:group-hover/ent:text-white transition-colors">
                                                                        {{ $entityName }}
                                                                    </h6>
                                                                </div>
                                                                <div class="flex items-center gap-3 cursor-default" @click.stop>
                                                                    <span
                                                                        class="px-2 py-0.5 bg-slate-200 dark:bg-slate-950 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-800 rounded-md text-[9px] font-black shadow-inner shadow-black/5">{{ $entityFTCount }}
                                                                        First Timers</span>
                                                                </div>
                                                            </button>

                                                            <div x-show="open" x-collapse>
                                                                <div class="overflow-x-auto">
                                                                    <table class="min-w-full divide-y divide-slate-800/50">
                                                                        <thead>
                                                                            <tr
                                                                                class="text-[9px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800/50 bg-slate-50/50 dark:bg-white/5">
                                                                                <th class="px-6 py-3 text-left">Name</th>
                                                                                <th class="px-6 py-3 text-left">Contact</th>
                                                                                <th class="px-6 py-3 text-left">Visits</th>
                                                                                <th class="px-6 py-3 text-left">Status</th>
                                                                                <th class="px-6 py-3 text-right">Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800/30">
                                                                            @foreach($firstTimers as $ft)
                                                                                <tr class="hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group"
                                                                                    x-show="matches({ 
                                                                                                                                                        name: @js($ft->full_name), 
                                                                                                                                                        contact: @js($ft->primary_contact), 
                                                                                                                                                        locked: {{ $ft->locked ? 'true' : 'false' }}, 
                                                                                                                                                        gender: @js($ft->gender),
                                                                                                                                                        church_id: @js($ft->church_id),
                                                                                                                                                        pcf_id: @js($ft->pcf_id)
                                                                                                                                                    })">
                                                                                    <td
                                                                                        class="px-6 py-3 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                                                        {{ $ft->full_name }}
                                                                                    </td>
                                                                                    <td
                                                                                        class="px-6 py-3 whitespace-nowrap text-sm text-slate-500 font-mono">
                                                                                        {{ $ft->primary_contact }}
                                                                                    </td>
                                                                                    <td class="px-6 py-3 whitespace-nowrap text-sm">
                                                                                        <button
                                                                                            @click="$dispatch('view-visit-history', { 
                                                                                                                                                 name: '{{ $ft->full_name }}', 
                                                                                                                                                 initial: '{{ $ft->earliest_visit_date ? \Carbon\Carbon::parse($ft->earliest_visit_date)->format('M d, Y') : 'N/A' }}',
                                                                                                                                                 logs: {{ $ft->attendanceLogs->sortBy('service_date')->map(fn($l) => ['date' => $l->service_date->format('M d, Y')])->values()->toJson() }}
                                                                                                                                             })"
                                                                                            class="px-2.5 py-1 text-[9px] font-black rounded-lg transition-all {{ $ft->service_count >= 3 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500/20' }}">
                                                                                            {{ $ft->service_count }} / 3
                                                                                        </button>
                                                                                    </td>
                                                                                    <td class="px-6 py-3 whitespace-nowrap text-sm">
                                                                                        @if($ft->locked)
                                                                                            <span
                                                                                                class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                                                                                <svg class="w-4 h-4" fill="none"
                                                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round"
                                                                                                        stroke-linejoin="round" stroke-width="3"
                                                                                                        d="M5 13l4 4L19 7" />
                                                                                                </svg>
                                                                                                Retained
                                                                                            </span>
                                                                                        @else
                                                                                            <span
                                                                                                class="text-amber-600 dark:text-amber-500/80 font-bold text-xs tracking-wide">Pending</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td
                                                                                        class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium">
                                                                                        <div class="flex items-center justify-end gap-3">
                                                                                            <button
                                                                                                @click="$dispatch('view-visitor', {{ $ft->id }})"
                                                                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors font-bold uppercase tracking-wider text-[9px]">View</button>
                                                                                            <a href="{{ route('first-timers.edit', $ft) }}"
                                                                                                class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors font-bold uppercase tracking-wider text-[9px]">Edit</a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="glass-card p-12 flex flex-col items-center justify-center text-center border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50">
                            <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">No First Timers Found</h3>
                            <p class="text-sm text-slate-500">There are no first timers registered in the system yet.</p>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>
    </div>

    <!-- Visitor Detail Modal -->
    <div x-data="{ 
            open: false, 
            visitor: null,
            loading: false,
            init() {
                window.addEventListener('view-visitor', async (e) => {
                    this.loading = true;
                    this.open = true;
                    try {
                        const response = await fetch(`/first-timers/${e.detail}`);
                        this.visitor = await response.json();
                    } catch (error) {
                        console.error('Error fetching visitor:', error);
                    } finally {
                        this.loading = false;
                    }
                });
            }
        }" x-show="open" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm" @click="open = false"></div>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-2xl px-6 py-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 shadow-2xl rounded-3xl">

                <template x-if="loading">
                    <div class="flex flex-col items-center justify-center py-12">
                        <div
                            class="w-10 h-10 border-4 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin mb-4">
                        </div>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Loading Details...
                        </p>
                    </div>
                </template>

                <template x-if="!loading && visitor">
                    <div class="space-y-8">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight" x-text="visitor.full_name">
                                </h3>
                                <p class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[2px] mt-1"
                                    x-text="visitor.church?.name || visitor.pcf?.name || 'Unassigned'"></p>
                            </div>
                            <button @click="open = false" class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Primary
                                        Contact</label>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white font-mono" x-text="visitor.primary_contact">
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Email</label>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="visitor.email || 'N/A'"></p>
                                </div>
                                <div>
                                    <label
                                        class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Date
                                        of Visit</label>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white"
                                        x-text="new Date(visitor.earliest_visit_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })">
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Address</label>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300 leading-relaxed"
                                        x-text="visitor.residential_address"></p>
                                </div>
                                <div>
                                    <label
                                        class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Occupation</label>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="visitor.occupation || 'N/A'"></p>
                                </div>
                                <div>
                                    <label
                                        class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Marital
                                        Status</label>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="visitor.marital_status"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-200 dark:border-white/5">
                            <div class="space-y-4">
                                <label
                                    class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Spiritual
                                    Status</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-if="visitor.born_again">
                                        <span
                                            class="px-2 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md text-[9px] font-black uppercase tracking-wider">Born
                                            Again</span>
                                    </template>
                                    <template x-if="visitor.water_baptism">
                                        <span
                                            class="px-2 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-md text-[9px] font-black uppercase tracking-wider">Water
                                            Baptism</span>
                                    </template>
                                    <template x-if="!visitor.born_again && !visitor.water_baptism">
                                        <span
                                            class="text-[10px] text-slate-500 font-bold uppercase tracking-wider italic">None
                                            Recorded</span>
                                    </template>
                                </div>
                            </div>
                            <div x-show="visitor.prayer_requests" class="space-y-2">
                                <label
                                    class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Prayer
                                    Requests</label>
                                <div class="p-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-white/5">
                                    <p class="text-xs text-slate-600 dark:text-slate-400 italic leading-relaxed"
                                        x-text="visitor.prayer_requests"></p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-200 dark:border-white/5" x-show="visitor.bringer">
                            <label
                                class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Brought
                                By</label>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-600/20 text-emerald-400 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400" x-text="visitor.bringer?.name"></p>
                                    <p class="text-[9px] text-slate-500 dark:text-slate-400 font-bold mt-0.5"
                                        x-text="visitor.bringer?.contact"></p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-200 dark:border-white/5">
                            <label
                                class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Visit
                                History</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template
                                    x-for="(log, index) in visitor.attendance_logs.sort((a,b) => new Date(a.service_date) - new Date(b.service_date))"
                                    :key="log.id">
                                    <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
                                        <p class="text-[8px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1"
                                            x-text="'Visit ' + (index + 1)"></p>
                                        <p class="text-xs font-bold text-slate-900 dark:text-white"
                                            x-text="new Date(log.service_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })">
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Visit History Modal -->
    <div x-data="{ 
            open: false, 
            data: null,
            init() {
                window.addEventListener('view-visit-history', (e) => {
                    this.data = e.detail;
                    this.open = true;
                });
            }
        }" x-show="open" class="fixed inset-0 z-[110] overflow-y-auto" style="display: none;">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm" @click="open = false"></div>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-sm px-6 py-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 shadow-2xl rounded-3xl">

                <div class="space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Visit
                                History</p>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight" x-text="data?.name"></h3>
                        </div>
                        <button @click="open = false" class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(log, index) in data?.logs" :key="index">
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-white/5 rounded-2xl flex items-center justify-between">
                                <div>
                                    <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-0.5"
                                        x-text="'Service Visit ' + (index + 1)"></p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="log.date"></p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-4">
                        <button @click="open = false"
                            class="w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white text-[9px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95">
                            Close History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>