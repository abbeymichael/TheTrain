<?php
// Livewire 4 SFC — User\TripBooking
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Challenge;

new
#[Layout('layouts::user')]
class extends Component {
    public function render()
    {
        return $this->view()->title("Book — {$this->trip->title}");
    }

    public Trip $trip;
    public array $selectedChallenges = [];
    public ?int $primaryChallenge = null;
    public bool $includeFood = true;
    public float $finalPrice = 0;
    public string $error = '';

    public function mount(Trip $trip): void
    {
        // Gate: user must be approved
        $user = auth()->user();
        if ($user->status !== 'approved') {
            $this->error = 'Your account must be approved before you can book a trip.';
        }

        // Gate: trip must be open
        if ($trip->status !== 'open') {
            $this->error = 'This trip is not currently open for booking.';
        }

        // Gate: trip not already booked
        if (Booking::where('user_id', $user->id)->where('trip_id', $trip->id)->whereIn('status', ['confirmed', 'pending_payment'])->exists()) {
            $this->error = 'You have already booked this trip.';
        }

        $this->trip = $trip->load('challenges');
        $this->computePrice();
    }

    public function updatedIncludeFood(): void { $this->computePrice(); }

    private function computePrice(): void
    {
        $this->finalPrice = $this->calculateFinalPrice($this->trip, $this->includeFood);
    }

    private function calculateFinalPrice(Trip $trip, bool $includeFood): float
    {
        if ($includeFood) return (float) $trip->base_price;

        if ($trip->food_deduction_type === 'flat') {
            return max(0, (float) $trip->base_price - (float) $trip->food_deduction_value);
        }
        return max(0, (float) $trip->base_price - ((float) $trip->base_price * (float) $trip->food_deduction_value / 100));
    }

    public function book(): void
    {
        if ($this->error) return;

        $this->validate([
            'selectedChallenges' => ['required', 'array', 'min:1'],
            'primaryChallenge'   => ['required', 'integer'],
        ], [
            'selectedChallenges.required' => 'Please select at least one challenge.',
            'selectedChallenges.min'      => 'Please select at least one challenge.',
            'primaryChallenge.required'   => 'Please mark one challenge as your primary focus.',
        ]);

        if (! in_array($this->primaryChallenge, $this->selectedChallenges)) {
            $this->addError('primaryChallenge', 'Your primary challenge must be one of the selected challenges.');
            return;
        }

        $this->computePrice();

        $booking = Booking::create([
            'user_id'                       => auth()->id(),
            'trip_id'                       => $this->trip->id,
            'status'                        => 'pending_payment',
            'base_price_snapshot'           => $this->trip->base_price,
            'opted_out_of_food'             => ! $this->includeFood,
            'food_deduction_type_snapshot'  => $this->trip->food_deduction_type,
            'food_deduction_value_snapshot' => $this->trip->food_deduction_value,
            'final_price'                   => $this->finalPrice,
        ]);

        // Sync challenge selections
        $pivotData = collect($this->selectedChallenges)->mapWithKeys(fn ($id) => [
            $id => ['is_primary' => (int)$id === (int)$this->primaryChallenge]
        ])->toArray();
        $booking->challenges()->sync($pivotData);

        // TODO: Redirect to Stripe Checkout — wired in next phase
        // For now, redirect to trip details with a notice
        session()->flash('booking_success', 'Your booking is reserved! Complete payment to confirm your spot.');
        $this->redirect(route('user.trip.details', $this->trip), navigate: true);
    }
}; ?>

