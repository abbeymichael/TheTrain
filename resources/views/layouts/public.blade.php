<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'TheTrain' }} — {{ config('app.name', 'TheTrain') }}</title>
    <meta name="description" content="{{ $meta_description ?? 'Curated support retreats designed to provide emotional safety, steady guidance, and a supportive community for your personal growth.' }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400..900;1,8..60,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,0&icon_names=arrow_forward,calendar_month,check_circle,location_on,mail,menu,psychology,restaurant,send,share,spa" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="bg-background text-on-surface font-body antialiased">
    {{-- Public Navigation --}}
    <nav data-public-nav class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 transition-all duration-300">
        <div class="flex justify-between items-center w-full px-container-margin py-md max-w-7xl mx-auto">
            <a href="{{ route('home') }}" class="font-display text-headline-lg font-bold text-primary">TheTrain</a>

            <div class="hidden md:flex items-center gap-lg">
                <a href="{{ route('trips') }}" class="font-label-md text-label-md {{ request()->routeIs('trips') || request()->routeIs('trip.show') ? 'text-primary border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors' }}">
                    Browse Trips
                </a>
                <a href="{{ route('home', ['#how']) }}" class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">
                    How it Works
                </a>
            </div>

            <div class="flex items-center gap-md">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="hidden md:block font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden md:block font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-primary text-on-primary px-lg py-sm rounded-full font-label-md text-label-md hover:opacity-90 active:scale-[0.98] transition-all shadow-md">
                        Register
                    </a>
                @endauth

                <button class="md:hidden flex items-center justify-center p-2 text-on-surface-variant" aria-label="Open menu">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <div>
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <footer class="w-full mt-xl bg-surface-container-low border-t border-outline-variant/50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-xl px-container-margin py-xl max-w-7xl mx-auto">
            <div class="md:col-span-1">
                <div class="font-display text-headline-md text-primary mb-4">TheTrain</div>
                <p class="text-on-surface-variant font-body-md mb-md">Guided retreats for emotional restoration and community-led growth.</p>
                <div class="flex gap-md">
                    <a href="#" class="text-primary hover:opacity-70 transition-opacity" aria-label="Share"><span class="material-symbols-outlined">share</span></a>
                    <a href="mailto:hello@thetrain.example" class="text-primary hover:opacity-70 transition-opacity" aria-label="Email"><span class="material-symbols-outlined">mail</span></a>
                </div>
            </div>

            <div>
                <h5 class="font-label-md text-label-md font-bold mb-md">Explore</h5>
                <ul class="space-y-sm">
                    <li><a href="{{ route('trips') }}" class="text-on-surface-variant hover:text-primary transition-colors">Browse Trips</a></li>
                    <li><a href="{{ route('home', ['#how']) }}" class="text-on-surface-variant hover:text-primary transition-colors">How it Works</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-label-md text-label-md font-bold mb-md">Support</h5>
                <ul class="space-y-sm">
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Terms of Service</a></li>
                    <li><a href="mailto:hello@thetrain.example" class="text-on-surface-variant hover:text-primary transition-colors">Contact Us</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-label-md text-label-md font-bold mb-md">Newsletter</h5>
                <p class="text-[12px] text-on-surface-variant mb-md">Receive gentle reminders and retreat updates.</p>
                <form class="flex gap-xs" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Email address" class="bg-surface border border-outline-variant rounded-lg text-label-md flex-1 focus:ring-2 focus:ring-primary px-md py-sm">
                    <button type="submit" class="bg-primary text-on-primary p-2 rounded-lg" aria-label="Subscribe">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-container-margin py-lg border-t border-outline-variant/30 text-center">
            <p class="text-on-surface-variant text-[12px]">&copy; {{ date('Y') }} {{ config('app.name', 'TheTrain') }}. Your journey to restoration starts here.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
