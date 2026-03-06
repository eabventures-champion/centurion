<x-app-layout :show-back="true">
    <x-slot name="header">
        {{ $entity->name }} ({{ $type }}) - {{ __('Attendance') }}
    </x-slot>

    <div class="py-6" x-data="{
        showMigrationModal: false,
        isMigrating: false,
        pendingMember: null,
        pendingDate: null,
        async confirmMigration() {
            if (!this.pendingMember || this.isMigrating) return;
            this.isMigrating = true;
            try {
                const res = await fetch('{{ route("attendance.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        first_timer_id: this.pendingMember.id,
                        service_date: this.pendingDate,
                        confirmed: true
                    })
                });
                const data = await res.json();
                if (data.success && data.migrated) {
                    this.showMigrationModal = false;
                    this.isMigrating = false;
                    alert(data.message);
                    window.location.reload();
                } else {
                    this.isMigrating = false;
                    alert(data.error || 'Migration failed');
                }
            } catch(e) { 
                this.isMigrating = false;
                console.error(e); 
            }
        },
        async cancelMigration() {
            if (!this.pendingMember) return;
            this.showMigrationModal = false;
            await toggleAttendance(this.pendingMember.id, this.pendingDate);
        }
    }"
        @migration-ready.window="pendingMember = { id: $event.detail.ftId, name: $event.detail.name }; pendingDate = $event.detail.date; showMigrationModal = true;">
        <div class="max-w-7xl mx-auto px-4">
            <div class="glass-card overflow-hidden">
                <div class="p-8">
                    <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $type }}
                                    Attendance Grid</h3>
                                <span
                                    class="px-2.5 py-1 text-[10px] font-black rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 uppercase tracking-[2px]">
                                    {{ $entity->name }}
                                </span>
                            </div>
                            <div class="relative mt-2" x-data="{ open: false }">
                                @php
                                    $months = [
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December'
                                    ];
                                    $currentMonth = now()->month;
                                @endphp

                                <button @click="open = !open"
                                    class="flex items-center gap-3 px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[2px] shadow-lg shadow-indigo-600/20 hover:bg-indigo-500 transition-all active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $months[$selectedMonth] }}
                                    <svg class="w-3 h-3 transition-transform duration-300"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150" @click.away="open = false"
                                    class="absolute left-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl z-50 p-2 backdrop-blur-xl"
                                    x-cloak>
                                    <div class="grid grid-cols-1 gap-1">
                                        @foreach(range(1, $currentMonth) as $m)
                                            <a href="{{ request()->fullUrlWithQuery(['month' => $m]) }}"
                                                class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all {{ $selectedMonth == $m ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white' }}">
                                                {{ $months[$m] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4">
                            <div
                                class="flex flex-wrap items-center gap-4 p-2 rounded-2xl bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-white/5 shadow-sm">
                                <input type="date" id="custom_date" max="{{ now()->toDateString() }}"
                                    class="bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-[10px] sm:text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 cursor-pointer w-full sm:w-auto min-w-[120px] shadow-sm appearance-none">
                                <button @click="markCustomDate()"
                                    class="px-4 py-2 w-full sm:w-auto bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-500/20 transition-all">
                                    + Add Date
                                </button>
                            </div>
                            <div class="h-8 w-px bg-slate-200 dark:bg-white/5 mx-2 hidden lg:block"></div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-3 h-3 rounded-full bg-emerald-500/20 border border-emerald-500/50"></span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Initial
                                        Visit</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-md bg-indigo-600"></span>
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Present</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead>
                                <tr
                                    class="text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800 text-center">
                                    <th class="px-6 py-4 text-left">First Timer Name</th>
                                    <th class="px-4 py-4 text-left">Joined</th>
                                    @foreach($serviceDates as $date)
                                        <th class="px-4 py-4 min-w-[80px]">
                                            <div class="flex flex-col items-center">
                                                <span>{{ $date->format('M d') }}</span>
                                                <span class="text-[8px] opacity-50 mt-0.5">{{ $date->format('D') }}</span>
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-4 text-right whitespace-nowrap">Visits</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/50">
                                @forelse($firstTimers as $ft)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
                                                id="name-{{ $ft->id }}">{{ $ft->full_name }}</div>
                                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                                {{ $ft->primary_contact }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-[11px] font-bold text-slate-400">
                                                {{ $ft->global_first_visit ? \Carbon\Carbon::parse($ft->global_first_visit)->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>

                                        @foreach($serviceDates as $date)
                                                                    @php
                                                                        $dateStr = $date->toDateString();
                                                                        $isInitial = $ft->global_first_visit === $dateStr;
                                                                        $isPresent = $ft->attendanceLogs->where('service_date', $date)->isNotEmpty();
                                                                    @endphp
                                                                    <td class="px-4 py-4 text-center">
                                                                        <button {{ $isInitial ? 'disabled' : '' }}
                                                                            onclick="toggleAttendance({{ $ft->id }}, '{{ $dateStr }}')"
                                                                            id="cell-{{ $ft->id }}-{{ $dateStr }}"
                                                                            class="inline-flex items-center justify-center p-2 rounded-lg transition-all active:scale-90
                                                                                                                                                                                                                                                                                                                                                                                                                     {{ $isInitial ? 'bg-emerald-500/20 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-default' :
                                            ($isPresent ? 'bg-indigo-600 border border-indigo-400 text-white shadow-lg shadow-indigo-600/20' :
                                                'bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5 text-slate-400 dark:text-slate-600 hover:border-slate-400 dark:hover:border-slate-500 hover:text-slate-600 dark:hover:text-slate-400') }}">
                                                                            @if($isPresent)
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                                        d="M5 13l4 4L19 7" />
                                                                                </svg>
                                                                            @else
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                        d="M12 4v16m8-8H4" />
                                                                                </svg>
                                                                            @endif
                                                                        </button>
                                                                    </td>
                                        @endforeach

                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span id="badge-{{ $ft->id }}"
                                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition-all {{ $ft->service_count >= 3 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' }}">
                                                {{ $ft->service_count }} / 3
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $serviceDates->count() + 2 }}"
                                            class="px-6 py-12 text-center text-slate-500 italic font-medium">
                                            No active first timers found in this {{ $type }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Congratulations Modal -->
        <template x-if="showMigrationModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="cancelMigration()"></div>
                <div
                    class="relative glass-card bg-slate-900 border border-indigo-500/30 p-8 max-w-md w-full shadow-2xl shadow-indigo-500/20 text-center animate-in fade-in zoom-in duration-300">
                    <div
                        class="w-20 h-20 bg-indigo-500/20 border border-indigo-500/40 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-wider mb-2">
                        Congratulations!</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed">
                        <span class="text-slate-900 dark:text-white font-bold" x-text="pendingMember?.name"></span> has
                        successfully
                        completed their attendance tracker and is now ready to become a full member.
                    </p>
                    <div class="flex flex-col gap-3">
                        <button @click="confirmMigration()" :disabled="isMigrating"
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-800/50 disabled:text-indigo-400 disabled:cursor-not-allowed text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 uppercase tracking-widest text-xs flex items-center justify-center gap-3">
                            <template x-if="isMigrating">
                                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </template>
                            <span
                                x-text="isMigrating ? 'Processing Migration...' : 'Yes, Move to Retained Members'"></span>
                        </button>
                        <button @click="cancelMigration()" :disabled="isMigrating"
                            class="w-full py-4 bg-slate-800 hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed text-slate-400 font-black rounded-2xl transition-all uppercase tracking-widest text-xs border border-white/5">
                            No, Deselect Attendance
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            async function toggleAttendance(ftId, date) {
                const cell = document.getElementById(`cell-${ftId}-${date}`);
                const originalClasses = cell.className;
                const originalContent = cell.innerHTML;

                cell.disabled = true;
                cell.innerHTML = '<svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                try {
                    const response = await fetch("{{ route('attendance.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            first_timer_id: ftId,
                            service_date: date
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        if (data.requires_confirmation) {
                            window.dispatchEvent(new CustomEvent('migration-ready', {
                                detail: {
                                    ftId: ftId,
                                    name: document.getElementById(`name-${ftId}`).innerText.trim(),
                                    date: date
                                }
                            }));
                        }

                        if (data.migrated) {
                            alert(data.message);
                            window.location.reload();
                            return;
                        }
                        const badge = document.getElementById(`badge-${ftId}`);
                        badge.innerText = `${data.count} / 3`;

                        if (data.count >= 3) {
                            badge.className = 'px-2.5 py-1 text-[11px] font-black rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                        } else {
                            badge.className = 'px-2.5 py-1 text-[11px] font-black rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20';
                        }

                        if (data.status === 'added') {
                            cell.className = 'inline-flex items-center justify-center p-2 rounded-lg transition-all active:scale-90 bg-indigo-600 border border-indigo-400 text-white shadow-lg shadow-indigo-600/20';
                            cell.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>';
                        } else {
                            cell.className = 'inline-flex items-center justify-center p-2 rounded-lg transition-all active:scale-90 bg-white/5 border border-white/5 text-slate-600 hover:border-slate-500 hover:text-slate-400';
                            cell.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>';
                        }
                        cell.disabled = false;
                    } else {
                        alert(data.error || 'Error toggling attendance');
                        cell.className = originalClasses;
                        cell.innerHTML = originalContent;
                        cell.disabled = false;
                    }
                } catch (e) {
                    console.error(e);
                    alert('Connection error');
                    cell.className = originalClasses;
                    cell.innerHTML = originalContent;
                    cell.disabled = false;
                }
            }

            function markCustomDate() {
                const date = document.getElementById('custom_date').value;
                if (!date)
                    return alert('Please select a date first');

                const url = new URL(window.location.href);
                const currentDates = url.searchParams.getAll('custom_dates[]');
                if (!currentDates.includes(date)) {
                    url.searchParams.append('custom_dates[]', date);
                }
                window.location.href = url.toString();
            }
        </script>
    @endpush
</x-app-layout>