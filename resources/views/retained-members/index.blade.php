<x-app-layout>
    <x-slot name="header">
        {{ __('Retained Members') }}
    </x-slot>

    <div class="py-10" x-data="retainedMembers()">
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('retainedMembers', () => ({
                    search: "",
                    gender: "all",
                    churchFilter: "all",
                    pcfFilter: "all",
                    showViewModal: false,
                    showEditModal: false,
                    showAttendanceModal: false,
                    visitor: {},
                    editingMember: {},
                    bringers: @js($bringers->values()),
                    attendanceDates: @js(\App\Http\Controllers\AttendanceController::getServiceDates(now()->month, now()->year, null, null)->map(fn($d) => $d->toDateString())),

                    get filteredBringers() {
                        if (!this.editingMember) return this.bringers;
                        const pcfId = parseInt(this.editingMember.pcf_id);
                        const currentBringerId = this.editingMember.bringer_id;

                        return this.bringers.filter(b => {
                            // Always include the currently assigned bringer so it's selected in the modal
                            // Even if they don't match the PCF filter (though they usually should)
                            return !pcfId || b.pcf_ids.includes(pcfId) || (currentBringerId && b.id == currentBringerId);
                        });
                    },

                    matches(member) {
                        const s = this.search.toLowerCase();
                        const nameMatch = !s || member.name.toLowerCase().includes(s) || (member.contact && member.contact.includes(s));
                        const genderMatch = this.gender === "all" || member.gender === this.gender;
                        const churchMatch = this.churchFilter === "all" || member.church_id == this.churchFilter;
                        const pcfMatch = this.pcfFilter === "all" || member.pcf_id == this.pcfFilter;
                        return nameMatch && genderMatch && churchMatch && pcfMatch;
                    },

                    anyVisible(items) {
                        return items.some(i => this.matches(i));
                    },

                    viewMember(id) {
                        fetch(`/retained-members/${id}`)
                            .then(res => res.json())
                            .then(data => {
                                this.visitor = data;
                                this.showViewModal = true;
                            });
                    },

                    editMember(id) {
                        fetch(`/retained-members/${id}`)
                            .then(res => res.json())
                            .then(data => {
                                this.editingMember = data;
                                // Wait for pcf_id to update filteredBringers and template x-for to render
                                this.$nextTick(() => {
                                    this.showEditModal = true;
                                    // Set as string after a small delay to ensure options exist in DOM
                                    setTimeout(() => {
                                        if (this.editingMember && this.editingMember.bringer_id) {
                                            this.editingMember.bringer_id = this.editingMember.bringer_id.toString();
                                        }
                                    }, 50);
                                });
                            });
                    },

                    updateMember() {
                        fetch(`/retained-members/${this.editingMember.id}`, {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.editingMember)
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                }
                            });
                    },

                    editAttendance(id) {
                        fetch(`/retained-members/${id}`)
                            .then(res => res.json())
                            .then(data => {
                                this.editingMember = data;
                                this.showAttendanceModal = true;
                            });
                    },

                    toggleAttendance(date) {
                        fetch(`/retained-members/toggle-attendance`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                retained_member_id: this.editingMember.id,
                                service_date: date
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.reverted) {
                                    location.reload();
                                } else {
                                    this.editAttendance(this.editingMember.id);
                                }
                            });
                    },

                    isPresent(date) {
                        if (this.isInitial(date)) return true;
                        if (!this.editingMember.attendance_logs) return false;
                        return this.editingMember.attendance_logs.some(l => {
                            const ld = (l.service_date || "").split("T")[0];
                            return ld === date;
                        });
                    },

                    isInitial(date) {
                        return this.editingMember.global_first_visit === date;
                    },

                    acknowledgeAll() {
                        fetch('/retained-members/acknowledge-all', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) location.reload();
                            });
                    },

                    acknowledgeMember(id) {
                        fetch(`/retained-members/${id}/acknowledge`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) location.reload();
                            });
                    },

                    getModalDates() {
                        const dates = new Set();
                        if (this.editingMember.global_first_visit) {
                            dates.add(this.editingMember.global_first_visit);
                        }
                        if (this.editingMember.attendance_logs) {
                            this.editingMember.attendance_logs.forEach(log => {
                                const ld = (log.service_date || "").split("T")[0];
                                if (ld) dates.add(ld);
                            });
                        }
                        // Sort descending
                        return Array.from(dates).filter(d => !!d).sort((a, b) => b.localeCompare(a));
                    }
                }));
            });
        </script>
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Retained Members
                        </h3>
                        @php
                            $user = auth()->user();
                            if ($user->hasRole('Super Admin')) {
                                $rmCount = \App\Models\RetainedMember::count();
                            } elseif ($user->hasRole('Official')) {
                                $officialPcfIds = $user->pcfs()->pluck('id');
                                $rmCount = \App\Models\RetainedMember::whereIn('pcf_id', $officialPcfIds)->count();
                            } else {
                                $rmCount = $user->church() ? \App\Models\RetainedMember::where('church_id', $user->church()->id)->orWhereIn('pcf_id', \App\Models\Pcf::where('church_group_id', $user->church()->church_group_id)->pluck('id'))->count() : 0;
                            }
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/20 text-[9px] font-black uppercase tracking-widest shadow-sm shadow-amber-500/10">
                            {{ $rmCount }}
                        </span>
                    </div>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                        View and manage members who have completed their retention tenure
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('retained-members.export.excel') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-emerald-600/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                    <a href="{{ route('retained-members.export.pdf') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-rose-600/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>

            @if($unacknowledgedCount > 0)
                <div
                    class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-500/15 rounded-xl">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-amber-600 dark:text-amber-400">{{ $unacknowledgedCount }} New
                                Member{{ $unacknowledgedCount > 1 ? 's' : '' }} Migrated</p>
                            <p
                                class="text-[10px] font-bold text-amber-600/70 dark:text-amber-500/70 uppercase tracking-widest">
                                First timers who completed 3/3 attendance</p>
                        </div>
                    </div>
                    <button @click="acknowledgeAll()"
                        class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-lg shadow-amber-500/20 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Acknowledge All
                    </button>
                </div>
            @endif

            <div class="glass-card mb-6 p-4 border border-slate-200 dark:border-white/5">
                <div class="flex flex-col xl:flex-row gap-3">
                    <!-- Search -->
                    <div class="relative flex-1 group min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Search by name, contact..."
                            class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl py-2.5 pl-10 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all font-medium">
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap md:flex-nowrap gap-2 items-center">
                        <div class="relative min-w-[120px]">
                            <select x-model="gender"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider rounded-xl py-2.5 px-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                <option value="all">Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="relative min-w-[140px]">
                            <select x-model="churchFilter"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider rounded-xl py-2.5 px-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                <option value="all">All Churches</option>
                                @foreach($availableChurches as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="relative min-w-[140px]">
                            <select x-model="pcfFilter"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider rounded-xl py-2.5 px-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none appearance-none cursor-pointer transition-all">
                                <option value="all">All PCFs</option>
                                @foreach($availablePcfs as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <button @click="search = ''; gender = 'all'; churchFilter = 'all'; pcfFilter = 'all'"
                            title="Reset Filters"
                            class="p-2.5 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl transition-all active:scale-95 border border-slate-200 dark:border-white/5 shadow-sm">
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
                        $allFlatMembers = collect($groupedMembers)->flatMap(function ($groups) {
                            return collect($groups)->flatMap(function ($entities) {
                                return collect($entities)->flatten(1);
                            });
                        });
                    @endphp
                    @if($allFlatMembers->count() > 0)
                        <div class="glass-card overflow-hidden border border-slate-200 dark:border-white/5">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800/50">
                                    <thead>
                                        <tr
                                            class="text-[9px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800/50 text-center bg-slate-50 dark:bg-white/5">
                                            <th class="px-6 py-3.5 text-left">Name</th>
                                            <th class="px-6 py-3.5 text-left">Contact</th>
                                            <th class="px-6 py-3.5">Retained Date</th>
                                            <th class="px-6 py-3.5 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/30">
                                        @foreach($allFlatMembers as $member)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group {{ !$member->acknowledged ? 'border-l-2 border-l-amber-500 bg-amber-500/[0.03]' : '' }}"
                                                x-show="matches({
                                                                                                                                                                                                                                                                                 name: @js($member->full_name),
                                                                                                                                                                                                                                                                                 contact: @js($member->primary_contact),
                                                                                                                                                                                                                                                                                 gender: @js($member->gender),
                                                                                                                                                                                                                                                                                 church_id: @js($member->church_id),
                                                                                                                                                                                                                                                                                 pcf_id: @js($member->pcf_id)
                                                                                                                                                                                                                                                                             })">
                                                <td
                                                    class="px-6 py-3.5 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    <div class="flex items-center gap-2">
                                                        {{ $member->full_name }}
                                                        @if(!$member->acknowledged)
                                                            <span
                                                                class="px-1.5 py-0.5 rounded-md text-[8px] font-black bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-wider animate-pulse">New</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-500 font-bold">
                                                    {{ $member->primary_contact }}
                                                </td>
                                                <td
                                                    class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-500 dark:text-slate-400 font-bold text-center">
                                                    {{ $member->retained_date ? \Carbon\Carbon::parse($member->retained_date)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        @if(!$member->acknowledged)
                                                            <button @click="acknowledgeMember({{ $member->id }})"
                                                                class="px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-white border border-amber-600/20 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all active:scale-95 shadow-sm shadow-amber-500/20"
                                                                title="Acknowledge Migration">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        @endif
                                                        <button @click="viewMember({{ $member->id }})"
                                                            class="p-1.5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all transition-colors"
                                                            title="View Member Detail">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                        <button @click="editMember({{ $member->id }})"
                                                            class="p-1.5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all transition-colors"
                                                            title="Edit Member Info">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <button @click="editAttendance({{ $member->id }})"
                                                            class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-500 border border-amber-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all">
                                                            Attendance
                                                        </button>
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
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">No Retained Members Found</h3>
                            <p class="text-sm text-slate-500">There are no retained members registered for your church yet.</p>
                        </div>
                    @endif
                @else
                    {{-- Super Admin: Nested Hierarchy --}}
                    @forelse($groupedMembers as $category => $groups)
                        @php
                            $categoryItems = collect($groups)->flatten(2)->map(fn($f) => [
                                'name' => $f->full_name,
                                'contact' => $f->primary_contact,
                                'gender' => $f->gender,
                                'church_id' => $f->church_id,
                                'pcf_id' => $f->pcf_id
                            ]);
                            $categoryCount = $categoryItems->count();
                        @endphp
                        <div class="glass-card mb-6 border border-slate-200 dark:border-white/5 overflow-hidden"
                            x-data="{ open: false, items: @js($categoryItems) }" x-show="anyVisible(items)">
                            <button @click="open = !open"
                                class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-all group/cat">
                                <h4
                                    class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-[2px] flex items-center gap-3">
                                    <svg class="w-4 h-4 text-indigo-500 transition-transform duration-300"
                                        :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                    {{ $category }}
                                </h4>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-black group-hover/cat:bg-indigo-500/20 transition-colors">
                                        {{ $categoryCount }} Members
                                    </span>
                                </div>
                            </button>

                            <div x-show="open" x-collapse>
                                <div class="p-6 space-y-6">
                                    @foreach($groups as $groupName => $entities)
                                        @php
                                            $groupItems = collect($entities)->flatten(1)->map(fn($f) => [
                                                'name' => $f->full_name,
                                                'contact' => $f->primary_contact,
                                                'gender' => $f->gender,
                                                'church_id' => $f->church_id,
                                                'pcf_id' => $f->pcf_id
                                            ]);
                                            $groupCount = $groupItems->count();
                                        @endphp
                                        <div class="space-y-4" x-data="{ open: false, items: @js($groupItems) }"
                                            x-show="anyVisible(items)">
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
                                                    <span
                                                        class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md text-[9px] font-black">
                                                        {{ $groupCount }} Members
                                                    </span>
                                                </div>
                                                <div
                                                    class="h-px bg-slate-700 flex-1 transition-colors group-hover/grp:bg-emerald-500/50">
                                                </div>
                                            </button>

                                            <div x-show="open" x-collapse>
                                                <div class="space-y-4 pl-0 md:pl-4">
                                                    @foreach($entities as $entityName => $members)
                                                        @php
                                                            $entityItems = collect($members)->map(fn($f) => [
                                                                'name' => $f->full_name,
                                                                'contact' => $f->primary_contact,
                                                                'gender' => $f->gender,
                                                                'church_id' => $f->church_id,
                                                                'pcf_id' => $f->pcf_id
                                                            ]);
                                                            $entityCount = $entityItems->count();
                                                        @endphp
                                                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                                                            x-data="{ open: true, items: @js($entityItems) }"
                                                            x-show="anyVisible(items)">
                                                            <button @click="open = !open"
                                                                class="w-full bg-slate-100 dark:bg-slate-800/50 px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center focus:outline-none transition-colors group/ent">
                                                                <div class="flex items-center gap-2.5">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-300 group-hover/ent:text-white"
                                                                        :class="open ? 'rotate-90' : ''" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="3" d="M9 5l7 7-7 7"></path>
                                                                    </svg>
                                                                    <span
                                                                        class="text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">{{ $entityName }}</span>
                                                                </div>
                                                                <span
                                                                    class="px-2 py-0.5 bg-slate-200/50 dark:bg-white/5 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/5 rounded text-[9px] font-bold">{{ $entityCount }}
                                                                    Members</span>
                                                            </button>

                                                            <div x-show="open" x-collapse>
                                                                <div class="overflow-x-auto">
                                                                    <table class="min-w-full divide-y divide-slate-800/50">
                                                                        <thead>
                                                                            <tr
                                                                                class="text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-800/50 text-center">
                                                                                <th class="px-6 py-4 text-left">Name</th>
                                                                                <th class="px-6 py-4 text-left">Contact</th>
                                                                                <th class="px-6 py-4">Retained Date</th>
                                                                                <th class="px-6 py-4 text-right">Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-slate-800/30">
                                                                            @foreach($members as $member)
                                                                                <tr class="hover:bg-white/5 transition-colors group"
                                                                                    x-show="matches({
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    name: @js($member->full_name),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    contact: @js($member->primary_contact),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    gender: @js($member->gender),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    church_id: @js($member->church_id),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    pcf_id: @js($member->pcf_id)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                })">
                                                                                    <td
                                                                                        class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-400 transition-colors">
                                                                                        {{ $member->full_name }}
                                                                                    </td>
                                                                                    <td
                                                                                        class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-mono">
                                                                                        {{ $member->primary_contact }}
                                                                                    </td>
                                                                                    <td
                                                                                        class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-bold text-center">
                                                                                        {{ $member->retained_date ? \Carbon\Carbon::parse($member->retained_date)->format('M d, Y') : 'N/A' }}
                                                                                    </td>
                                                                                    <td
                                                                                        class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                                                                        <div class="flex items-center justify-end gap-3">
                                                                                            <button @click="viewMember({{ $member->id }})"
                                                                                                class="p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-indigo-400 transition-all transition-colors"
                                                                                                title="View Member Detail">
                                                                                                <svg class="w-4 h-4" fill="none"
                                                                                                    stroke="currentColor"
                                                                                                    viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round"
                                                                                                        stroke-linejoin="round"
                                                                                                        stroke-width="2"
                                                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                                    <path stroke-linecap="round"
                                                                                                        stroke-linejoin="round"
                                                                                                        stroke-width="2"
                                                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                                                </svg>
                                                                                            </button>
                                                                                            <button @click="editMember({{ $member->id }})"
                                                                                                class="p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-emerald-400 transition-all transition-colors"
                                                                                                title="Edit Member Info">
                                                                                                <svg class="w-4 h-4" fill="none"
                                                                                                    stroke="currentColor"
                                                                                                    viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round"
                                                                                                        stroke-linejoin="round"
                                                                                                        stroke-width="2"
                                                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                                                </svg>
                                                                                            </button>
                                                                                            <button
                                                                                                @click="editAttendance({{ $member->id }})"
                                                                                                class="px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 border border-amber-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                                                                                Attendance
                                                                                            </button>
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
                        <div
                            class="glass-card p-12 flex flex-col items-center justify-center text-center border border-slate-200 dark:border-white/5">
                            <svg class="w-16 h-16 text-slate-400 dark:text-slate-600 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">No Members Found</h3>
                            <p class="text-sm text-slate-500">There are no retained members registered for your church yet.</p>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>

        <!-- View Details Modal -->
        <template x-if="showViewModal">
            <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm"
                        @click="showViewModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom glass-card relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <div class="absolute top-0 right-0 pt-6 pr-6">
                            <button @click="showViewModal = false"
                                class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-8">
                            <div class="flex items-center gap-6 mb-8">
                                <div
                                    class="w-20 h-20 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                                    <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400"
                                        x-text="visitor.full_name?.charAt(0)"></span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight"
                                        x-text="visitor.full_name"></h3>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1"
                                        x-text="visitor.primary_contact"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <template x-for="(val, label) in {
                                    'Email': visitor.email || 'N/A',
                                    'Gender': visitor.gender,
                                    'Date of Birth': visitor.date_of_birth,
                                    'Marital Status': visitor.marital_status,
                                    'Occupation': visitor.occupation || 'N/A',
                                    'Born Again': visitor.born_again ? 'Yes' : 'No',
                                    'Water Baptism': visitor.water_baptism ? 'Yes' : 'No',
                                    'Prayer Requests': visitor.prayer_requests || 'N/A',
                                    'Address': visitor.residential_address,
                                    'Church': visitor.church?.name || (visitor.pcf?.name ? visitor.pcf.name + ' (PCF)' : 'N/A'),
                                    'Bringer': visitor.bringer?.name || 'N/A'
                                }">
                                    <div
                                        class="bg-slate-50 dark:bg-slate-950/50 p-4 rounded-2xl border border-slate-200 dark:border-white/5">
                                        <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1"
                                            x-text="label"></p>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="val"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Edit Info Modal -->
        <template x-if="showEditModal">
            <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm"
                        @click="showEditModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom glass-card relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <div class="absolute top-0 right-0 pt-6 pr-6">
                            <button @click="showEditModal = false"
                                class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-8">
                            <h3
                                class="text-xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-widest">
                                Edit Member Info
                            </h3>

                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="col-span-full">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Full
                                        Name</label>
                                    <input type="text" x-model="editingMember.full_name"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all font-medium">
                                </div>
                                <div class="col-span-full">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Email
                                        Address</label>
                                    <input type="email" x-model="editingMember.email"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all">
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Primary
                                        Contact</label>
                                    <input type="text" x-model="editingMember.primary_contact"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all">
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Alternate
                                        Contact</label>
                                    <input type="text" x-model="editingMember.alternate_contact"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all">
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Gender</label>
                                    <select x-model="editingMember.gender"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all appearance-none cursor-pointer">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Birthday
                                        (Day/Month)</label>
                                    <input type="text" x-model="editingMember.date_of_birth" placeholder="DD/MM"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all">
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Occupation</label>
                                    <input type="text" x-model="editingMember.occupation"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all">
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Marital
                                        Status</label>
                                    <select x-model="editingMember.marital_status"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all appearance-none cursor-pointer">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <!-- <option value="Widowed">Widowed</option>
                                        <option value="Divorced">Divorced</option> -->
                                    </select>
                                </div>

                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Assigned
                                        Church</label>
                                    <select x-model="editingMember.church_id"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all appearance-none cursor-pointer">
                                        <option value="">None / PCF Assigned</option>
                                        @foreach($availableChurches as $church)
                                            <option value="{{ $church->id }}">{{ $church->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-full md:col-span-1">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Assigned
                                        PCF</label>
                                    <select x-model="editingMember.pcf_id"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all appearance-none cursor-pointer">
                                        <option value="">None / Church Assigned</option>
                                        @foreach($availablePcfs as $pcf)
                                            <option value="{{ $pcf->id }}">{{ $pcf->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-full">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Bringer</label>
                                    <select x-model="editingMember.bringer_id"
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-slate-900 dark:text-white py-3 px-4 transition-all appearance-none cursor-pointer">
                                        <option value="">None</option>
                                        <template x-for="bringer in filteredBringers" :key="bringer.id">
                                            <option :value="bringer.id.toString()"
                                                x-text="bringer.name + ' (' + bringer.contact + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="col-span-full">
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Residential
                                        Address</label>
                                    <textarea name="residential_address" x-model="editingMember.residential_address"
                                        class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl shadow-sm transition-all duration-300 py-3 px-4 font-medium"
                                        rows="2" required></textarea>
                                </div>

                                <!-- Spiritual Status & Prayer Requests -->
                                <div
                                    class="col-span-full grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-white/5">
                                    <div class="space-y-4">
                                        <label
                                            class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Spiritual
                                            Status</label>
                                        <div class="flex flex-col gap-3">
                                            <label class="flex items-center gap-3 cursor-pointer group">
                                                <input type="checkbox" x-model="editingMember.born_again"
                                                    class="rounded-md border-slate-300 dark:border-white/10 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-500 transition-all">
                                                <span
                                                    class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Born
                                                    Again</span>
                                            </label>
                                            <label class="flex items-center gap-3 cursor-pointer group">
                                                <input type="checkbox" x-model="editingMember.water_baptism"
                                                    class="rounded-md border-slate-300 dark:border-white/10 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-500 transition-all">
                                                <span
                                                    class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Water
                                                    Baptism</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <label
                                            class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">Prayer
                                            Requests</label>
                                        <textarea x-model="editingMember.prayer_requests"
                                            class="block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 rounded-xl shadow-sm transition-all duration-300 py-3 px-4 text-xs resize-none"
                                            rows="3" placeholder="Enter prayer requests..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-8">
                                <button @click="showEditModal = false"
                                    class="px-6 py-3 text-slate-400 font-bold hover:text-slate-900 dark:hover:text-white transition-colors tracking-widest text-[10px] uppercase">Cancel</button>
                                <button @click="updateMember()"
                                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-xl transition-all shadow-lg shadow-indigo-600/20 tracking-widest text-[10px] uppercase">SAVE
                                    CHANGES</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Attendance Modal -->
        <template x-if="showAttendanceModal">
            <div class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showAttendanceModal = false">
                    </div>
                    <div
                        class="glass-card relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl p-8 max-w-lg w-full shadow-2xl">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-widest"
                                    x-text="editingMember.full_name"></h3>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Manage
                                    Attendance (3/3 threshold)</p>
                            </div>
                            <div class="px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl">
                                <span class="text-lg font-black text-indigo-600 dark:text-indigo-400"
                                    x-text="editingMember.service_count + '/3'"></span>
                            </div>
                        </div>

                        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="dateString in getModalDates()">
                                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-white/5 transition-all"
                                    :class="isPresent(dateString) ? 'border-emerald-500/30 bg-emerald-500/5' : ''">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white"
                                            x-text="new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                                        <template x-if="isInitial(dateString)">
                                            <span
                                                class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mt-1">Initial
                                                Visit</span>
                                        </template>
                                    </div>

                                    <button @click="toggleAttendance(dateString)" :disabled="isInitial(dateString)"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900"
                                        :class="isPresent(dateString) ? 'bg-emerald-500' : 'bg-slate-700'"
                                        :title="isInitial(dateString) ? 'Initial visit cannot be changed' : ''">
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                            :class="isPresent(dateString) ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <p class="mt-6 text-[9px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">
                            <span class="text-amber-500">Note:</span> Decreasing attendance below 3/3 will automatically
                            move this member back to the First Timers list for further follow-up.
                        </p>

                        <div class="mt-8">
                            <button @click="showAttendanceModal = false"
                                class="w-full py-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-white font-black rounded-2xl transition-all uppercase tracking-widest text-xs border border-slate-200 dark:border-white/5">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>