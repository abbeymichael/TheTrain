<?php
// Livewire 4 SFC — Public\TripsList
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Trip;
use App\Models\Challenge;

new
#[Layout('layouts::public')]
#[Title('Browse Trips — TheTrain')]
class extends Component {
    public string $search = '';
    public ?int $challengeFilter = null;

    public function with(): array
    {
        return [
            'trips' => Trip::with('challenges')
                ->where('status', 'open')
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                      ->orWhere('city', 'like', '%'.$this->search.'%')
                      ->orWhere('description', 'like', '%'.$this->search.'%');
                }))
                ->when($this->challengeFilter, fn ($q) => $q->whereHas('challenges', fn ($q) => $q->where('challenges.id', $this->challengeFilter)))
                ->orderBy('start_date')
                ->paginate(12),
            'challenges' => Challenge::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }
}; ?>

<div>

    <!-- Nav -->
    <nav class="fixed top-0 w-full z-50 bg-[#fbf9f6]/90 backdrop-blur-md border-b border-[#c1c8c2]/30 shadow-sm">
        <div class="flex justify-between items-center w-full px-4 md:px-8 py-4 max-w-7xl mx-auto">
            <a class="text-[28px] font-semibold text-[#416352]" style="font-family:'Source Serif 4',serif;" href="{{ route('home') }}">TheTrain</a>
            <div class="hidden md:flex items-center space-x-6">
                <a class="text-sm font-semibold text-[#416352] border-b-2 border-[#416352] pb-1" href="{{ route('trips') }}">Browse Trips</a>
                <a class="text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors" href="{{ route('home') }}#how">How it Works</a>
            </div>
            <div class="flex items-center space-x-3">
                @guest
                    <a href="{{ route('login') }}" class="hidden md:block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="bg-[#416352] text-white px-5 py-2 rounded-full text-sm font-semibold hover:opacity-90 transition-all shadow-md">Register</a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="bg-[#416352] text-white px-5 py-2 rounded-full text-sm font-semibold hover:opacity-90 transition-all shadow-md">My Account</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Page header -->
    <section class="pt-28 pb-10 bg-[#f5f3f0]">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <h1 class="text-3xl md:text-4xl font-semibold text-[#1b1c1a] mb-2" style="font-family:'Source Serif 4',serif;">Upcoming Retreats</h1>
            <p class="text-base text-[#414844]">Find a restorative retreat that fits your journey.</p>

            <!-- Filters -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9ba39c] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">search</span>
                    <input wire:model.live.debounce.300ms="search" type="search"
                        class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="Search by location or keyword…" />
                </div>
                <select wire:model.live="challengeFilter"
                    class="px-3 py-2.5 rounded-lg border border-[#c1c8c2] bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                    <option value="">All challenge tracks</option>
                    @foreach ($challenges as $challenge)
                        <option value="{{ $challenge->id }}">{{ $challenge->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- Trips grid -->
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            @if ($trips->isEmpty())
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-[#c1c8c2] text-6xl block mb-4" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">train</span>
                    <p class="text-lg text-[#727973] mb-2">No trips found.</p>
                    <p class="text-sm text-[#9ba39c]">Check back soon or clear your filters.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($trips as $trip)
                        <a href="{{ route('trip.show', $trip) }}" class="group bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden hover:-translate-y-1 hover:shadow-[0_10px_30px_-10px_rgba(84,106,123,0.15)] transition-all duration-300">
                            <!-- Cover image -->
                            <div class="h-48 bg-[#eae8e5] overflow-hidden">
                                @if ($trip->cover_image)
                                    <img src="{{ asset('storage/'.$trip->cover_image) }}" alt="{{ $trip->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-[#c6ebd5]">
                                        <span class="material-symbols-outlined text-[#416352] text-5xl" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">landscape</span>
                                    </div>
                                @endif
                            </div>
                            <!-- Card body -->
                            <div class="p-5">
                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @foreach ($trip->challenges->take(3) as $challenge)
                                        <span class="text-[11px] bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2 py-0.5 rounded-full font-medium">{{ $challenge->name }}</span>
                                    @endforeach
                                    @if ($trip->challenges->count() > 3)
                                        <span class="text-[11px] text-[#9ba39c] px-2 py-0.5">+{{ $trip->challenges->count() - 3 }} more</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-[#1b1c1a] mb-2 group-hover:text-[#416352] transition-colors" style="font-family:'Source Serif 4',serif;">{{ $trip->title }}</h3>
                                @if ($trip->description)
                                    <p class="text-xs text-[#727973] mb-3 line-clamp-2">{{ $trip->description }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#f0f1f0]">
                                    <div class="text-xs text-[#727973] space-y-0.5">
                                        <p class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">calendar_today</span>
                                            {{ $trip->start_date?->format('d M Y') }}
                                        </p>
                                        @if ($trip->city)
                                            <p class="flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">location_on</span>
                                                {{ $trip->city }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-base font-bold text-[#1b1c1a]">${{ number_format($trip->base_price, 0) }}</p>
                                        <p class="text-[11px] text-[#9ba39c]">all-inclusive</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($trips->hasPages())
                    <div class="mt-10">{{ $trips->links() }}</div>
                @endif
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#f5f3f0] border-t border-[#c1c8c2]/50 py-8 mt-10">
        <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <span class="text-[20px] font-medium text-[#416352]" style="font-family:'Source Serif 4',serif;">TheTrain</span>
            <p class="text-[12px] text-[#727973]">© {{ date('Y') }} TheTrain Platform. Your journey to restoration starts here.</p>
        </div>
    </footer>
</div>
