@php
    $user = auth()->user();
    $isSuperAdmin = $user->hasRole('Super Admin');

    if ($isSuperAdmin) {
        $categoriesCount = \App\Models\ChurchCategory::count();
        $groupsCount = \App\Models\ChurchGroup::count();
        $churchesCount = \App\Models\Church::count();
        $pcfsCount = \App\Models\Pcf::count();
        $officialsCount = \App\Models\User::role('Official')->count();
        $firstTimersCount = \App\Models\FirstTimer::count();
        $retainedMembersCount = \App\Models\RetainedMember::count();
        $unacknowledgedCount = \App\Models\RetainedMember::where('acknowledged', false)->count();
        $bringersCount = \App\Models\Bringer::count();
        $usersCount = \App\Models\User::count();
        $pendingApprovalsCount = \App\Models\User::where('is_approved', false)->count();
        $pendingDeletionsCount = \App\Models\User::where('pending_deletion', true)->count();
        $trashedCount = \App\Models\ChurchCategory::onlyTrashed()->count()
            + \App\Models\ChurchGroup::onlyTrashed()->count()
            + \App\Models\Church::onlyTrashed()->count()
            + \App\Models\Pcf::onlyTrashed()->count()
            + \App\Models\User::onlyTrashed()->count();
    } elseif ($user->hasRole('Official')) {
        $officialPcfIds = $user->pcfs()->pluck('id');
        $firstTimersCount = \App\Models\FirstTimer::whereIn('pcf_id', $officialPcfIds)->count();
        $retainedMembersCount = \App\Models\RetainedMember::whereIn('pcf_id', $officialPcfIds)->count();
        $unacknowledgedCount = \App\Models\RetainedMember::whereIn('pcf_id', $officialPcfIds)->where('acknowledged', false)->count();
        $bringersCount = \App\Models\Bringer::whereIn('pcf_id', $officialPcfIds)->count();
        $categoriesCount = 0;
        $groupsCount = 0;
        $churchesCount = 0;
        $pcfsCount = 0;
        $officialsCount = 0;
        $usersCount = 0;
    } else {
        $church = $user->church();
        $pcfIds = $church ? \App\Models\Pcf::where('church_group_id', $church->church_group_id)->pluck('id') : collect();
        $firstTimersCount = $church ? \App\Models\FirstTimer::where(function ($q) use ($church, $pcfIds) {
            $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
        })->count() : 0;
        $retainedMembersCount = $church ? \App\Models\RetainedMember::where(function ($q) use ($church, $pcfIds) {
            $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
        })->count() : 0;
        $unacknowledgedCount = $church ? \App\Models\RetainedMember::where(function ($q) use ($church, $pcfIds) {
            $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
        })->where('acknowledged', false)->count() : 0;
        $bringersCount = $church ? \App\Models\Bringer::where(function ($q) use ($church, $pcfIds) {
            $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
        })->count() : 0;
        $categoriesCount = 0;
        $groupsCount = 0;
        $churchesCount = 0;
        $pcfsCount = 0;
        $officialsCount = 0;
        $usersCount = 0;
    }