<div>

    <div class="max-w-2xl">
        <div class="mb-5">
            <a href="{{ route('trips') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
                Back to Trips
            </a>
        </div>

        @if ($error)
            <div class="mb-5 flex gap-3 bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-800">
                <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">error</span>
                {{ $error }}
            </div>
        @else

        <!-- Trip Summary -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm mb-5">
            <h2 class="text-lg font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">{{ $trip->title }}</h2>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#727973]">
                <span>{{ $trip->start_date?->format('d M Y') }} → {{ $trip->end_date?->format('d M Y') }}</span>
                @if ($trip->city) <span>{{ $trip->venue ? $trip->venue.', '.$trip->city : $trip->city }}</span> @endif
                <span>Capacity: {{ $trip->capacity }} participants</span>
            </div>
        </div>

        <!-- Booking Form -->
        <form wire:submit="book" class="space-y-5">

            <!-- Challenge Selection -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">What are you working through? <span class="text-red-500">*</span></h3>
                <p class="text-xs text-[#727973] mb-4">Select all that apply. Mark your <strong>primary focus</strong> — this determines which specialist track you'll be placed in.</p>
                @error('selectedChallenges') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror
                @error('primaryChallenge') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror

                @if ($trip->challenges->isEmpty())
                    <p class="text-sm text-[#727973]">No specific challenge tracks defined for this trip.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($trip->challenges as $challenge)
                            <div class="flex items-start gap-3 p-4 rounded-xl border transition-colors
                                {{ in_array($challenge->id, $selectedChallenges) ? 'border-[#416352] bg-[#f0faf5]' : 'border-[#e4e7e5] hover:border-[#c1c8c2]' }}">
                                <input type="checkbox" wire:model.live="selectedChallenges" value="{{ $challenge->id }}" id="c_{{ $challenge->id }}"
                                    class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352] mt-0.5 shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <label for="c_{{ $challenge->id }}" class="text-sm font-medium text-[#1b1c1a] cursor-pointer flex items-center gap-2">
                                        {{ $challenge->name }}
                                        @if ($challenge->is_sensitive)
                                            <span class="text-[10px] text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-full font-semibold">Sensitive</span>
                                        @endif
                                    </label>
                                    @if ($challenge->description)
                                        <p class="text-xs text-[#727973] mt-0.5">{{ $challenge->description }}</p>
                                    @endif
                                </div>
                                @if (in_array($challenge->id, $selectedChallenges))
                                    <div class="shrink-0">
                                        <label class="flex items-center gap-1.5 text-xs cursor-pointer">
                                            <input type="radio" wire:model="primaryChallenge" value="{{ $challenge->id }}"
                                                class="w-3.5 h-3.5 text-[#416352] border-[#c1c8c2] focus:ring-[#416352]" />
                                            <span class="font-medium text-[#416352]">Primary</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Food Opt-in/out -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">Feeding Included?</h3>
                <p class="text-xs text-[#727973] mb-4">All-inclusive pricing covers meals. You may opt out for a reduced rate.</p>
                <div class="space-y-2">
                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-colors {{ $includeFood ? 'border-[#416352] bg-[#f0faf5]' : 'border-[#e4e7e5] hover:border-[#c1c8c2]' }}">
                        <input type="radio" wire:model.live="includeFood" value="1"
                            class="w-4 h-4 text-[#416352] border-[#c1c8c2] mt-0.5 shrink-0" />
                        <div>
                            <p class="text-sm font-medium text-[#1b1c1a]">Yes, include meals</p>
                            <p class="text-xs text-[#727973]">Full all-inclusive: meals, accommodation, and activities.</p>
                        </div>
                        <span class="ml-auto font-semibold text-[#1b1c1a] text-sm shrink-0">${{ number_format($trip->base_price, 2) }}</span>
                    </label>
                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-colors {{ ! $includeFood ? 'border-[#416352] bg-[#f0faf5]' : 'border-[#e4e7e5] hover:border-[#c1c8c2]' }}">
                        <input type="radio" wire:model.live="includeFood" value="0"
                            class="w-4 h-4 text-[#416352] border-[#c1c8c2] mt-0.5 shrink-0" />
                        <div>
                            <p class="text-sm font-medium text-[#1b1c1a]">No, opt out of meals</p>
                            <p class="text-xs text-[#727973]">
                                Deduction: {{ $trip->food_deduction_type === 'percentage' ? $trip->food_deduction_value.'%' : '$'.number_format($trip->food_deduction_value, 2) }} off
                            </p>
                        </div>
                        @php
                            $optOutPrice = $trip->food_deduction_type === 'flat'
                                ? max(0, $trip->base_price - $trip->food_deduction_value)
                                : max(0, $trip->base_price - $trip->base_price * $trip->food_deduction_value / 100);
                        @endphp
                        <span class="ml-auto font-semibold text-[#1b1c1a] text-sm shrink-0">${{ number_format($optOutPrice, 2) }}</span>
                    </label>
                </div>
            </div>

            <!-- Price Summary + Submit -->
            <div class="bg-[#1b2e25] rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-white/80">Total due</span>
                    <span class="text-2xl font-bold">${{ number_format($finalPrice, 2) }}</span>
                </div>
                <p class="text-xs text-white/60 mb-5">Payment is processed securely via Stripe. Your spot is only confirmed once payment is completed.</p>
                <button type="submit"
                    class="w-full bg-[#416352] text-white text-sm font-semibold py-3 rounded-xl hover:bg-[#5a7c6a] active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-lg">
                    <span wire:loading.remove wire:target="book">Continue to Payment</span>
                    <span wire:loading wire:target="book">Processing…</span>
                    <span wire:loading.remove wire:target="book" class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">lock</span>
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
