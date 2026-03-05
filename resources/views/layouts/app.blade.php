<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')

    <!-- Prevent FOUC: hide x-cloak elements until Alpine initializes -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Pre-Alpine: immediately apply minimized sidebar state before Alpine boots */
        html.sidebar-is-minimized .glass-sidebar {
            width: 5rem !important;
        }

        html.sidebar-is-minimized .glass-sidebar .sidebar-expandable {
            display: none !important;
        }

        html.sidebar-is-minimized .glass-sidebar .profile-img-container {
            width: 2rem !important;
            height: 2rem !important;
            font-size: 0.65rem !important;
        }
    </style>
    <script>
        // Apply theme and sidebar state immediately before Alpine boots to prevent flash
        (function () {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            if (localStorage.getItem('sidebar_minimized') === 'true') {
                document.documentElement.classList.add('sidebar-is-minimized');
            }
        })();
    </script>
</head>

<body class="font-sans antialiased text-slate-900 dark:text-slate-200 overflow-x-hidden" x-data="{ 
    sidebarMinimized: localStorage.getItem('sidebar_minimized') === 'true', 
    mobileSidebarOpen: false,
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleTheme() {
        this.darkMode = !this.darkMode;
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}">
    <div class="min-h-screen flex" id="app-root">
        <!-- Sidebar -->
        <aside class="glass-sidebar hidden lg:flex" :class="sidebarMinimized ? 'minimized' : ''"
            x-init="$el.closest('html').classList.remove('sidebar-is-minimized')">
            <div class="flex flex-col h-full w-full overflow-hidden">
                <!-- Static Sidebar Header -->
                <div class="border-b border-slate-800/50 bg-slate-900/50 backdrop-blur-xl z-20 transition-all duration-300"
                    :class="sidebarMinimized ? 'p-4' : 'p-6'">
                    <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                        <div
                            class="shrink-0 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 p-2 text-white">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <div x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0" class="sidebar-expandable">
                            <h1 class="font-bold text-sm tracking-tight text-white">CENTURION CAMPAIGN</h1>
                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">100 souls per
                                member</p>
                        </div>
                    </div>

                    {{-- Toggle Button --}}
                    <button
                        @click="sidebarMinimized = !sidebarMinimized; localStorage.setItem('sidebar_minimized', sidebarMinimized)"
                        class="absolute -right-3 top-20 w-6 h-6 bg-slate-800 border border-slate-700 rounded-full flex items-center justify-center text-slate-400 hover:text-white transition-all z-50">
                        <svg class="w-4 h-4 transition-transform duration-300"
                            :class="sidebarMinimized ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Scrollable Sidebar Content -->
                <div class="glass-sidebar-content flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar"
                    :class="sidebarMinimized ? 'p-2' : 'p-4'">
                    @include('layouts.navigation-sidebar')
                </div>

                <!-- Sidebar Footer (Logout) -->
                <div class="mt-auto border-t border-slate-800 bg-slate-900/80 backdrop-blur-xl transition-all duration-300"
                    :class="sidebarMinimized ? 'p-3' : 'p-6'">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 mb-6 px-1 overflow-hidden group/user hover:bg-white/5 p-2 rounded-xl transition-all">
                        <div class="shrink-0 overflow-hidden rounded-xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm transition-all group-hover/user:bg-indigo-600 group-hover/user:text-white profile-img-container"
                            :class="sidebarMinimized ? 'w-8 h-8 text-xs' : 'w-10 h-10'">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                    class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <div x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            class="sidebar-expandable">
                            <p
                                class="text-[11px] font-bold text-white truncate max-w-[120px] group-hover/user:text-indigo-400 transition-colors">
                                {{ Auth::user()->title ? Auth::user()->title . ' ' : '' }}{{ Auth::user()->name }}
                            </p>
                            <p class="text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                {{ Auth::user()->getRoleNames()->first() }}
                            </p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] uppercase tracking-[2px] rounded-xl shadow-lg shadow-red-600/20 transition-all active:scale-95"
                            :class="sidebarMinimized ? 'justify-center px-0 h-10' : 'px-4'">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span x-show="!sidebarMinimized">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Drawer (Overlay) -->
        <div x-show="mobileSidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileSidebarOpen = false"
            class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[60] lg:hidden" x-cloak>
        </div>

        <aside x-show="mobileSidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 w-72 glass-sidebar z-[70] lg:hidden flex flex-col" x-cloak>
            <div class="flex flex-col h-full w-full overflow-hidden">
                <div class="p-6 border-b border-slate-800/50 bg-slate-900/50 backdrop-blur-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center p-1.5 text-white">
                                <x-application-logo class="w-full h-full" />
                            </div>
                            <span class="font-bold text-sm text-white tracking-tight">CENTURION</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    @include('layouts.navigation-sidebar')
                </div>

                <!-- Mobile Sidebar Footer -->
                <div class="mt-auto border-t border-slate-800 bg-slate-900/80 backdrop-blur-xl p-6">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 mb-6 px-1 overflow-hidden group/user">
                        <div
                            class="shrink-0 w-10 h-10 overflow-hidden rounded-xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                    class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-white truncate max-w-[120px]">
                                {{ Auth::user()->title ? Auth::user()->title . ' ' : '' }}{{ Auth::user()->name }}
                            </p>
                            <p class="text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                {{ Auth::user()->getRoleNames()->first() }}
                            </p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] uppercase tracking-[2px] rounded-xl shadow-lg shadow-red-600/20 transition-all active:scale-95 px-4">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content-wrapper" :class="sidebarMinimized ? 'sidebar-minimized' : ''">
            <!-- Top Header -->
            <header
                class="h-14 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 md:px-8 sticky top-0 bg-[var(--header-bg)] backdrop-blur-xl z-40 transition-colors">
                <div class="flex items-center gap-4">
                    <button @click="mobileSidebarOpen = true"
                        class="lg:hidden text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                    @if($showBack)
                        <button onclick="history.back()"
                            class="text-slate-500 hover:text-white transition-all hover:bg-white/5 rounded-xl p-2 active:scale-90"
                            title="Go Back">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </button>
                    @endif
                    @isset($header)
                        <div class="text-indigo-400 font-black uppercase tracking-[3px] text-xs">
                            {{ $header }}
                        </div>
                    @endisset
                </div>
                <div class="flex items-center gap-2">
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()"
                        class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all"
                        title="Toggle Theme">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 16.243l.707.707M7.757 7.757l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 md:p-8 flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        // Auto-hide flash messages after 5 seconds - strictly target elements with .flash-alert class, but EXCLUDE errors
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.flash-alert');
            alerts.forEach(alert => {
                // Do not auto-hide errors (rose-500 class or similar)
                if (alert.classList.contains('bg-rose-500/10')) return;

                setTimeout(() => {
                    alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 600);
                }, 5000);
            });
        });
    </script>
</body>

</html>