<x-app-layout>
    <x-slot name="header">
        {{ __('Reporting Overview') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header & Week Selector -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        Weekly Reporting
                        <span
                            class="px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-[10px] font-black uppercase tracking-widest">
                            {{ $totalFirstTimers }} First Timers
                        </span>
                    </h3>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                        First timers received between {{ $weekStart->format('M d') }} -
                        {{ $weekEnd->format('M d, Y') }}
                    </p>
                </div>

                <div class="flex flex-wrap sm:flex-nowrap items-center gap-4 mt-4 md:mt-0">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('reporting.export.excel', ['week_start' => $weekStart->toDateString()]) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel
                        </a>
                        <a href="{{ route('reporting.export.pdf', ['week_start' => $weekStart->toDateString()]) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-[10px] font-black uppercase tracking-widest hover:bg-rose-500/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            PDF
                        </a>
                    </div>

                    <div
                        class="flex items-center gap-3 bg-white dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
                        <a href="{{ route('reporting.index', ['week_start' => $weekStart->copy()->subWeek()->toDateString()]) }}"
                            class="p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400 transition-all"
                            title="Previous Week">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div
                            class="px-4 py-2 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
                            <span
                                class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest whitespace-nowrap">
                                Week of {{ $weekStart->format('M d') }}
                            </span>
                        </div>
                        @if($weekStart->copy()->addWeek()->lte(now()->startOfWeek(\Carbon\Carbon::SUNDAY)))
                            <a href="{{ route('reporting.index', ['week_start' => $weekStart->copy()->addWeek()->toDateString()]) }}"
                                class="p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400 transition-all"
                                title="Next Week">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <span class="p-2.5 rounded-xl text-slate-300 dark:text-slate-700 cursor-not-allowed"
                                title="Current week">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Group Summations -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @foreach($groups as $group)
                    <div
                        class="glass-card p-6 border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">
                            {{ $group->group_name }}
                        </p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $group->grand_total }}
                        </h4>
                        <div class="mt-4 flex items-center gap-4">
                            @if($group->is_pcf_focused)
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">PCFs
                                        Only</span>
                                    <span
                                        class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $group->pcf_total }}</span>
                                </div>
                            @else
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Churches
                                        Only</span>
                                    <span
                                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $group->church_total }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- PCFs Detailed Breakdown -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h4
                            class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-[2px] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            PCF Breakdown
                        </h4>
                    </div>
                    <div
                        class="glass-card overflow-hidden border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50">
                        <div class="overflow-x-auto overflow-y-hidden w-full max-w-full">
                            <table class="w-full text-left min-w-[500px] sm:min-w-[600px]">
                                <thead
                                    class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                            PCF Name</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                            Group</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">
                                            First Timers</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                                    @forelse($pcfs as $pcf)
                                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                            <td class="px-6 py-4">
                                                <div
                                                    class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $pcf->name }}
                                                </div>
                                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                    {{ $pcf->leader_name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ $pcf->churchGroup->group_name }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-sm font-black">
                                                    {{ $pcf->visitor_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 italic text-sm">No
                                                PCF data for this week.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Churches Detailed Breakdown -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h4
                            class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-[2px] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Church Breakdown
                        </h4>
                    </div>
                    <div
                        class="glass-card overflow-hidden border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/50">
                        <div class="overflow-x-auto overflow-y-hidden w-full max-w-full">
                            <table class="w-full text-left min-w-[500px] sm:min-w-[600px]">
                                <thead
                                    class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                            Church Name</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                            Group</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">
                                            First Timers</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                                    @forelse($churches as $church)
                                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                            <td class="px-6 py-4">
                                                <div
                                                    class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                    {{ $church->name }}
                                                </div>
                                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                    {{ $church->leader_name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ $church->churchGroup->group_name }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-sm font-black">
                                                    {{ $church->visitor_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 italic text-sm">No
                                                Church data for this week.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>