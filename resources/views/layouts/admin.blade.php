<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} — {{ config('app.name', 'TheTrain') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400..900;1,8..60,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,0&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-background text-on-surface font-body antialiased">
    <div class="min-h-screen flex flex-col md:flex-row">
        {{-- Sidebar --}}
        <aside class="w-full md:w-64 bg-surface-container-low border-r border-outline-variant/50 flex-shrink-0">
            <div class="p-lg border-b border-outline-variant/50">
                <a href="{{ route('admin.dashboard') }}" class="font-display text-headline-lg font-bold text-primary">TheTrain</a>
                <p class="text-xs text-on-surface-variant mt-xs">Admin Panel</p>
            </div>

            <nav class="p-md space-y-xs">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="font-label-md text-label-md">Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">group</span>
                    <span class="font-label-md text-label-md">Users</span>
                </a>
                <a href="{{ route('admin.specialists') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.specialists*') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span class="font-label-md text-label-md">Specialists</span>
                </a>
                <a href="{{ route('admin.challenges') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.challenges') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">challenge</span>
                    <span class="font-label-md text-label-md">Challenges</span>
                </a>
                <a href="{{ route('admin.trip-series') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.trip-series') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">train</span>
                    <span class="font-label-md text-label-md">Trip Series</span>
                </a>
                <a href="{{ route('admin.trips') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.trips*') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                    <span class="font-label-md text-label-md">Trips</span>
                </a>
                <a href="{{ route('admin.analytics') }}" class="flex items-center gap-md px-md py-sm rounded-lg {{ request()->routeIs('admin.analytics') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high' }} transition-colors">
                    <span class="material-symbols-outlined">analytics</span>
                    <span class="font-label-md text-label-md">Analytics</span>
                </a>
            </nav>

            <div class="mt-auto p-md border-t border-outline-variant/50">
                <div class="flex items-center gap-md px-md py-sm">
                    <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="/logout" class="mt-sm">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="font-label-md text-label-md">Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-surface border-b border-outline-variant/50 px-container-margin py-md flex items-center justify-between">
                <h1 class="font-display text-headline-lg text-on-surface">{{ $page_title ?? 'Admin' }}</h1>
                <a href="{{ route('home') }}" class="text-on-surface-variant hover:text-primary transition-colors font-label-md text-label-md">Back to site</a>
            </header>

            <main class="p-container-margin flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
