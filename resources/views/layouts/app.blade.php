<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — E-Supervisi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#e8edf2] font-sans text-slate-800 antialiased">
    <header class="bg-[#1a2332] text-white shadow-md">
        <div class="mx-auto flex h-[72px] max-w-[1600px] items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#2563eb] shadow-inner">
                    <i class="fa-solid fa-shield-halved text-lg text-white"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-lg font-bold tracking-wide">E-SUPERVISI</p>
                    <p class="text-[11px] font-medium uppercase tracking-[0.2em] text-slate-300">Kinerja Profesional</p>
                </div>
            </div>
            <div id="session-timer"
                class="hidden items-center gap-2 rounded-md bg-[#fbbf24] px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm cursor-pointer transition hover:bg-[#f59e0b]"
                onclick="resetSession()" title="Klik untuk reset timer">
                <i class="fa-solid fa-clock text-xs"></i>
                <span id="timer-display">15:00</span>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:flex lg:gap-6 lg:px-8">
        @include('partials.sidebar')

        <main class="mt-6 min-w-0 flex-1 lg:mt-0">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    <script>
        @if (!session('is_locked'))
            @include('partials.session-lock-script')
        @endif
    </script>
</body>

</html>
