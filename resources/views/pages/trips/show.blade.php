<?php
// Livewire 4 SFC — Public\TripShow
use Livewire\Volt\Component;
use App\Models\Trip;

new class extends Component {
    public Trip $trip;

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load(['challenges', 'specialists.user.specialist', 'specialists.challenge']);
    }
}; ?>

<x-layouts.public>
    <x-slot:title>{{ $trip->title }} — TheTrain</x-slot:title>

    <!-- Nav -->
    <nav class="fixed top-0 w-full z-50 bg-[#fbf9f6]/90 backdrop-blur-md border-b border-[#c1c8c2]/30 shadow-sm">
        <div class="flex justify-between items-center w-full px-4 md:px-8 py-4 max-w-7xl mx-auto">
            <a class="text-[28px] font-semibold text-[#416352]" style="font-family:'Source Serif 4',serif;" href="{{ route('home') }}">TheTrain</a>
            <div class="flex items-center space-x-3">
                <a href="{{ route('trips') }}" class="hidden md:block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors">Browse Trips</a>
                @guest
                    <a href="{{ route('register') }}" class="bg-[#416352] text-white px-5 py-2 rounded-full text-sm font-semibold hover:opacity-90 transition-all shadow-md">Register</a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="bg-[#416352] text-white px-5 py-2 rounded-full text-sm font-semibold hover:opacity-90 transition-all shadow-md">My Account</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Hero image -->
    <div class="pt-16">
        <div class="w-full h-64 md:h-80 bg-[#eae8e5] overflow-hidden">
            @if ($trip->cover_image)
                <img src="{{ asset('storage/'.$trip->cover_image) }}" alt="{{ $trip->title }}" class="w-full h-full object-cover" />
            @else
                <div class="w-full h-full flex items-center justify-center bg-[#c6ebd5]">
                    <span class="material-symbols-outlined text-[#416352] text-7xl" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">landscape</span>
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Back + Title -->
                <div>
                    <a href="{{ route('trips') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium mb-3 block">
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
                        All Trips
                    </a>
                    <h1 class="text-3xl font-semibold text-[#1b1c1a] mb-3" style="font-family:'Source Serif 4',serif;">{{ $trip->title }}</h1>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach ($trip->challenges as $challenge)
                            <span class="text-xs bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2.5 py-1 rounded-full font-medium">{{ $challenge->name }}</span>
                        @endforeach
                    </div>
                    @if ($trip->description)
                        <p class="text-base text-[#414844] leading-relaxed">{{ $trip->description }}</p>
                    @endif
                </div>

                <!-- What's included -->
                <div class="bg-[#f5f3f0] rounded-xl p-6">
                    <h3 class="text-base font-semibold text-[#1b1c1a] mb-4" style="font-family:'Source Serif 4',serif;">What's Included</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#c6ebd5] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[#416352] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">hotel</span>
                            </div>
                            <div>
                                <p class="font-medium text-[#1b1c1a]">Accommodation</p>
                                <p class="text-xs text-[#727973]">${{ number_format($trip->accommodation_cost, 0) }} included</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#fce7f3] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-pink-600 text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">restaurant</span>
                            </div>
                            <div>
                                <p class="font-medium text-[#1b1c1a]">Meals</p>
                                <p class="text-xs text-[#727973]">Opt out to save {{ $trip->food_deduction_type === 'percentage' ? $trip->food_deduction_value.'%' : '$'.number_format($trip->food_deduction_value, 0) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#dbeafe] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">psychology</span>
                            </div>
                            <div>
                                <p class="font-medium text-[#1b1c1a]">Specialist Support</p>
                                <p class="text-xs text-[#727973]">Expert facilitation</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specialists (public facing — name and credentials only, no sensitive info) -->
                @if ($trip->specialists->isNotEmpty())
                    <div>
                        <h3 class="text-base font-semibold text-[#1b1c1a] mb-4" style="font-family:'Source Serif 4',serif;">Your Support Team</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($trip->specialists->unique('specialist_id') as $ts)
                                <div class="flex items-center gap-4 bg-white rounded-xl border border-[#e4e7e5] p-4 shadow-sm">
                                    <div class="w-12 h-12 rounded-full bg-[#dbeafe] flex items-center justify-center text-blue-600 font-semibold shrink-0">
                                        {{ substr($ts->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-[#1b1c1a] text-sm">{{ $ts->user?->specialist?->display_name ?? $ts->user?->name ?? '—' }}</p>
                                        <p class="text-xs text-[#727973]">{{ $ts->user?->specialist?->credentials ?? '' }}</p>
                                        <span class="inline-block mt-1 text-[10px] bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2 py-0.5 rounded-full font-medium">
                                            {{ $ts->challenge?->name }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Booking card -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white rounded-2xl border border-[#e4e7e5] shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] p-6">
                    <!-- Price -->
                    <div class="mb-5">
                        <p class="text-3xl font-bold text-[#1b1c1a]">${{ number_format($trip->base_price, 0) }}</p>
                        <p class="text-xs text-[#9ba39c]">per person, all-inclusive</p>
                    </div>

                    <!-- Trip info -->
                    <div class="space-y-2 text-sm mb-5 pb-5 border-b border-[#f0f1f0]">
                        <div class="flex items-center justify-between">
                            <span class="text-[#727973] flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">calendar_today</span>
                                Dates
                            </span>
                            <span class="font-medium text-[#1b1c1a]">{{ $trip->start_date?->format('d M') }} – {{ $trip->end_date?->format('d M Y') }}</span>
                        </div>
                        @if ($trip->city)
                            <div class="flex items-center justify-between">
                                <span class="text-[#727973] flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">location_on</span>
                                    Location
                                </span>
                                <span class="font-medium text-[#1b1c1a]">{{ $trip->city }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-[#727973] flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">group</span>
                                Capacity
                            </span>
                            <span class="font-medium text-[#1b1c1a]">{{ $trip->capacity }} spots</span>
                        </div>
                    </div>

                    <!-- CTA -->
                    @if ($trip->status === 'open')
                        @auth
                            @if (auth()->user()->status === 'approved')
                                <a href="{{ route('user.trips.book', $trip) }}"
                                    class="w-full bg-[#416352] text-white text-sm font-semibold py-3 rounded-xl hover:bg-[#2e4a3d] active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm">
                                    Book This Retreat
                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
                                </a>
                            @else
                                <div class="text-center text-xs text-[#727973] bg-[#f0f1f0] rounded-lg px-4 py-3">
                                    Your account must be approved to book trips.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('register') }}"
                                class="w-full bg-[#416352] text-white text-sm font-semibold py-3 rounded-xl hover:bg-[#2e4a3d] transition-all flex items-center justify-center gap-2 shadow-sm mb-2">
                                Register to Book
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
                            </a>
                            <a href="{{ route('login') }}" class="w-full text-center text-sm text-[#416352] font-medium hover:text-[#2e4a3d] transition-colors py-2 block">
                                Already have an account? Sign in
                            </a>
                        @endauth
                    @else
                        <div class="text-center text-sm text-[#727973] bg-[#f0f1f0] rounded-lg px-4 py-3 font-medium">
                            This trip is {{ $trip->status === 'closed' ? 'closed for booking' : $trip->status }}.
                        </div>
                    @endif

                    <p class="text-[11px] text-[#9ba39c] text-center mt-4">Registration is free. Account approval is quick and lightweight.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#f5f3f0] border-t border-[#c1c8c2]/50 py-8 mt-10">
        <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <span class="text-[20px] font-medium text-[#416352]" style="font-family:'Source Serif 4',serif;">TheTrain</span>
            <p class="text-[12px] text-[#727973]">© {{ date('Y') }} TheTrain Platform. Your journey to restoration starts here.</p>
        </div>
    </footer>
</x-layouts.public>
