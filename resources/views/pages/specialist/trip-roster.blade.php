<?php
// Livewire 4 SFC — Specialist\TripRoster
// Security: specialist can only view participants in their assigned challenge track(s) for this trip
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Trip;
use App\Models\TripSpecialist;
use App\Models\Booking;

new
#[Layout('layouts::specialist')]
class extends Component {
    public function render()
    {
        return $this->view()->title("Roster — {$this->trip->title}");
    }

    public Trip $trip;
    public ?int $challengeId = null;
    public $assignments;
    public $roster;

    public function mount(Trip $trip): void
    {
        $user = auth()->user();

        // Get all assignments for this specialist on this trip
        $this->assignments = TripSpecialist::with('challenge')
            ->where('trip_id', $trip->id)
            ->where('specialist_id', $user->id)
            ->get();

        // Security gate: ensure specialist IS assigned to this trip
        abort_if($this->assignments->isEmpty(), 403, 'You are not assigned to this trip.');

        $this->trip = $trip;
        $this->challengeId = $this->assignments->first()?->challenge_id;
        $this->loadRoster();
    }

    public function switchChallenge(int $id): void
    {
        // Only allow switching to a challenge this specialist is assigned to
        abort_unless($this->assignments->contains('challenge_id', $id), 403);
        $this->challengeId = $id;
        $this->loadRoster();
    }

    private function loadRoster(): void
    {
        // Roster scoped ONLY to this specialist's assigned challenge track
        $this->roster = Booking::with(['user.profile', 'challenges'])
            ->where('trip_id', $this->trip->id)
            ->where('status', 'confirmed')
            ->whereHas('challenges', fn ($q) => $q->where('challenges.id', $this->challengeId)->where('booking_challenges.is_primary', true))
            ->get();
    }
}; ?>

<div>

    <div class="mb-5">
        <a href="{{ route('specialist.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            Dashboard
        </a>
    </div>

    <!-- Trip header -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm mb-5">
        <h1 class="text-lg font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">{{ $trip->title }}</h1>
        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#727973]">
            <span>{{ $trip->start_date?->format('d M Y') }} → {{ $trip->end_date?->format('d M Y') }}</span>
            @if ($trip->city) <span>{{ $trip->city }}</span> @endif
        </div>
    </div>

    <!-- Challenge track switcher (if assigned to multiple tracks) -->
    @if ($assignments->count() > 1)
        <div class="flex flex-wrap gap-2 mb-5">
            @foreach ($assignments as $a)
                <button wire:click="switchChallenge({{ $a->challenge_id }})"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $challengeId === $a->challenge_id ? 'bg-[#416352] text-white' : 'bg-white border border-[#e4e7e5] text-[#414844] hover:border-[#416352]' }}">
                    {{ $a->challenge?->name }}
                </button>
            @endforeach
        </div>
    @else
        <!-- Single track label -->
        <div class="mb-4 flex items-center gap-2">
            <span class="text-xs bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2.5 py-1 rounded-full font-semibold">
                {{ $assignments->first()?->challenge?->name }} Track
            </span>
            <span class="text-xs text-[#9ba39c]">{{ $roster->count() }} participant(s)</span>
        </div>
    @endif

    <!-- Privacy note -->
    <div class="mb-5 flex gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 text-xs text-amber-800">
        <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">lock</span>
        <p>This roster is strictly confidential. You can only see participants who have selected your assigned challenge track as their primary focus. Do not share this information outside the therapeutic context of this trip.</p>
    </div>

    <!-- Roster -->
    @if ($roster->isEmpty())
        <div class="bg-white rounded-xl border border-[#e4e7e5] py-14 text-center shadow-sm">
            <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">group</span>
            <p class="text-sm text-[#727973]">No confirmed participants in your assigned challenge track yet.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($roster as $booking)
                <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-[#c6ebd5] flex items-center justify-center text-[#416352] font-semibold text-sm shrink-0">
                            {{ substr($booking->user?->name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-[#1b1c1a]">{{ $booking->user?->name ?? '—' }}</p>
                            <!-- Challenges (all selected, not just primary) -->
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                @foreach ($booking->challenges as $ch)
                                    <span class="text-[11px] px-2 py-0.5 rounded-full font-medium border {{ $ch->pivot?->is_primary ? 'bg-[#416352] text-white border-[#416352]' : 'bg-[#f0faf5] text-[#416352] border-[#c6ebd5]' }}">
                                        {{ $ch->name }} {{ $ch->pivot?->is_primary ? '★' : '' }}
                                    </span>
                                @endforeach
                            </div>
                            <!-- Care context (profile bio) — for specialist context -->
                            @if ($booking->user?->profile?->bio)
                                <div class="mt-3 bg-[#fafbfa] border border-[#f0f1f0] rounded-lg px-3 py-2.5 text-xs text-[#414844]">
                                    <span class="block text-[#9ba39c] font-medium mb-0.5 uppercase tracking-wide text-[10px]">Personal note</span>
                                    {{ $booking->user->profile->bio }}
                                </div>
                            @endif
                            <!-- Dietary -->
                            @if ($booking->user?->profile?->dietary_restrictions)
                                <p class="text-xs text-[#727973] mt-2">
                                    Dietary: {{ implode(', ', (array) $booking->user->profile->dietary_restrictions) }}
                                </p>
                            @endif
                            @if ($booking->opted_out_of_food)
                                <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">info</span>
                                    Opted out of meals
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
