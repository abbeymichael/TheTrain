<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Specialist Portal' }} — TheTrain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @livewireStyles
</head>
<body class="bg-[#f5f3f0] text-[#1b1c1a] antialiased min-h-screen" style="font-family:'Manrope',sans-serif;">

    <nav class="sticky top-0 z-40 bg-[#fbf9f6]/90 backdrop-blur-md border-b border-[#c1c8c2]/40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[22px] font-semibold text-[#416352]" style="font-family:'Source Serif 4',serif;">TheTrain</a>
                <span class="text-[10px] font-semibold tracking-widest text-[#416352] bg-[#c6ebd5] px-2 py-0.5 rounded-full uppercase">Specialist</span>
            </div>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('specialist.dashboard') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('specialist.dashboard') ? 'bg-[#c6ebd5] text-[#2e4a3d]' : 'text-[#414844] hover:text-[#416352] hover:bg-[#f0faf5]' }}">
                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">home</span>
                    Dashboard
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden md:block text-sm text-[#727973]">{{ auth()->user()->name ?? '' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-[#414844] hover:text-[#416352] transition-colors flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">logout</span>
                        <span class="hidden md:inline">Sign out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
