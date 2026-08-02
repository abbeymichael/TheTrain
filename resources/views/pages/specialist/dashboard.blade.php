<?php
// Livewire 4 SFC — Specialist\Dashboard
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\TripSpecialist;

new
#[Layout('layouts::specialist')]
#[Title('Dashboard')]
class extends Component {
    public function with(): array
    {
        $user = auth()->user();
        return [
            'user'      => $user,
            'upcoming'  => TripSpecialist::with(['trip', 'challenge'])
                ->where('specialist_id', $user->id)
                ->whereHas('trip', fn ($q) => $q->whereIn('status', ['open', 'closed'])->where('start_date', '>=', now()))
                ->orderBy('created_at', 'desc')
                ->get(),
            'past'      => TripSpecialist::with(['trip', 'challenge'])
                ->where('specialist_id', $user->id)
                ->whereHas('trip', fn ($q) => $q->where('status', 'completed')->orWhere('start_date', '<', now()))
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }
}; ?>

<div>

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">
            Hello, {{ explode(' ', $user->name)[0] }}
        </h1>
        <p class="text-sm text-[#727973]">Your specialist dashboard — upcoming trip assignments.</p>
    </div>

    @if ($user->status !== 'active')
        <div class="mb-6 flex gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 text-sm text-amber-800">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">info</span>
            <p>Your specialist account status is <strong>{{ str_replace('_', ' ', $user->status) }}</strong>. You'll be assigned to trips once your account is activated by admin.</p>
        </div>
    @endif

    <!-- Upcoming -->
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-[#1b1c1a] mb-3">Upcoming Trip Assignments</h2>
        @if ($upcoming->isEmpty())
            <div class="bg-white rounded-xl border border-[#e4e7e5] py-14 text-center shadow-sm">
                <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">train</span>
                <p class="text-sm text-[#727973]">No upcoming trips assigned to you yet.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($upcoming as $assignment)
                    <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm hover:border-[#c1c8c2] transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-[#f0faf5] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">train</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-[#1b1c1a]">{{ $assignment->trip?->title }}</h3>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-[#727973] mt-1">
                                    <span>{{ $assignment->trip?->start_date?->format('d M Y') }}</span>
                                    @if ($assignment->trip?->city) <span>{{ $assignment->trip->city }}</span> @endif
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2 py-0.5 rounded-full font-medium">
                                        {{ $assignment->challenge?->name }}
                                    </span>
                                    @if ($assignment->role_note)
                                        <span class="text-xs text-[#727973]">{{ $assignment->role_note }}</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('specialist.trip.roster', $assignment->trip_id) }}"
                                class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium bg-[#416352] text-white hover:bg-[#2e4a3d] transition-colors">
                                View Roster
                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Past -->
    @if ($past->isNotEmpty())
        <div>
            <h2 class="text-sm font-semibold text-[#1b1c1a] mb-3 text-[#727973]">Past Trips</h2>
            <div class="space-y-3">
                @foreach ($past as $assignment)
                    <div class="bg-white rounded-xl border border-[#e4e7e5] p-4 shadow-sm flex items-center gap-4 opacity-70">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-[#1b1c1a] text-sm">{{ $assignment->trip?->title }}</p>
                            <p class="text-xs text-[#9ba39c]">{{ $assignment->trip?->start_date?->format('d M Y') }} · {{ $assignment->challenge?->name }}</p>
                        </div>
                        <span class="text-xs text-[#9ba39c] bg-[#f0f1f0] px-2 py-0.5 rounded-full">Completed</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
