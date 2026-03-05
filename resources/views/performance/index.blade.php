<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
                class="p-2 hover:bg-slate-100 dark:hover:bg-white/5 rounded-full transition-colors">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight italic">
                PERFORMANCE OVERVIEW
                ({{ $entityType === 'My Church' ? 'MY CHURCH' : Str::plural(strtoupper($entityType)) }})
            </h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="performanceChart()">
        <!-- Main Performance Chart (Leaderboard Overview) -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">SOUL
                        RETENTION PERFORMANCE</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Sorted by Retention
                        Rate (Last 6 Months Data)</p>
                </div>

                <div class="flex items-center gap-6">
                    <div
                        class="flex items-center bg-slate-100 dark:bg-white/5 p-1 rounded-xl border border-slate-200 dark:border-white/10">
                        <button @click="chartType = 'line'; updateChart()"
                            :class="chartType === 'line' ? 'bg-white dark:bg-white/10 text-indigo-500 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                            Line
                        </button>
                        <button @click="chartType = 'bar'; updateChart()"
                            :class="chartType === 'bar' ? 'bg-white dark:bg-white/10 text-indigo-500 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                            Bar
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative h-[400px] w-full">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Church Performance Hierarchy (Tabular) -->
        <div class="glass-card overflow-hidden">
            <div
                class="px-6 py-5 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-slate-800/50 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Church Performance
                        Hierarchy</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Nested View by Group
                        and Category</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('performance.export.excel', ['type' => $type]) }}"
                        class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                    <a href="{{ route('performance.export.pdf', ['type' => $type]) }}"
                        class="flex items-center gap-2 px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </a>
                    <div class="ml-4 text-[10px] font-black text-slate-500 uppercase tracking-widest hidden md:block">
                        SORTED BY RETENTION RATE
                    </div>
                </div>
            </div>

            <div class="p-4 space-y-4 bg-white dark:bg-slate-900/40">
                @foreach($hierarchy as $category)
                    @php $isSuperAdmin = auth()->user()->hasRole('Super Admin'); @endphp

                    <div x-data="{ open: true }"
                        class="{{ $isSuperAdmin ? 'border border-slate-200 dark:border-white/5 rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-900/20' : '' }}">
                        <!-- Category Header (Super Admin Only) -->
                        @if($isSuperAdmin)
                            <div @click="open = !open"
                                class="flex items-center justify-between p-4 bg-slate-100 dark:bg-slate-800/40 hover:bg-slate-200 dark:hover:bg-slate-800/60 cursor-pointer transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-1.5 bg-indigo-500/10 rounded-lg group-hover:bg-indigo-500/20 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-indigo-400 transition-transform duration-300"
                                            :class="open ? '' : '-rotate-90'" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ $category['name'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-slate-500 dark:text-slate-600 uppercase">({{ count($category['groups']) }}
                                        Groups)</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest opacity-60">AVG
                                        RETENTION:</span>
                                    <span
                                        class="text-xs font-black p-1 px-2 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $category['avg_retention'] }}%
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Category Content -->
                        <div x-show="open" x-collapse>
                            <div class="{{ $isSuperAdmin ? 'p-3 space-y-3' : 'space-y-4' }}">
                                @foreach($category['groups'] as $group)
                                    @php $showGroupHeader = $isSuperAdmin || count($category['groups']) > 1; @endphp

                                    <div x-data="{ groupOpen: true }"
                                        class="{{ $showGroupHeader ? 'border border-slate-200 dark:border-white/5 rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-900/40' : '' }}">
                                        <!-- Group Header -->
                                        @if($showGroupHeader)
                                            <div @click="groupOpen = !groupOpen"
                                                class="flex items-center justify-between p-3 bg-slate-100/50 dark:bg-white/[0.02] hover:bg-slate-200/50 dark:hover:bg-white/[0.04] cursor-pointer transition-colors">
                                                <div class="flex items-center gap-3 ml-4">
                                                    <svg class="w-3 h-3 text-slate-500 transition-transform duration-300"
                                                        :class="groupOpen ? '' : '-rotate-90'" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                    <span
                                                        class="text-[11px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest">{{ $group['name'] }}</span>
                                                    <span
                                                        class="text-[9px] font-bold text-slate-400 dark:text-slate-700 uppercase">({{ count($group['entities']) }}
                                                        {{ $entityType === 'My Church' ? 'Church' : ($entityType === 'My PCF' ? 'PCF' : Str::plural($entityType)) }})</span>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <span
                                                        class="text-[9px] font-bold text-slate-500 dark:text-slate-600 uppercase tracking-widest">Performance:</span>
                                                    <span
                                                        class="text-[11px] font-black text-slate-600 dark:text-slate-400">{{ $group['avg_retention'] }}%</span>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Group Table -->
                                        <div x-show="groupOpen" x-collapse>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-left">
                                                    <thead>
                                                        <tr class="bg-slate-100 dark:bg-slate-900/80 border-b border-slate-200 dark:border-white/5">
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest {{ $showGroupHeader ? 'pl-16' : '' }}">
                                                                {{ $entityType }}</th>
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                                                OFFICER</th>
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">
                                                                BRINGERS</th>
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">
                                                                TOTAL FT</th>
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center text-orange-400">
                                                                NEW FT</th>
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center text-emerald-400">
                                                                RETAINED MEMBERS</th>
                                                            <th
                                                                class="px-6 py-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">
                                                                RETENTION %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                                                        @foreach($group['entities'] as $entity)
                                                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group/row">
                                                                <td class="px-6 py-4 {{ $showGroupHeader ? 'pl-16' : '' }}">
                                                                    <span
                                                                        class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase group-hover/row:text-indigo-600 dark:group-hover/row:text-white transition-colors">{{ $entity['name'] }}</span>
                                                                </td>
                                                                <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                                                    {{ $entity['officer'] }}</td>
                                                                <td
                                                                    class="px-6 py-4 text-xs font-black text-indigo-400/80 text-center">
                                                                    {{ $entity['bringers'] }}</td>
                                                                <td class="px-6 py-4 text-xs font-black text-slate-400 text-center">
                                                                    {{ $entity['total_ft'] }}</td>
                                                                <td
                                                                    class="px-6 py-4 text-xs font-black text-orange-500/80 text-center">
                                                                    {{ $entity['new_ft'] }}</td>
                                                                <td
                                                                    class="px-6 py-4 text-xs font-black text-emerald-500/80 text-center">
                                                                    {{ $entity['total_rm'] }}</td>
                                                                <td class="px-6 py-4 text-right">
                                                                    <span
                                                                        class="text-xs font-black {{ $entity['retention_rate'] >= 50 ? 'text-emerald-500' : 'text-indigo-400' }}">
                                                                        {{ $entity['retention_rate'] }}%
                                                                    </span>
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('performanceChart', () => ({
                    chartType: 'line',
                    chart: null,
                    init() {
                        this.createChart();
                    },
                    createChart() {
                        const ctx = document.getElementById('performanceChart').getContext('2d');
                        if (this.chart) {
                            this.chart.destroy();
                        }
                        this.chart = new Chart(ctx, {
                            type: this.chartType,
                            data: {
                                labels: @json($labels),
                                datasets: this.getDatasets()
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { intersect: false, mode: 'index' },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            color: '#64748b',
                                            font: { size: 9, weight: 'bold' },
                                            usePointStyle: true,
                                            padding: 12
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        titleFont: { size: 13, weight: 'bold' },
                                        bodyFont: { size: 12 },
                                        itemSort: (a, b) => b.raw - a.raw
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
                                        ticks: { color: '#64748b', font: { size: 10, weight: 'bold' }, stepSize: 1 }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: '#64748b', font: { size: 10, weight: 'bold' } }
                                    }
                                }
                            }
                        });
                    },
                    updateChart() {
                        this.createChart();
                    },
                    getDatasets() {
                        const chartData = @json($chartData).slice(0, 10); // Show top 10 for clarity in overview
                        const datasets = [];
                        const isBar = this.chartType === 'bar';

                        chartData.forEach((entity, index) => {
                            const colors = [
                                { ft: '#6366f1', rm: '#10b981' },
                                { ft: '#8b5cf6', rm: '#06b6d4' },
                                { ft: '#ec4899', rm: '#f59e0b' },
                                { ft: '#06b6d4', rm: '#ec4899' },
                                { ft: '#f59e0b', rm: '#6366f1' },
                                { ft: '#3b82f6', rm: '#10b981' },
                                { ft: '#6366f1', rm: '#fbbf24' },
                                { ft: '#10b981', rm: '#6366f1' },
                                { ft: '#8b5cf6', rm: '#ec4899' },
                                { ft: '#f59e0b', rm: '#06b6d4' }
                            ];
                            const color = colors[index % colors.length];

                            datasets.push({
                                label: `${entity.name} (FT)`,
                                data: entity.ft_data,
                                borderColor: color.ft,
                                backgroundColor: isBar ? color.ft : 'transparent',
                                borderWidth: isBar ? 0 : 3,
                                tension: 0.4,
                                fill: !isBar,
                                pointRadius: isBar ? 0 : 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: color.ft,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                borderRadius: isBar ? 6 : 0,
                                barPercentage: 0.8,
                                categoryPercentage: 0.9
                            });

                            datasets.push({
                                label: `${entity.name} (RM)`,
                                data: entity.rm_data,
                                borderColor: color.rm,
                                backgroundColor: isBar ? color.rm : 'transparent',
                                borderWidth: isBar ? 0 : 2,
                                borderDash: isBar ? [] : [5, 5],
                                tension: 0.4,
                                fill: !isBar,
                                pointRadius: isBar ? 0 : 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: color.rm,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                borderRadius: isBar ? 6 : 0,
                                barPercentage: 0.8,
                                categoryPercentage: 0.9
                            });
                        });
                        return datasets;
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>