<?php
// Livewire 4 SFC — User\TripDetails
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Booking;

new
#[Layout('layouts::user')]
class extends Component {
    public function render()
    {
        return $this->view()->title($this->booking->trip?->title);
    }

    public Booking $booking;

    public function mount(int $trip): void
    {
        $this->booking = Booking::with(['trip.challenges', 'trip.specialists.user', 'challenges'])
            ->where('user_id', auth()->id())
            ->where('trip_id', $trip)
            ->firstOrFail();
    }
}; ?>

<div>

    <div class="mb-5">
        <a href="{{ route('user.trips') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            My Trips
        </a>
    </div>

    @if (session('booking_success'))
        <div class="mb-5 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('booking_success') }}
        </div>
    @endif

    <div class="max-w-2xl space-y-5">
        <!-- Trip header -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <div class="flex items-start justify-between gap-3 mb-3">
                <h1 class="text-xl font-semibold text-[#1b1c1a]" style="font-family:'Source Serif 4',serif;">{{ $booking->trip?->title }}</h1>
                @php
                    $statusColor = match($booking->status) {
                        'confirmed'       => 'bg-[#c6ebd5] text-[#2e4a3d]',
                        'pending_payment' => 'bg-amber-100 text-amber-700',
                        'cancelled'       => 'bg-red-100 text-red-700',
                        default           => 'bg-[#f0f1f0] text-[#727973]',
                    };
                @endphp
                <span class="inline-flex shrink-0 items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                    {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                </span>
            </div>
            <div class="space-y-2 text-sm text-[#414844]">
                @if ($booking->trip?->city)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">location_on</span>
                        {{ $booking->trip->venue ? $booking->trip->venue.', '.$booking->trip->city : $booking->trip->city }}
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">calendar_today</span>
                    {{ $booking->trip?->start_date?->format('d M Y') }} → {{ $booking->trip?->end_date?->format('d M Y') }}
                </div>
            </div>
        </div>

        <!-- Booking details -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Booking Summary</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-[#727973]">Base price</span>
                    <span class="font-medium">${{ number_format($booking->base_price_snapshot ?? 0, 2) }}</span>
                </div>
                @if ($booking->opted_out_of_food)
                    <div class="flex justify-between text-amber-600">
                        <span>Food opt-out deduction</span>
                        <span class="font-medium">
                            − @if ($booking->food_deduction_type_snapshot === 'flat')
                                ${{ number_format($booking->food_deduction_value_snapshot ?? 0, 2) }}
                            @else
                                {{ $booking->food_deduction_value_snapshot }}%
                            @endif
                        </span>
                    </div>
                @endif
                <div class="flex justify-between border-t border-[#f0f1f0] pt-3">
                    <span class="font-semibold text-[#1b1c1a]">Total charged</span>
                    <span class="font-bold text-[#1b1c1a] text-base">${{ number_format($booking->final_price ?? 0, 2) }}</span>
                </div>
                @if ($booking->stripe_verified)
                    <div class="flex items-center gap-1.5 text-xs text-[#416352] bg-[#f0faf5] border border-[#c6ebd5] rounded-lg px-3 py-2">
                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">verified</span>
                        Payment verified via Stripe
                    </div>
                @elseif ($booking->status === 'pending_payment')
                    <div class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">schedule</span>
                        Payment pending — your spot will be confirmed once payment is complete.
                    </div>
                @endif
            </div>
        </div>

        <!-- Your challenges -->
        @if ($booking->challenges->isNotEmpty())
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-3">Your Challenge Focus</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($booking->challenges as $challenge)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border {{ $challenge->pivot?->is_primary ? 'bg-[#416352] text-white border-[#416352]' : 'bg-[#f0faf5] text-[#416352] border-[#c6ebd5]' }}">
                            {{ $challenge->name }}
                            @if ($challenge->pivot?->is_primary)
                                <span class="text-white/80">— Primary</span>
                            @endif
                        </span>
                    @endforeach
                </div>
                <p class="text-xs text-[#9ba39c] mt-3">Your primary challenge determines which specialist track you'll be placed in. This information is private.</p>
            </div>
        @endif
    </div>
</div>
