<x-app-layout>
    <x-slot name="header">
        {{ __('Bringers Directory') }}
    </x-slot>

    <div class="py-10" x-data="{ 
        search: '', 
        churchFilter: 'all',
        pcfFilter: 'all',
        matches(bringer) {
            const s = this.search.toLowerCase();
            const nameMatch = !s || bringer.name.toLowerCase().includes(s) || bringer.contact.includes(s);
            
            // Bringer matches locations based on their assigned PCF or Church
            const churchMatch = this.churchFilter === 'all' || bringer.church_id == parseInt(this.churchFilter);
            const pcfMatch = this.pcfFilter === 'all' || bringer.pcf_id == parseInt(this.pcfFilter);
            
            return nameMatch && churchMatch && pcfMatch;
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
            <div class="mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Bringers Register
                        </h3>
                        @php
                            $user = auth()->user();
                            if ($user->hasRole('Super Admin')) {
                                $bCount = \App\Models\Bringer::count();
                            } elseif ($user->hasRole('Official')) {
                                $officialPcfIds = $user->pcfs()->pluck('id');
                                $bCount = \App\Models\Bringer::whereIn('pcf_id', $officialPcfIds)->count();
                            } else {
                                $church = $user->church();
                                $pcfIds = $church ? \App\Models\Pcf::where('church_group_id', $church->church_group_id)->pluck('id') : collect();
                                $bCount = $church ? \App\Models\Bringer::where(function ($q) use ($church, $pcfIds) {
                                    $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
                                })->count() : 0;
                            }
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm shadow-amber-500/10">
                            {{ $bCount }}
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                        Directory of all members who have brought first timers
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('bringers.export.excel') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-emerald-600/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                    <a href="{{ route('bringers.export.pdf') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-rose-600/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>


            <!-- Enhanced Search & Filter Bar -->
            <div class="glass-card mb-8 p-6 border border-white/5">
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
                        <input type="text" x-model="search" placeholder="Search by bringer name or contact..."
                            class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl py-4 pl-14 text-sm text-slate-900 dark:text-white placeholder-slate-500 transition-all font-medium">
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap md:flex-nowrap gap-3 items-center">
                        <div class="relative min-w-[200px]">
                            <select x-model="churchFilter"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-2xl py-4 px-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
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

                        <div class="relative min-w-[200px]">
                            <select x-model="pcfFilter"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-2xl py-4 px-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
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

                        <button @click="search = ''; churchFilter = 'all'; pcfFilter = 'all'" title="Reset Filters"
                            class="px-5 py-4 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-2xl transition-all active:scale-95 border border-slate-200 dark:border-white/5 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                @forelse($groupedBringers as $category => $groups)
                    @php
                        $categoryBringers = collect($groups)->flatten(2);
                        $categorySummary = $categoryBringers->map(fn($b) => [
                            'name' => $b->name,
                            'contact' => $b->contact,
                            'church_id' => $b->church_id,
                            'pcf_id' => $b->pcf_id
                        ]);
                        $categoryBringersCount = $categoryBringers->count();
                        $categorySoulsCount = $categoryBringers->sum('first_timers_count');
                    @endphp
                    <div class="glass-card overflow-hidden" x-data="{ open: true }"
                        x-show="anyVisible({{ $categorySummary->toJson() }})">
                        <button @click="open = !open"
                            class="w-full text-left bg-indigo-50/50 dark:bg-indigo-900/40 px-6 py-4 flex items-center justify-between transition-colors group/cat"
                            :class="open ? 'border-b border-slate-200 dark:border-indigo-500/20' : 'rounded-b-2xl'">
                            <h4
                                class="text-sm font-black text-indigo-400 uppercase tracking-widest flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-400 transition-transform duration-300"
                                    :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                                {{ $category }}
                            </h4>
                            <div class="flex items-center gap-3">
                                <span
                                    class="px-2.5 py-1 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-black group-hover/cat:bg-indigo-500/20 transition-colors">{{ $categoryBringersCount }}
                                    Bringers</span>
                                <span
                                    class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black shadow-inner shadow-black/5 dark:shadow-black/50">{{ $categorySoulsCount }}
                                    Souls</span>
                            </div>
                        </button>

                        <div x-show="open" x-collapse>
                            <div class="p-6 space-y-6">
                                @foreach($groups as $groupName => $entities)
                                    @php
                                        $groupBringers = collect($entities)->flatten(1);
                                        $groupSummary = $groupBringers->map(fn($b) => [
                                            'name' => $b->name,
                                            'contact' => $b->contact,
                                            'church_id' => $b->church_id,
                                            'pcf_id' => $b->pcf_id
                                        ]);
                                        $groupBringersCount = $groupBringers->count();
                                        $groupSoulsCount = $groupBringers->sum('total_souls_count');
                                    @endphp
                                    <div class="space-y-4" x-data="{ open: false }"
                                        x-show="anyVisible({{ $groupSummary->toJson() }})">
                                        <button @click="open = !open" class="w-full flex items-center gap-2 group/grp">
                                            <div
                                                class="h-px bg-slate-200 dark:bg-slate-700 flex-1 transition-colors group-hover/grp:bg-emerald-500/50">
                                            </div>
                                            <div class="flex items-center gap-3 px-3">
                                                <svg class="w-3.5 h-3.5 text-emerald-400 transition-transform duration-300"
                                                    :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                <h5 class="text-xs font-black text-emerald-400 uppercase tracking-widest">
                                                    {{ $groupName }}
                                                </h5>
                                                <div class="flex items-center gap-2 ml-1 cursor-default" @click.stop>
                                                    <span
                                                        class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-md text-[9px] font-black">{{ $groupBringersCount }}
                                                        Bringers</span>
                                                    <span
                                                        class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-md text-[9px] font-black shadow-inner shadow-black/5 dark:shadow-black/50">{{ $groupSoulsCount }}
                                                        Souls</span>
                                                </div>
                                            </div>
                                            <div
                                                class="h-px bg-slate-700 flex-1 transition-colors group-hover/grp:bg-emerald-500/50">
                                            </div>
                                        </button>

                                        <div x-show="open" x-collapse>
                                            <div class="space-y-4 pl-0 md:pl-4">
                                                @foreach($entities as $entityName => $bringers)
                                                    @php
                                                        $entityBringers = collect($bringers);
                                                        $entitySummary = $entityBringers->map(fn($b) => [
                                                            'name' => $b->name,
                                                            'contact' => $b->contact,
                                                            'church_id' => $b->church_id,
                                                            'pcf_id' => $b->pcf_id
                                                        ]);
                                                        $entityBringersCount = $entityBringers->count();
                                                        $entitySoulsCount = $entityBringers->sum('total_souls_count');
                                                    @endphp
                                                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                                                        x-show="anyVisible({{ $entitySummary->toJson() }})" x-data="{ open: true }">
                                                        <button @click="open = !open"
                                                            class="w-full bg-white dark:bg-slate-800/50 px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center focus:outline-none transition-colors group/ent">
                                                            <div class="flex items-center gap-2.5">
                                                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-300 group-hover/ent:text-slate-900 dark:group-hover:text-white"
                                                                    :class="open ? 'rotate-90' : ''" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M9 5l7 7-7 7"></path>
                                                                </svg>
                                                                <h6
                                                                    class="text-[11px] font-bold text-slate-300 uppercase tracking-wider group-hover/ent:text-slate-900 dark:group-hover:text-white transition-colors">
                                                                    {{ $entityName }}
                                                                </h6>
                                                            </div>
                                                            <div class="flex items-center gap-3 cursor-default" @click.stop>
                                                                <span
                                                                    class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ $entityBringersCount }}
                                                                    Bringers</span>
                                                                <span
                                                                    class="px-2 py-0.5 bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-800 rounded-md text-[9px] font-black shadow-inner shadow-black/5 dark:shadow-black/50">{{ $entitySoulsCount }}
                                                                    Souls</span>
                                                            </div>
                                                        </button>

                                                        <div x-show="open" x-collapse>
                                                            <div class="overflow-x-auto">
                                                                <table
                                                                    class="min-w-full divide-y divide-slate-200 dark:divide-slate-800/50">
                                                                    <thead>
                                                                        <tr
                                                                            class="text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800/50">
                                                                            <th class="px-4 py-3 text-left w-1/3">Bringer Name</th>
                                                                            <th class="px-4 py-3 text-left w-1/4">Contact</th>
                                                                            <th class="px-4 py-3 text-left w-1/4">Cell Info</th>
                                                                            <th class="px-4 py-3 text-left text-right">First Timers
                                                                                Brought</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody
                                                                        class="divide-y divide-slate-100 dark:divide-slate-800/30">
                                                                        @foreach($bringers as $bringer)
                                                                            <tr class="hover:bg-white/5 transition-colors group"
                                                                                x-show="matches({
                                                                                                                                                                                                                                                                     name: @js($bringer->name),
                                                                                                                                                                                                                                                                     contact: @js($bringer->contact),
                                                                                                                                                                                                                                                                     church_id: @js($bringer->church_id),
                                                                                                                                                                                                                                                                     pcf_id: @js($bringer->pcf_id)
                                                                                                                                                                                                                                                                 })">
                                                                                <td
                                                                                    class="px-4 py-3 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                                                                    {{ $bringer->name }}
                                                                                </td>
                                                                                <td
                                                                                    class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400 font-mono">
                                                                                    {{ $bringer->contact }}
                                                                                </td>
                                                                                <td
                                                                                    class="px-4 py-3 whitespace-nowrap text-sm text-slate-400">
                                                                                    @if($bringer->senior_cell_name || $bringer->cell_name)
                                                                                        <div class="flex flex-col">
                                                                                            @if($bringer->senior_cell_name) <span
                                                                                                class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $bringer->senior_cell_name }}
                                                                                            (SC)</span> @endif
                                                                                            @if($bringer->cell_name) <span
                                                                                                class="text-[10px] text-slate-500 dark:text-slate-400">{{ $bringer->cell_name }}
                                                                                            (Cell)</span> @endif
                                                                                        </div>
                                                                                    @else
                                                                                        <span
                                                                                            class="text-slate-600 italic text-xs">N/A</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td
                                                                                    class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                                                                    <button
                                                                                        @click="$dispatch('view-brought-timers', {{ $bringer->firstTimers->load(['church', 'pcf'])->merge($bringer->retainedMembers->load(['church', 'pcf']))->toJson() }})"
                                                                                        class="px-2.5 py-1 text-[10px] font-black rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-amber-500/50">
                                                                                        {{ $bringer->total_souls_count }} Souls
                                                                                    </button>
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
                    <div class="glass-card p-12 flex flex-col items-center justify-center text-center">
                        <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">No Bringers Found</h3>
                        <p class="text-sm text-slate-500">There are no bringers registered in the system yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Brought First Timers Modal -->
    <div x-data="{ 
            open: false, 
            firstTimers: [],
            init() {
                window.addEventListener('view-brought-timers', (e) => {
                    this.firstTimers = e.detail;
                    this.open = true;
                });
            }
        }" x-show="open" class="fixed inset-0 z-[110] overflow-y-auto" style="display: none;">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm" @click="open = false">
            </div>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" <div
                class="inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform glass-card bg-white dark:bg-slate-900 shadow-2xl rounded-3xl sm:my-8 border border-slate-200 dark:border-white/10 relative z-10">

                <div class="space-y-6">
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex items-center justify-between bg-slate-50 dark:bg-white/5">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Souls Won</h3>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">First Timers
                                brought by this member</p>
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

                        <div class="max-h-[60vh] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-700">
                            <div class="space-y-3">
                                <template x-for="ft in firstTimers" :key="ft.id">
                                    <div
                                        class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:border-amber-500/30 transition-colors">
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors"
                                                x-text="ft.full_name"></h4>
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-1"
                                                x-text="(ft.church?.name || ft.pcf?.name || 'Unassigned')"></p>
                                        </div>
                                        <div class="flex flex-col sm:items-end gap-2">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest bg-slate-800 text-slate-400"
                                                    x-text="ft.primary_contact"></span>
                                            </div>
                                            <div class="flex items-center gap-2 text-[10px] font-bold">
                                                <span class="text-slate-500 uppercase tracking-widest">Visits:</span>
                                                <span
                                                    :class="ft.service_count >= 3 ? 'text-emerald-400' : 'text-indigo-400'"
                                                    x-text="ft.service_count"></span>
                                            </div>
                                            <template x-if="ft.service_count >= 3">
                                                <div
                                                    class="flex items-center gap-1.5 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md text-[9px] font-black uppercase tracking-wider">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Retained
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="firstTimers.length === 0">
                                    <div class="py-8 text-center text-slate-500 italic text-sm">
                                        No records found.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="px-8 py-5 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/5 flex justify-end">
                        <x-secondary-button @click="open = false">
                            {{ __('Close') }}
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>