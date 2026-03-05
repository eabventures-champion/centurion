@props(['user', 'settings'])

@if(session('show_pastor_welcome') && $settings && $settings->show_welcome_modal)
    <div x-data="{ 
                    show: true,
                    init() {
                        setTimeout(() => {
                            // Auto close after 10 seconds if user doesn't close
                            // this.show = false;
                        }, 10000);
                    }
                }" x-show="show" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 overflow-hidden"
        @keydown.escape.window="show = false">

        <!-- Backdrop with blur -->
        <div x-show="show" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 backdrop-blur-0" x-transition:enter-end="opacity-100 backdrop-blur-xl"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 backdrop-blur-xl"
            x-transition:leave-end="opacity-0 backdrop-blur-0" class="absolute inset-0 bg-slate-950/60"
            @click="show = false">
        </div>

        <!-- Modal Content -->
        <div x-show="show" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-8"
            class="relative w-full max-w-2xl bg-slate-900/40 border border-white/10 rounded-[2.5rem] shadow-2xl p-8 sm:p-12 overflow-hidden backdrop-blur-2xl">

            <!-- Animated Background Glow -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-indigo-600/20 rounded-full blur-[80px] animate-pulse"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-600/20 rounded-full blur-[80px] animate-pulse">
            </div>

            <div class="relative z-10 text-center space-y-8">
                <!-- Church Animation -->
                <div class="flex justify-center">
                    <div
                        class="relative w-32 h-32 flex items-center justify-center bg-indigo-600/10 rounded-3xl p-6 border border-indigo-500/20 animate-bounce-slow">
                        <svg class="w-full h-full text-indigo-400 drop-shadow-[0_0_10px_rgba(99,102,241,0.5)]"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21V10m0 0l-8 4m8-4l8 4m-8-12a2 2 0 100 4 2 2 0 000-4zM5 20h14a1 1 0 001-1v-5a1 1 0 00-1-1H5a1 1 0 00-1 1v5a1 1 0 001 1z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13v3m6-3v3" />
                            <circle cx="12" cy="7" r="1.5" stroke="currentColor" fill="currentColor"
                                class="animate-pulse" />
                        </svg>
                        <!-- Floating Cross Icons -->
                        <div class="absolute -top-2 -right-2 w-4 h-4 text-emerald-400 animate-ping">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 3h-2v8H3v2h8v8h2v-8h8v-2h-8V3z" />
                            </svg>
                        </div>
                        <div class="absolute -bottom-3 -left-2 w-5 h-5 text-indigo-400 opacity-50 animate-pulse delay-700">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 3h-2v8H3v2h8v8h2v-8h8v-2h-8V3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-4xl sm:text-5xl font-black text-white tracking-tighter">
                        @php
                            $heading = $settings->welcome_modal_heading ?? 'Welcome Home, {title} {name}!';
                            $userTitle = $user->title ? $user->title . ' ' : '';

                            // Replace {title} with title + space, or empty if no title
                            $heading = str_replace('{title} ', $userTitle, $heading);
                            // Fallback for {title} without trailing space in template
                            $heading = str_replace('{title}', trim($userTitle), $heading);

                            $formattedHeading = str_replace('{name}', '<span class="bg-gradient-to-r from-indigo-400 to-emerald-400 bg-clip-text text-transparent">' . $user->name . '</span>', $heading);
                        @endphp
                        {!! $formattedHeading !!}
                    </h2>
                    <div class="max-w-md mx-auto">
                        <p class="text-lg text-slate-400 leading-relaxed font-medium">
                            @php
                                $msg = $settings->welcome_modal_message ?? 'We are thrilled to have you lead your congregation in the Centurion Campaign. Together, we will reach our goal of 100 souls per member!';

                                // Same logic for message
                                $msg = str_replace('{title} ', $userTitle, $msg);
                                $msg = str_replace('{title}', trim($userTitle), $msg);

                                $msg = str_replace('{name}', "<strong>{$user->name}</strong>", $msg);
                            @endphp
                            {!! $msg !!}
                        </p>
                    </div>
                </div>

                <div class="pt-4">
                    <button @click="show = false"
                        class="group relative px-12 py-5 bg-indigo-600 hover:bg-indigo-500 rounded-2xl font-black text-xs text-white uppercase tracking-[4px] transition-all transform active:scale-95 shadow-xl shadow-indigo-600/30">
                        <span class="relative z-10">Get Started</span>
                        <div
                            class="absolute inset-0 rounded-2xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </button>
                </div>

                <p class="text-[10px] text-slate-600 font-bold uppercase tracking-[2px]">Tap background or press ESC to
                    dismiss</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 4s ease-in-out infinite;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endif