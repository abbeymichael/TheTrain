<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Admin' }} — TheTrain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @livewireStyles
</head>
<body class="bg-[#f2f4f3] text-[#1b1c1a] antialiased h-full" style="font-family:'Manrope',sans-serif;">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- ============================================================
         SIDEBAR
    ============================================================ -->
    <!-- Mobile overlay -->
    <div
        class="fixed inset-0 z-20 bg-black/40 backdrop-blur-sm lg:hidden"
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar panel -->
    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-[#1b2e25] transform transition-transform duration-300 ease-in-out
               lg:relative lg:translate-x-0 lg:flex lg:shrink-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Sidebar Header / Logo -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-white/10 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <span class="text-[22px] font-semibold text-white" style="font-family:'Source Serif 4',serif;">TheTrain</span>
                <span class="text-[10px] font-semibold tracking-widest text-[#7ab595] bg-white/10 px-2 py-0.5 rounded-full uppercase">Admin</span>
            </a>
            <!-- Close button (mobile) -->
            <button class="lg:hidden text-white/60 hover:text-white transition-colors" @click="sidebarOpen = false">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">close</span>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            <!-- Overview -->
            <div class="px-3 pt-2 pb-1">
                <p class="text-[10px] font-semibold tracking-widest text-white/30 uppercase">Overview</p>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.dashboard') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">dashboard</span>
                Dashboard
            </a>

            <a href="{{ route('admin.analytics') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.analytics') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.analytics') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">bar_chart</span>
                Analytics
            </a>

            <!-- People -->
            <div class="px-3 pt-4 pb-1">
                <p class="text-[10px] font-semibold tracking-widest text-white/30 uppercase">People</p>
            </div>

            <a href="{{ route('admin.users') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.users', 'admin.user.show') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.users', 'admin.user.show') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">group</span>
                Users
            </a>

            <a href="{{ route('admin.specialists') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.specialists', 'admin.specialist.show') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.specialists', 'admin.specialist.show') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">psychology</span>
                Specialists
            </a>

            <!-- Programmes -->
            <div class="px-3 pt-4 pb-1">
                <p class="text-[10px] font-semibold tracking-widest text-white/30 uppercase">Programmes</p>
            </div>

            <a href="{{ route('admin.challenges') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.challenges') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.challenges') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">favorite</span>
                Challenges
            </a>

            <a href="{{ route('admin.trip-series') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.trip-series') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.trip-series') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">repeat</span>
                Trip Series
            </a>

            <a href="{{ route('admin.trips') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.trips', 'admin.trip.show', 'admin.trips.create', 'admin.trips.edit', 'admin.trip.specialists', 'admin.trip.refunds') ? 'bg-[#416352] text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' {{ request()->routeIs('admin.trips', 'admin.trip.show', 'admin.trips.create', 'admin.trips.edit', 'admin.trip.specialists', 'admin.trip.refunds') ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">train</span>
                Trips
            </a>

        </nav>

        <!-- Sidebar Footer — user info + logout -->
        <div class="shrink-0 border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#416352] flex items-center justify-center text-white text-sm font-semibold shrink-0">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-white/50 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out" class="text-white/40 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ============================================================
         MAIN CONTENT AREA
    ============================================================ -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        <!-- Top Bar -->
        <header class="shrink-0 h-16 bg-white border-b border-[#e4e7e5] flex items-center gap-4 px-4 md:px-6">
            <!-- Mobile hamburger -->
            <button
                class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-[#414844] hover:bg-[#f2f4f3] transition-colors"
                @click="sidebarOpen = true"
            >
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">menu</span>
            </button>

            <!-- Page title (slot) -->
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-[#1b1c1a] truncate">{{ $heading ?? '' }}</h1>
            </div>

            <!-- Top-bar actions -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Quick link to public site -->
                <a href="{{ route('home') }}" target="_blank"
                   class="hidden md:flex items-center gap-1.5 text-xs font-medium text-[#416352] hover:text-[#2e4a3d] transition-colors px-3 py-1.5 rounded-lg border border-[#c6ebd5] hover:border-[#416352] bg-[#f0faf5]">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">open_in_new</span>
                    View Site
                </a>
                <!-- Notification bell placeholder -->
                <button class="w-9 h-9 flex items-center justify-center rounded-lg text-[#414844] hover:bg-[#f2f4f3] transition-colors">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">notifications</span>
                </button>
            </div>
        </header>

        <!-- Scrollable page content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

</div>

@livewireScripts
<script>
// Minimal Alpine-like x-data shim (only needed if Livewire's bundled Alpine isn't available)
// Livewire 4 ships Alpine — nothing to add here.
</script>
</body>
</html>
