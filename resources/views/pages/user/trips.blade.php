<?php
// Livewire 4 SFC — User\MyTrips
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Booking;

new
#[Layout('layouts::user')]
#[Title('My Trips')]
class extends Component {
    public string $statusFilter = '';

    public function with(): array
    {
        return [
            'bookings' => Booking::with(['trip', 'challenges'])
                ->where('user_id', auth()->id())
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ];
    }
}; ?>

<div>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1c1a]" style="font-family:'Source Serif 4',serif;">My Trips</h1>
            <p class="text-sm text-[#727973]">All your retreat bookings.</p>
        </div>
        <a href="{{ route('trips') }}" class="flex items-center gap-2 bg-[#416352] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors shadow-sm shrink-0">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">explore</span>
            Browse Trips
        </a>
    </div>

    <!-- Filter -->
    <div class="mb-4">
        <select wire:model.live="statusFilter"
            class="px-3 py-2.5 rounded-lg border border-[#c1c8c2] bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
            <option value="">All bookings</option>
            <option value="confirmed">Confirmed</option>
            <option value="pending_payment">Pending Payment</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    @if ($bookings->isEmpty())
        <div class="bg-white rounded-xl border border-[#e4e7e5] py-16 text-center shadow-sm">
            <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">train</span>
            <p class="text-sm text-[#727973] mb-3">No trips booked yet.</p>
            <a href="{{ route('trips') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#416352] hover:text-[#2e4a3d] transition-colors">
                Browse upcoming trips
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm hover:border-[#c1c8c2] transition-colors">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Trip info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="font-semibold text-[#1b1c1a]">{{ $booking->trip?->title ?? '—' }}</h3>
                                @php
                                    $statusColor = match($booking->status) {
                                        'confirmed'       => 'bg-[#c6ebd5] text-[#2e4a3d]',
                                        'pending_payment' => 'bg-amber-100 text-amber-700',
                                        'cancelled'       => 'bg-red-100 text-red-700',
                                        default           => 'bg-[#f0f1f0] text-[#727973]',
                                    };
                                @endphp
                                <span class="inline-flex shrink-0 items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#727973] mb-3">
                                @if ($booking->trip?->city)
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">location_on</span>
                                        {{ $booking->trip->city }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">calendar_today</span>
                                    {{ $booking->trip?->start_date?->format('d M Y') }}
                                </span>
                                @if ($booking->opted_out_of_food)
                                    <span class="flex items-center gap-1 text-amber-600">
                                        <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">no_meals</span>
                                        Food opted out
                                    </span>
                                @endif
                            </div>
                            @if ($booking->challenges->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($booking->challenges as $challenge)
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2 py-0.5 rounded-full font-medium">
                                            {{ $challenge->name }}
                                            @if ($challenge->pivot?->is_primary)
                                                <span class="text-[#2e4a3d]">★</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <!-- Price + action -->
                        <div class="shrink-0 flex flex-row sm:flex-col items-center sm:items-end gap-3 sm:gap-2">
                            <p class="text-lg font-bold text-[#1b1c1a]">${{ number_format($booking->final_price ?? 0, 2) }}</p>
                            <a href="{{ route('user.trip.details', $booking->trip_id) }}"
                                class="text-xs font-medium text-[#416352] border border-[#c6ebd5] bg-[#f0faf5] px-3 py-1.5 rounded-lg hover:bg-[#c6ebd5] transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($bookings->hasPages())
            <div class="mt-6">{{ $bookings->links() }}</div>
        @endif
    @endif
</div>
