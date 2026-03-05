<x-app-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <div class="space-y-8">
        @if(auth()->user()->hasRole('Super Admin') && ($stats['pending_approvals_count'] ?? 0) > 0)
            <div
                class="glass-card mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-pulse-subtle">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-500/15 rounded-xl">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-amber-600 dark:text-amber-400">
                            {{ $stats['pending_approvals_count'] }} Pending
                            Approval{{ $stats['pending_approvals_count'] !== 1 ? 's' : '' }}
                        </p>
                        <p class="text-[10px] font-bold text-amber-600/70 dark:text-amber-500/70 uppercase tracking-widest">
                            Review and approve new admin accounts</p>
                    </div>
                </div>
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                    Review Now
                </a>
            </div>
        @endif

        <!-- Top Row Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 lg:gap-4">
            <!-- Total First Timers -->
            <div class="glass-card p-4 flex items-center justify-between group">
                <div>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total First Timers
                    </p>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">
                        {{ $stats['total_first_timers'] ?? 0 }}
                    </h3>
                </div>
                <div
                    class="stat-icon bg-cyan-500/20 text-cyan-400 group-hover:bg-cyan-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(6,182,212,0.4)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            <!-- New First Timers -->
            <div class="glass-card p-4 flex items-center justify-between group">
                <div>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">New First Timers</p>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $stats['new_first_timers'] ?? 0 }}
                    </h3>
                </div>
                <div
                    class="stat-icon bg-orange-500/20 text-orange-400 group-hover:bg-orange-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(249,115,22,0.4)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>

            <!-- Retained Members -->
            <div class="glass-card p-4 flex items-center justify-between group">
                <div>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Retained</p>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $stats['total_retained'] ?? 0 }}
                    </h3>
                </div>
                <div
                    class="stat-icon bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <!-- Offerers (1-9) -->
            <div class="glass-card p-4 flex items-center justify-between group cursor-pointer hover:border-blue-500/30 transition-all duration-300"
                onclick="openTierModal('Offerers', {{ json_encode($stats['tiers']['offerers']['bringers'] ?? []) }})">
                <div>
                    <p class="text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">
                        Offerers</p>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors">
                        {{ $stats['tiers']['offerers']['count'] ?? 0 }}
                    </h3>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="stat-icon bg-blue-500/10 text-blue-400 group-hover:bg-blue-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(59,130,246,0.4)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[9px] font-bold border border-blue-500/20 whitespace-nowrap">1-9
                        Souls</span>
                </div>
            </div>

            <!-- Tithers (10-49) -->
            <div class="glass-card p-4 flex items-center justify-between group cursor-pointer hover:border-purple-500/30 transition-all duration-300"
                onclick="openTierModal('Tithers', {{ json_encode($stats['tiers']['tithers']['bringers'] ?? []) }})">
                <div>
                    <p class="text-[9px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1">
                        Tithers</p>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white group-hover:text-purple-600 transition-colors">
                        {{ $stats['tiers']['tithers']['count'] ?? 0 }}
                    </h3>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="stat-icon bg-purple-500/10 text-purple-400 group-hover:bg-purple-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(168,85,247,0.4)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 text-[9px] font-bold border border-purple-500/20 whitespace-nowrap">10-49
                        Souls</span>
                </div>
            </div>

            <!-- Jubilee (50-99) -->
            <div class="glass-card p-4 flex items-center justify-between group cursor-pointer hover:border-pink-500/30 transition-all duration-300"
                onclick="openTierModal('Jubilee', {{ json_encode($stats['tiers']['jubilee']['bringers'] ?? []) }})">
                <div>
                    <p class="text-[9px] font-bold text-pink-600 dark:text-pink-400 uppercase tracking-widest mb-1">
                        Jubilee</p>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white group-hover:text-pink-600 transition-colors">
                        {{ $stats['tiers']['jubilee']['count'] ?? 0 }}
                    </h3>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="stat-icon bg-pink-500/10 text-pink-400 group-hover:bg-pink-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(236,72,153,0.4)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded-full bg-pink-500/10 text-pink-400 text-[9px] font-bold border border-pink-500/20 whitespace-nowrap">50-99
                        Souls</span>
                </div>
            </div>

            <!-- Centurion (100-999) -->
            <div class="glass-card p-4 flex items-center justify-between group cursor-pointer hover:border-amber-500/30 transition-all duration-300"
                onclick="openTierModal('Centurion', {{ json_encode($stats['tiers']['centurion']['bringers'] ?? []) }})">
                <div>
                    <p class="text-[9px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">
                        Centurion</p>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white group-hover:text-amber-600 transition-colors">
                        {{ $stats['tiers']['centurion']['count'] ?? 0 }}
                    </h3>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="stat-icon bg-amber-500/10 text-amber-400 group-hover:bg-amber-500 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(245,158,11,0.4)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[9px] font-bold border border-amber-500/20 whitespace-nowrap">100-999
                        Souls</span>
                </div>
            </div>

            <!-- Millennial (1000+) -->
            <div class="glass-card p-4 flex items-center justify-between group cursor-pointer hover:border-yellow-400/30 transition-all duration-300"
                onclick="openTierModal('Millennial', {{ json_encode($stats['tiers']['millennial']['bringers'] ?? []) }})">
                <div>
                    <p class="text-[9px] font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-widest mb-1">
                        Millennial</p>
                    <h3
                        class="text-xl font-black text-slate-900 dark:text-white group-hover:text-yellow-600 transition-colors">
                        {{ $stats['tiers']['millennial']['count'] ?? 0 }}
                    </h3>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="stat-icon bg-yellow-400/10 text-yellow-500 group-hover:bg-yellow-400 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(250,204,21,0.4)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded-full bg-yellow-400/10 text-yellow-500 text-[9px] font-bold border border-yellow-400/20 whitespace-nowrap">1000+
                        Souls</span>
                </div>
            </div>
        </div>

        <!-- Lower Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Gender Distribution -->
            <div class="glass-card p-5">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Gender Distribution
                    </h3>
                    @unless($stats['is_super_admin'] ?? false)
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                            {{ $stats['church_name'] ?? 'Global Members' }}
                        </div>
                    @endunless
                </div>

                @if($stats['is_super_admin'] ?? false)
                    <div class="space-y-10">
                        <!-- Main Church Section -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="h-px flex-1 bg-white/5"></div>
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-[3px]">Zonal
                                    Church</span>
                                <div class="h-px flex-1 bg-white/5"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <div class="flex justify-between text-[10px] font-bold mb-2">
                                        <span class="text-slate-500 uppercase tracking-widest">MALE</span>
                                        <span
                                            class="text-slate-900 dark:text-white font-black">{{ $stats['gender_dist_main']['male'] ?? 0 }}
                                            ({{ $stats['gender_dist_main']['male_percent'] ?? 0 }}%)</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)] transition-all duration-1000"
                                            style="width: {{ $stats['gender_dist_main']['male_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-[10px] font-bold mb-2">
                                        <span class="text-slate-500 uppercase tracking-widest">FEMALE</span>
                                        <span
                                            class="text-slate-900 dark:text-white font-black">{{ $stats['gender_dist_main']['female'] ?? 0 }}
                                            ({{ $stats['gender_dist_main']['female_percent'] ?? 0 }}%)</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)] transition-all duration-1000"
                                            style="width: {{ $stats['gender_dist_main']['female_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Churches Section -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="h-px flex-1 bg-white/5"></div>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-[3px]">Other
                                    Churches</span>
                                <div class="h-px flex-1 bg-white/5"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <div class="flex justify-between text-[10px] font-bold mb-2">
                                        <span class="text-slate-500 uppercase tracking-widest">MALE</span>
                                        <span
                                            class="text-slate-900 dark:text-white font-black">{{ $stats['gender_dist_other']['male'] ?? 0 }}
                                            ({{ $stats['gender_dist_other']['male_percent'] ?? 0 }}%)</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500/60 shadow-[0_0_10px_rgba(99,102,241,0.3)] transition-all duration-1000"
                                            style="width: {{ $stats['gender_dist_other']['male_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-[10px] font-bold mb-2">
                                        <span class="text-slate-500 uppercase tracking-widest">FEMALE</span>
                                        <span
                                            class="text-slate-900 dark:text-white font-black">{{ $stats['gender_dist_other']['female'] ?? 0 }}
                                            ({{ $stats['gender_dist_other']['female_percent'] ?? 0 }}%)</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-rose-500/60 shadow-[0_0_10px_rgba(244,63,94,0.3)] transition-all duration-1000"
                                            style="width: {{ $stats['gender_dist_other']['female_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-8">
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-2">
                                <span class="text-slate-400 dark:text-slate-500">MALE</span>
                                <span class="text-slate-900 dark:text-white">{{ $stats['gender_dist']['male'] ?? 0 }}
                                    ({{ $stats['gender_dist']['male_percent'] ?? 0 }}%)</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-700/50 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)] transition-all duration-1000"
                                    style="width: {{ $stats['gender_dist']['male_percent'] ?? 0 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold mb-2">
                                <span class="text-slate-400 dark:text-slate-500">FEMALE</span>
                                <span class="text-slate-900 dark:text-white">{{ $stats['gender_dist']['female'] ?? 0 }}
                                    ({{ $stats['gender_dist']['female_percent'] ?? 0 }}%)</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-700/50 rounded-full overflow-hidden">
                                <div class="h-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)] transition-all duration-1000"
                                    style="width: {{ $stats['gender_dist']['female_percent'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Birthday Reminders -->
            <div class="glass-card p-5">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">🎂 Birthday Reminders
                    </h3>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        Upcoming</div>
                </div>

                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-slate-700">
                    @forelse($stats['birthday_reminders'] ?? [] as $churchName => $celebrants)
                        <div class="space-y-2">
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[2px] mb-2">
                                {{ $churchName }}
                            </p>
                            @foreach($celebrants as $person)
                                <div
                                    class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5 hover:border-indigo-500/30 transition-all">
                                    <span
                                        class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $person->full_name }}</span>
                                    <span
                                        class="text-[10px] px-2 py-1 bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 rounded-md font-bold">
                                        {{ preg_match('/^\d{2}-\d{2}$/', $person->date_of_birth) ? \Carbon\Carbon::createFromFormat('d-m', $person->date_of_birth)->format('M d') : \Carbon\Carbon::parse($person->date_of_birth)->format('M d') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 opacity-30">
                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-sm">No birthdays this month</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- pastor Welcome Modal --}}
    <x-registration.welcome-modal :user="auth()->user()" :settings="$homepageSettings" />

    {{-- Tier Bringers Modal --}}
    <div id="tierModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-3xl transform transition-all duration-300 scale-95 opacity-0 overflow-hidden flex flex-col max-h-[90vh]"
            id="tierModalContent">
            <!-- Modal Header -->
            <div
                class="p-5 border-b border-slate-200 dark:border-white/5 flex justify-between items-center bg-slate-50 dark:bg-white/5">
                <div>
                    <h3 id="tierModalTitle"
                        class="text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        <span id="tierModalIcon"
                            class="p-2 rounded-lg bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                            <!-- Icon injected via JS -->
                        </span>
                        <span id="tierModalName">Tier Name</span>
                    </h3>
                    <p id="tierModalSubtitle"
                        class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-bold uppercase tracking-wide">
                        Showing all
                        bringers in this category</p>
                </div>
                <button onclick="closeTierModal()"
                    class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div
                class="p-5 overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-700">
                <div id="tierModalList" class="space-y-3">
                    <!-- List injected via JS -->
                </div>

                <!-- Empty State -->
                <div id="tierModalEmpty" class="hidden flex-col items-center justify-center py-12 opacity-50">
                    <svg class="w-16 h-16 mb-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-slate-300 font-medium">No bringers found in this tier yet.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openTierModal(tierName, bringers) {
                const modal = document.getElementById('tierModal');
                const content = document.getElementById('tierModalContent');
                const title = document.getElementById('tierModalName');
                const list = document.getElementById('tierModalList');
                const empty = document.getElementById('tierModalEmpty');
                const iconContainer = document.getElementById('tierModalIcon');
                const subtitle = document.getElementById('tierModalSubtitle');

                // Set Title & styling based on tier
                title.textContent = tierName;

                let iconSvg = '';
                let colorClass = '';

                // Note: counts are based on actual total souls (first_timers + retained_members)
                switch (tierName) {
                    case 'Offerers': // 1-9
                        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>';
                        colorClass = 'text-blue-400 bg-blue-500/20';
                        subtitle.textContent = 'Bringers with 1-9 souls';
                        break;
                    case 'Tithers': // 10-49
                        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>';
                        colorClass = 'text-purple-400 bg-purple-500/20';
                        subtitle.textContent = 'Bringers with 10-49 souls';
                        break;
                    case 'Jubilee': // 50-99
                        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>';
                        colorClass = 'text-pink-400 bg-pink-500/20';
                        subtitle.textContent = 'Bringers with 50-99 souls';
                        break;
                    case 'Centurion': // 100-999
                        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>';
                        colorClass = 'text-amber-400 bg-amber-500/20';
                        subtitle.textContent = 'Bringers with 100-999 souls';
                        break;
                    case 'Millennial': // 1000+
                        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>';
                        colorClass = 'text-yellow-400 bg-yellow-400/20';
                        subtitle.textContent = 'Bringers with 1000+ souls';
                        break;
                    default:
                        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>';
                        colorClass = 'text-indigo-400 bg-indigo-500/20';
                }

                iconContainer.innerHTML = iconSvg;
                iconContainer.className = `p-2 rounded-lg ${colorClass}`;

                // Populate List
                list.innerHTML = '';

                if (bringers && bringers.length > 0) {
                    list.classList.remove('hidden');
                    empty.classList.add('hidden');

                    bringers.forEach(bringer => {
                        const totalSouls = (bringer.first_timers_count || 0) + (bringer.retained_members_count || 0);

                        const item = document.createElement('div');
                        item.className = 'flex items-center justify-between p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 hover:border-indigo-500/30 transition-colors group';
                        item.innerHTML = `
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold uppercase">
                                                        ${bringer.name ? bringer.name.substring(0, 2) : 'UK'}
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">${bringer.name || 'Unknown'}</h4>
                                                        <div class="flex items-center gap-3 mt-1">
                                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 font-medium">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                                    ${bringer.contact || 'N/A'}
                                                                </span>
                                                                ${bringer.senior_cell_name ? `
                                                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                                    ${bringer.senior_cell_name}
                                                                </span>
                                                                ` : ''}
                                                                ${bringer.cell_name ? `
                                                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                                    ${bringer.cell_name}
                                                                </span>
                                                                ` : ''}
                                                                ${bringer.fellowship_name && bringer.fellowship_name !== 'Unassigned' ? `
                                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-black bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border border-yellow-500/20 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                                                ${bringer.fellowship_name}
                                                            </span>
                                                                ` : ''}
                                                            </div>
                                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                                                                ${bringer.first_timers_count || 0} First Timers
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                                ${bringer.retained_members_count || 0} Retained
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                                                ${bringer.retention_percentage || 0}% Retention
                                                            </span>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col items-end justify-center min-w-[80px]">
                                                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">TOTAL SOULS</span>
                                                    <span class="text-xl font-black text-slate-900 dark:text-white leading-none">${totalSouls}</span>
                                                </div>
                                                `;
                        list.appendChild(item);
                    });
                } else {
                    list.classList.add('hidden');
                    empty.classList.remove('hidden');
                    empty.classList.add('flex');
                }

                // Show modal with animation
                modal.classList.remove('hidden');
                // Small delay to allow display:block to apply before animating opacity/transform
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeTierModal() {
                const modal = document.getElementById('tierModal');
                const content = document.getElementById('tierModalContent');

                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300); // Wait for transition
            }

            // Close on outside click
            document.getElementById('tierModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeTierModal();
                }
            });
        </script>
    @endpush
</x-app-layout>