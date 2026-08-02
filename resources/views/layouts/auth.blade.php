<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'TheTrain' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @livewireStyles
</head>
<body class="bg-[#fbf9f6] text-[#1b1c1a] antialiased min-h-screen flex items-center justify-center" style="font-family:'Manrope',sans-serif;">
    <div class="w-full max-w-md mx-auto px-4 py-12">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-[28px] font-semibold text-[#416352]" style="font-family:'Source Serif 4',serif;">TheTrain</a>
            <p class="text-sm text-[#727973] mt-1">Your Journey to Restoration</p>
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
