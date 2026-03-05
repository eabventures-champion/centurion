<x-app-layout>
    <x-slot name="header">
        {{ __('Attendance Marking') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Attendance Marking</h3>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">Select a Church or PCF to mark attendance for first timers</p>
                </div>
                <div class="relative" x-data="{ open: false }">
                    @php
                        $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
                        $currentMonth = now()->month;
                    @endphp
                    
                    <button @click="open = !open" 
                        class="flex items-center gap-3 px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[2px] shadow-lg shadow-indigo-600/20 hover:bg-indigo-500 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $months[$month] }}
                        <svg class="w-3 h-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl z-50 p-2 backdrop-blur-xl"
                        x-cloak>
                        <div class="grid grid-cols-1 gap-1">
                            @foreach(range(1, $currentMonth) as $m)
                                <a href="{{ route('attendance.index', ['month' => $m]) }}" 
                                    class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all {{ $month == $m ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white' }}">
                                    {{ $months[$m] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @if($pcfs->isNotEmpty())
                <div class="mb-6">
                    <h4 class="text-xs font-black text-amber-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        PCFs
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pcfs as $pcf)
                            <a href="{{ route('attendance.pcf', [$pcf, 'month' => $month]) }}"
                                class="glass-card group hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-all active:scale-[0.98] border border-slate-200 dark:border-white/10 dark:bg-white/5">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-4">
                                        <div
                                            class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 group-hover:bg-amber-500/20 transition-colors">
                                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                            {{ $pcf->first_timers_count }} Active
                                        </span>
                                    </div>

                                    <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                        {{ $pcf->name }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mb-2">
                                        {{ $pcf->churchGroup->group_name ?? 'N/A' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($churches->isNotEmpty())
                <div class="mb-6 {{ $pcfs->isNotEmpty() ? 'mt-12' : '' }}">
                    <h4 class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        @if(auth()->user()->hasRole('Admin')) MY CHURCH @else Churches @endif
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($churches as $church)
                            <a href="{{ route('attendance.church', [$church, 'month' => $month]) }}"
                                class="glass-card group hover:border-indigo-500/50 dark:hover:border-indigo-500/50 transition-all active:scale-[0.98] border border-slate-200 dark:border-white/10 dark:bg-white/5">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-4">
                                        <div
                                            class="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 group-hover:bg-indigo-500/20 transition-colors">
                                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                            {{ $church->first_timers_count }} Active
                                        </span>
                                    </div>

                                    <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $church->name }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mb-2">
                                        {{ $church->churchGroup->group_name ?? 'N/A' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($churches->isEmpty() && $pcfs->isEmpty())
                <div class="py-12 text-center bg-slate-50 dark:bg-slate-900/50 rounded-2xl border-dashed border-2 border-slate-200 dark:border-slate-800">
                    <p class="text-slate-500 italic font-medium">No entities found with active first timers.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>