@endphp
<nav class="space-y-3" x-data="{ 
    expandedSections: JSON.parse(localStorage.getItem('sidebar_expanded_sections')) || { hierarchy: true, management: true, performance: true, settings: true },
    hoverSection: null,
    hoverTimeout: null,
    toggleSection(name) {
        if (sidebarMinimized) return;
        this.expandedSections[name] = !this.expandedSections[name];
        localStorage.setItem('sidebar_expanded_sections', JSON.stringify(this.expandedSections));
    },
    enterSection(name) {
        if (this.hoverTimeout) clearTimeout(this.hoverTimeout);
        this.hoverSection = name;
    },
    leaveSection() {
        this.hoverTimeout = setTimeout(() => {
            this.hoverSection = null;
        }, 300);
    }
}">
    <!-- Overview Section -->
    <div class="px-2">
        <div class="px-2 mb-2" x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Overview</p>
        </div>
        <a href="{{ route('dashboard') }}"
            class="nav-link-premium {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            :class="sidebarMinimized ? 'justify-center px-0' : ''" title="Dashboard">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0">Dashboard</span>
        </a>
    </div>

    <!-- Church Hierarchy Section (Super Admin Only) -->
    @role('Super Admin')
    <div class="relative px-2" @mouseenter="enterSection('hierarchy')" @mouseleave="leaveSection()">
        <button @click="toggleSection('hierarchy')"
            class="w-full flex items-center justify-between px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors group"
            :class="sidebarMinimized ? 'justify-center px-0' : ''" title="Church Hierarchy">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-500 group-hover:text-indigo-400 transition-colors shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0">Church Hierarchy</span>
            </div>
            <svg x-show="!sidebarMinimized" class="w-3 h-3 transition-transform duration-200"
                :class="expandedSections.hierarchy ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Expanded State (Desktop) --}}
        <div x-show="expandedSections.hierarchy && !sidebarMinimized" x-collapse
            class="space-y-1 px-4 border-l border-slate-800 ml-4 pb-2 mt-1">
            <a href="{{ route('church-categories.index') }}"
                class="nav-link-premium {{ request()->routeIs('church-categories.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Categories</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $categoriesCount }}</span>
            </a>
            <a href="{{ route('church-groups.index') }}"
                class="nav-link-premium {{ request()->routeIs('church-groups.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Groups</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $groupsCount }}</span>
            </a>
            <a href="{{ route('local-assemblies.index') }}"
                class="nav-link-premium {{ request()->routeIs('local-assemblies.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Church names</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[8px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-tighter">
                    NEW
                </span>
            </a>
            <a href="{{ route('churches.index') }}"
                class="nav-link-premium {{ request()->routeIs('churches.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Create Church</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $churchesCount }}</span>
            </a>
            <a href="{{ route('credentials.index') }}"
                class="nav-link-premium {{ request()->routeIs('credentials.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Credentials</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[8px] font-black bg-rose-500/10 text-rose-400 border border-rose-500/20 uppercase tracking-tighter">
                    Secure</span>
            </a>
            <a href="{{ route('pcfs.index') }}"
                class="nav-link-premium {{ request()->routeIs('pcfs.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">PCFs</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $pcfsCount }}</span>
            </a>
        </div>

        {{-- Flyout Menu (Minimized) --}}
        <div x-show="sidebarMinimized && hoverSection === 'hierarchy'" @mouseenter="enterSection('hierarchy')"
            @mouseleave="leaveSection()" class="sidebar-flyout" style="top: 100px;">
            <div class="px-3 py-2 border-b border-white/5 mb-1">
                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Church Hierarchy</p>
            </div>
            <a href="{{ route('church-categories.index') }}"
                class="sidebar-flyout-item flex items-center justify-between">
                <span>Categories</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $categoriesCount }}</span>
            </a>
            <a href="{{ route('church-groups.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                <span>Groups</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $groupsCount }}</span>
            </a>
            <a href="{{ route('local-assemblies.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                <span>Church names</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[8px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-tighter">
                    NEW
                </span>
            </a>
            <a href="{{ route('churches.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                <span>Create Church</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $churchesCount }}
                </span>
            </a>
            <a href="{{ route('credentials.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                <span>Credentials</span>
                <span
                    class="px-1 py-0.5 rounded text-[8px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/20">ADMIN</span>
            </a>
            <a href="{{ route('pcfs.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                <span>PCFs</span>
                <span
                    class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $pcfsCount }}</span>
            </a>
        </div>
    </div>
    @endrole

    <!-- Management Section -->
    @if($user->hasAnyRole(['Super Admin', 'Admin', 'Official']))
        <div class="relative px-2" @mouseenter="enterSection('management')" @mouseleave="leaveSection()">
            <button @click="toggleSection('management')"
                class="w-full flex items-center justify-between px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors group"
                :class="sidebarMinimized ? 'justify-center px-0' : ''" title="Management">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-400 transition-colors shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Management</span>
                </div>
                <svg x-show="!sidebarMinimized" class="w-3 h-3 transition-transform duration-200"
                    :class="expandedSections.management ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Expanded State (Desktop) --}}
            <div x-show="expandedSections.management && !sidebarMinimized" x-collapse
                class="space-y-1 px-4 border-l border-slate-800 ml-4 pb-2 mt-1">
                @if($user->hasAnyRole(['Super Admin', 'Admin']))
                    <a href="{{ route('bulk-upload.index') }}"
                        class="nav-link-premium {{ request()->routeIs('bulk-upload.*') ? 'active' : '' }} !py-2 !px-3">
                        <span class="text-xs">Bulk Upload</span>
                    </a>
                @endif
                @if($user->hasAnyRole(['Super Admin', 'Admin', 'Official']))
                    <a href="{{ route('first-timers.index') }}"
                        class="nav-link-premium {{ request()->routeIs('first-timers.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                        <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">First Timers</span>
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 sidebar-badge">{{ $firstTimersCount }}</span>
                    </a>
                    <a href="{{ route('retained-members.index') }}"
                        class="nav-link-premium {{ request()->routeIs('retained-members.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                        <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors flex items-center gap-1.5">Retained
                            @if($unacknowledgedCount > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if($unacknowledgedCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-500/15 text-amber-500 border border-amber-500/25 animate-pulse">{{ $unacknowledgedCount }} NEW</span>
                            @endif
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 sidebar-badge">{{ $retainedMembersCount }}</span>
                        </span>
                    </a>
                    <a href="{{ route('bringers.index') }}"
                        class="nav-link-premium {{ request()->routeIs('bringers.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                        <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Bringers</span>
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 sidebar-badge">{{ $bringersCount }}</span>
                    </a>
                @endif
                @if($user->hasAnyRole(['Super Admin', 'Admin']))
                    @role('Super Admin')
                    <a href="{{ route('officials.index') }}"
                        class="nav-link-premium {{ request()->routeIs('officials.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                        <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors">Officials</span>
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $officialsCount }}</span>
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="nav-link-premium {{ request()->routeIs('users.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                        <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors flex items-center gap-1.5">Users
                            @if((isset($pendingApprovalsCount) && $pendingApprovalsCount > 0) || (isset($pendingDeletionsCount) && $pendingDeletionsCount > 0))
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if(isset($pendingDeletionsCount) && $pendingDeletionsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-rose-500/15 text-rose-500 border border-rose-500/25 animate-pulse">{{ $pendingDeletionsCount }} DEL</span>
                            @endif
                            @if(isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-500/15 text-amber-500 border border-amber-500/25 animate-pulse">{{ $pendingApprovalsCount }} NEW</span>
                            @endif
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $usersCount }}</span>
                        </span>
                    </a>
                    @endrole
                @endif
                <a href="{{ route('attendance.index') }}"
                    class="nav-link-premium {{ request()->routeIs('attendance.*') ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">Attendance</span>
                </a>
                </a>
            </div>

            {{-- Flyout Menu (Minimized) --}}
            <div x-show="sidebarMinimized && hoverSection === 'management'" @mouseenter="enterSection('management')"
                @mouseleave="leaveSection()" class="sidebar-flyout" style="top: 160px;">
                <div class="px-3 py-2 border-b border-white/5 mb-1">
                    <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Management</p>
                </div>
                @if($user->hasAnyRole(['Super Admin', 'Admin']))
                    <a href="{{ route('bulk-upload.index') }}" class="sidebar-flyout-item">Bulk Upload</a>
                @endif
                @if($user->hasAnyRole(['Super Admin', 'Admin', 'Official']))
                    <a href="{{ route('first-timers.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                        <span>First Timers</span>
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 sidebar-badge">{{ $firstTimersCount }}</span>
                    </a>
                    <a href="{{ route('retained-members.index') }}"
                        class="sidebar-flyout-item flex items-center justify-between">
                        <span class="flex items-center gap-1.5">Retained
                            @if($unacknowledgedCount > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if($unacknowledgedCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-500/15 text-amber-500 border border-amber-500/25 animate-pulse">{{ $unacknowledgedCount }} NEW</span>
                            @endif
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 sidebar-badge">{{ $retainedMembersCount }}</span>
                        </span>
                    </a>
                    <a href="{{ route('bringers.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                        <span>Bringers</span>
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 sidebar-badge">{{ $bringersCount }}</span>
                    </a>
                @endif
                @if($user->hasAnyRole(['Super Admin', 'Admin']))
                    @role('Super Admin')
                    <a href="{{ route('officials.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                        <span>Officials</span>
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $officialsCount }}</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                        <span class="flex items-center gap-1.5">Users
                            @if((isset($pendingApprovalsCount) && $pendingApprovalsCount > 0) || (isset($pendingDeletionsCount) && $pendingDeletionsCount > 0))
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if(isset($pendingDeletionsCount) && $pendingDeletionsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-rose-500/15 text-rose-500 border border-rose-500/25 animate-pulse">{{ $pendingDeletionsCount }} DEL</span>
                            @endif
                            @if(isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-500/15 text-amber-500 border border-amber-500/25 animate-pulse">{{ $pendingApprovalsCount }} NEW</span>
                            @endif
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 sidebar-badge">{{ $usersCount }}</span>
                        </span>
                    </a>
                    @endrole
                @endif
                <a href="{{ route('attendance.index') }}" class="sidebar-flyout-item">Attendance</a>
            </div>
        </div>
    @endif

    <!-- Performance Section -->
    @if($user->hasAnyRole(['Super Admin', 'Admin', 'Official']))
        <div class="relative px-2" @mouseenter="enterSection('performance')" @mouseleave="leaveSection()">
            <button @click="toggleSection('performance')"
                class="w-full flex items-center justify-between px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors group"
                :class="sidebarMinimized ? 'justify-center px-0' : ''" title="Performance">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-indigo-400 transition-colors shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Performance</span>
                </div>
                <svg x-show="!sidebarMinimized" class="w-3 h-3 transition-transform duration-200"
                    :class="expandedSections.performance ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Expanded State (Desktop) --}}
            <div x-show="expandedSections.performance && !sidebarMinimized" x-collapse
                class="space-y-1 px-4 border-l border-slate-800 ml-4 pb-2 mt-1">
                @role('Super Admin')
                <a href="{{ route('performance.index', ['type' => 'pcf']) }}"
                    class="nav-link-premium {{ request()->fullUrl() == route('performance.index', ['type' => 'pcf']) ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">PCFs</span>
                </a>
                <a href="{{ route('performance.index', ['type' => 'church']) }}"
                    class="nav-link-premium {{ request()->fullUrl() == route('performance.index', ['type' => 'church']) ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">Churches</span>
                </a>
                @endrole
                @role('Admin')
                <a href="{{ route('performance.index') }}"
                    class="nav-link-premium {{ request()->routeIs('performance.*') ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">My Church</span>
                </a>
                @endrole
                @role('Official')
                <a href="{{ route('performance.index') }}"
                    class="nav-link-premium {{ request()->routeIs('performance.*') ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">My Performance</span>
                </a>
                @endrole
                <a href="{{ route('reporting.index') }}"
                    class="nav-link-premium {{ request()->routeIs('reporting.*') ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">Reporting Overview</span>
                </a>
            </div>

            {{-- Flyout Menu (Minimized) --}}
            <div x-show="sidebarMinimized && hoverSection === 'performance'" @mouseenter="enterSection('performance')"
                @mouseleave="leaveSection()" class="sidebar-flyout" style="top: 220px;">
                <div class="px-3 py-2 border-b border-white/5 mb-1">
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Performance</p>
                </div>
                @role('Super Admin')
                <a href="{{ route('performance.index', ['type' => 'pcf']) }}" class="sidebar-flyout-item">PCFs</a>
                <a href="{{ route('performance.index', ['type' => 'church']) }}" class="sidebar-flyout-item">Churches</a>
                @endrole
                @role('Admin')
                <a href="{{ route('performance.index') }}" class="sidebar-flyout-item">My Church</a>
                @endrole
                @role('Official')
                <a href="{{ route('performance.index') }}" class="sidebar-flyout-item">My Performance</a>
                @endrole
                <a href="{{ route('reporting.index') }}" class="sidebar-flyout-item">Reporting Overview</a>
            </div>
        </div>
    @endif

    <!-- Settings Section -->
    <div class="relative px-2" @mouseenter="enterSection('settings')" @mouseleave="leaveSection()">
        @if(auth()->user()->hasRole('Super Admin'))
            <button @click="toggleSection('settings')"
                class="w-full flex items-center justify-between px-3 py-2 text-[9px] font-bold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors group"
                :class="sidebarMinimized ? 'justify-center px-0' : ''" title="Settings">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-indigo-400 transition-colors shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Settings</span>
                </div>
                <svg x-show="!sidebarMinimized" class="w-3 h-3 transition-transform duration-200"
                    :class="expandedSections.settings ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Expanded State (Desktop) --}}
            <div x-show="expandedSections.settings && !sidebarMinimized" x-collapse
                class="space-y-1 px-4 border-l border-slate-800 ml-4 pb-2 mt-1">
                <a href="{{ route('homepage-settings.edit') }}"
                    class="nav-link-premium {{ request()->routeIs('homepage-settings.edit') ? 'active' : '' }} !py-2 !px-3">
                    <span class="text-xs">Homepage</span>
                </a>
                <a href="{{ route('trash.index') }}"
                    class="nav-link-premium {{ request()->routeIs('trash.*') ? 'active' : '' }} !py-2 !px-3 group/nav flex items-center justify-between">
                    <span class="text-xs text-slate-400 group-hover/nav:text-white transition-colors flex items-center gap-1.5">Trash
                        @if(isset($trashedCount) && $trashedCount > 0)
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                        @endif
                    </span>
                    @if(isset($trashedCount) && $trashedCount > 0)
                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-rose-500/15 text-rose-400 border border-rose-500/25 animate-pulse">{{ $trashedCount }}</span>
                    @else
                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-slate-500/10 text-slate-500 border border-slate-500/20">0</span>
                    @endif
                </a>

            </div>

            {{-- Flyout Menu (Minimized) --}}
            <div x-show="sidebarMinimized && hoverSection === 'settings'" @mouseenter="enterSection('settings')"
                @mouseleave="leaveSection()" class="sidebar-flyout" style="top: 220px;">
                <div class="px-3 py-2 border-b border-white/5 mb-1">
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Settings</p>
                </div>
                <a href="{{ route('homepage-settings.edit') }}" class="sidebar-flyout-item">Homepage</a>
                <a href="{{ route('trash.index') }}" class="sidebar-flyout-item flex items-center justify-between">
                    <span class="flex items-center gap-1.5">Trash
                        @if(isset($trashedCount) && $trashedCount > 0)
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                        @endif
                    </span>
                    @if(isset($trashedCount) && $trashedCount > 0)
                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-rose-500/15 text-rose-400 border border-rose-500/25 animate-pulse">{{ $trashedCount }}</span>
                    @else
                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-slate-500/10 text-slate-500 border border-slate-500/20">0</span>
                    @endif
                </a>

            </div>
        @endif
    </div>
</nav>