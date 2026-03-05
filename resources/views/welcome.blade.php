<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->hero_heading ?? 'Centurion Campaign' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,600,700,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .glass-header {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-gradient {
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)),
                url('{{ $settings->background_image ? asset("storage/" . $settings->background_image) : "https://images.unsplash.com/photo-1510076857177-7470076d4098?auto=format&fit=crop&q=80" }}');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(180deg, rgba(79, 70, 229, 0.2) 0%, rgba(124, 58, 237, 0.2) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: 0;
            animation: move 25s infinite alternate;
            pointer-events: none;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
            animation-duration: 30s;
            animation-delay: -5s;
        }

        @keyframes move {
            from {
                transform: translate(-10%, -10%) rotate(0deg);
            }

            to {
                transform: translate(20%, 20%) rotate(360deg);
            }
        }

        .content-z {
            position: relative;
            z-index: 10;
        }

        .premium-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.5);
        }
    </style>
</head>

<body class="antialiased bg-slate-950 text-white font-['Outfit']"
    x-data="{ mouseX: 0, mouseY: 0, mobileMenuOpen: false }"
    @mousemove="mouseX = $event.clientX; mouseY = $event.clientY">
    <div class="relative min-h-screen hero-gradient flex flex-col">
        <!-- Interactive Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="blob left-[-10%] top-[-10%]"
                :style="`transform: translate(${mouseX * 0.03}px, ${mouseY * 0.03}px)`"></div>
            <div class="blob blob-2 right-[-5%] bottom-[-5%]"
                :style="`transform: translate(${mouseX * -0.02}px, ${mouseY * -0.02}px)`"></div>
        </div>

        <!-- Navigation -->
        <header class="glass-header fixed top-0 left-0 right-0 z-[60]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-24 flex items-center justify-center transition-all -my-4 p-1">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <span class="font-black text-xl tracking-tight uppercase">Centurion Campaign</span>
                    </div>

                    <nav class="hidden md:flex items-center gap-8">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="text-sm font-bold text-slate-300 hover:text-white transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="text-sm font-bold text-slate-300 hover:text-white transition-colors">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('pastor.register') }}"
                                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-sm font-black transition-all premium-button">
                                        JOIN THE CAMPAIGN
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </nav>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-slate-300 p-2 hover:text-white transition-colors relative z-50">
                            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                            <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="md:hidden glass-header border-t border-white/5 fixed inset-x-0 top-20 h-[calc(100vh-80px)] overflow-y-auto pt-4 pb-8 px-4"
                x-cloak>
                <div class="flex flex-col gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="block p-4 text-lg font-bold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition-all">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="block p-4 text-lg font-bold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition-all">Log
                                in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('pastor.register') }}"
                                    class="block p-6 bg-indigo-600 text-center rounded-2xl text-lg font-black shadow-lg shadow-indigo-600/30">
                                    JOIN THE CAMPAIGN
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Content -->
        <main class="flex-grow flex items-center pt-40 pb-32 content-z">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full text-center">
                <div class="max-w-4xl mx-auto">
                    <span
                        class="inline-block px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-black tracking-[3px] uppercase mb-2">
                        {{ $settings->hero_subtext ?? '100 souls per member' }}
                    </span>

                    <div class="flex justify-center mb-2">
                        <x-application-logo class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px]" />
                    </div>

                    <p
                        class="text-base sm:text-lg md:text-2xl text-slate-400 font-light leading-relaxed mb-12 max-w-2xl mx-auto px-4">
                        {{ $settings->hero_description ?? 'Join the movement to win 100 souls per member. Let\'s saturate our world with the gospel of Jesus Christ.' }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('pastor.register') }}"
                            class="w-full sm:w-auto px-10 py-5 bg-indigo-600 hover:bg-indigo-500 rounded-2xl text-base font-black transition-all premium-button shadow-2xl shadow-indigo-600/30">
                            REGISTER AS PASTOR
                        </a>
                        <a href="#about"
                            class="px-8 py-4 rounded-2xl border border-white/10 font-bold hover:bg-white/5 transition-all active:scale-95">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Aim & Objectives Section -->
        <section id="about" class="py-24 bg-slate-900/30 content-z border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-6xl font-black mb-4 tracking-tighter">
                        @if($settings->objectives_title)
                            @php
                                $titleParts = explode(' ', $settings->objectives_title);
                                $lastWord = array_pop($titleParts);
                                $mainTitle = implode(' ', $titleParts);
                            @endphp
                            {{ $mainTitle }} <span class="text-indigo-500">{{ $lastWord }}</span>
                        @else
                            Our Aim & <span class="text-indigo-500">Objectives</span>
                        @endif
                    </h2>
                    <p class="text-slate-400 max-w-2xl mx-auto font-medium">
                        {{ $settings->objectives_subtitle ?? 'We are committed to the comprehensive growth and integration of every soul that walks through our doors.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Objective 1 -->
                    <div class="glass-card p-8 group hover:border-indigo-500/30 transition-all duration-500">
                        <div
                            class="w-14 h-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black mb-4">{{ $settings->obj_1_title ?? 'Nurturing Souls' }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {{ $settings->obj_1_description ?? 'Dedicated follow-up systems ensuring no one is left behind in their spiritual journey after their first visit.' }}
                        </p>
                    </div>

                    <!-- Objective 2 -->
                    <div class="glass-card p-8 group hover:border-emerald-500/30 transition-all duration-500">
                        <div
                            class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black mb-4">{{ $settings->obj_2_title ?? 'Foundation School' }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {{ $settings->obj_2_description ?? 'A structured curriculum designed to ground new converts in the core doctrines of the faith and church vision.' }}
                        </p>
                    </div>

                    <!-- Objective 3 -->
                    <div class="glass-card p-8 group hover:border-rose-500/30 transition-all duration-500">
                        <div
                            class="w-14 h-14 bg-rose-500/10 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black mb-4">{{ $settings->obj_3_title ?? 'Membership Integration' }}
                        </h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {{ $settings->obj_3_description ?? 'Transitioning first timers into fully integrated, productive members of the local church community and workforce.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-white/5 content-z">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-slate-500 text-sm italic font-medium">"Go ye into all the world, and preach the gospel to
                    every creature." — Mark 16:15</p>
                <p class="text-slate-600 text-[10px] mt-4 font-black uppercase tracking-widest">&copy; {{ date('Y') }}
                    CENTURION CAMPAIGN. ALL RIGHTS RESERVED.</p>
            </div>
        </footer>
    </div>
</body>

</html>