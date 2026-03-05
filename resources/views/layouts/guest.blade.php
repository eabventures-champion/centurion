<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Centurion Campaign') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,600,700,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: linear-gradient(180deg, rgba(79, 70, 229, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%);
            filter: blur(100px);
            border-radius: 50%;
            z-index: 0;
            animation: move 30s infinite alternate;
            pointer-events: none;
        }

        .blob-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
            animation-duration: 35s;
            animation-delay: -7s;
        }

        @keyframes move {
            from {
                transform: translate(-20%, -20%) rotate(0deg);
            }

            to {
                transform: translate(30%, 30%) rotate(360deg);
            }
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 2rem;
            padding: 1px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.02) 40%, rgba(255, 255, 255, 0));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
    </style>
</head>

<body class="font-['Outfit'] text-slate-200 antialiased bg-slate-950 min-h-screen overflow-x-hidden"
    x-data="{ mouseX: 0, mouseY: 0 }" @mousemove="mouseX = $event.clientX; mouseY = $event.clientY">

    <div class="relative min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Interactive Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="blob left-[-15%] top-[-10%]"
                :style="`transform: translate(${mouseX * 0.02}px, ${mouseY * 0.02}px)`"></div>
            <div class="blob blob-2 right-[-10%] bottom-[-10%]"
                :style="`transform: translate(${mouseX * -0.01}px, ${mouseY * -0.01}px)`"></div>
        </div>

        <div class="relative z-10 w-full max-w-4xl">
            <div class="flex flex-col items-center mb-8">
                <a href="/" class="group transition-transform active:scale-95">
                    <div
                        class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px] flex items-center justify-center transition-all -mb-16 md:-mb-24">
                        <x-application-logo class="w-full h-full" />
                    </div>
                </a>
            </div>

            <div class="w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>