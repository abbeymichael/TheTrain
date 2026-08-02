<?php
// Livewire 4 SFC — Admin\TripSpecialistAssigner
use Livewire\Volt\Component;
use App\Models\Trip;
use App\Models\TripSpecialist;
use App\Models\User;

new class extends Component {
    public Trip $trip;
    public int $specialist_id = 0;
    public int $challenge_id = 0;
    public string $role_note = '';

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load(['challenges', 'specialists.user', 'specialists.challenge']);
    }

    protected function rules(): array
    {
        return [
            'specialist_id' => ['required', 'exists:users,id'],
            'challenge_id'  => ['required', 'exists:challenges,id'],
            'role_note'     => ['nullable', 'string', 'max:100'],
        ];
    }

    public function assign(): void
    {
        $this->validate();

        // Check specialist is active
        $specialist = User::findOrFail($this->specialist_id);
        if ($specialist->status !== 'active') {
            $this->addError('specialist_id', 'Only active specialists can be assigned.');
            return;
        }

        // Upsert: if same (trip, specialist, challenge) exists, update role_note
        TripSpecialist::updateOrCreate(
            [
                'trip_id'       => $this->trip->id,
                'specialist_id' => $this->specialist_id,
                'challenge_id'  => $this->challenge_id,
            ],
            ['role_note' => $this->role_note ?: null]
        );

        $this->specialist_id = 0;
        $this->challenge_id  = 0;
        $this->role_note     = '';
        $this->trip->load(['specialists.user', 'specialists.challenge']);
        session()->flash('success', 'Specialist assigned.');
    }

    public function remove(int $id): void
    {
        TripSpecialist::where('trip_id', $this->trip->id)->where('id', $id)->delete();
        $this->trip->load(['specialists.user', 'specialists.challenge']);
        session()->flash('success', 'Assignment removed.');
    }

    public function with(): array
    {
        return [
            'activeSpecialists' => User::where('role', 'specialist')->where('status', 'active')->with('specialist')->orderBy('name')->get(),
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>Assign Specialists — {{ $trip->title }}</x-slot:title>
    <x-slot:heading>Specialist Assignment</x-slot:heading>

    <div class="mb-4">
        <a href="{{ route('admin.trip.show', $trip) }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            Back to {{ $trip->title }}
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add Assignment Form -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Assign a Specialist</h3>

            @if ($trip->challenges->isEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    This trip has no challenge tracks yet. <a href="{{ route('admin.trips.edit', $trip) }}" class="font-semibold hover:underline">Edit the trip</a> to add challenge tracks first.
                </div>
            @elseif ($activeSpecialists->isEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    No active specialists available. <a href="{{ route('admin.specialists') }}" class="font-semibold hover:underline">Activate specialists first.</a>
                </div>
            @else
                <form wire:submit="assign" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Specialist <span class="text-red-500">*</span></label>
                        <select wire:model="specialist_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                            <option value="0">Select specialist…</option>
                            @foreach ($activeSpecialists as $specialist)
                                <option value="{{ $specialist->id }}">
                                    {{ $specialist->name }} — {{ $specialist->specialist?->credentials ?? 'No credentials' }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialist_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Challenge Track <span class="text-red-500">*</span></label>
                        <select wire:model="challenge_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                            <option value="0">Select challenge track…</option>
                            @foreach ($trip->challenges as $challenge)
                                <option value="{{ $challenge->id }}">{{ $challenge->name }}</option>
                            @endforeach
                        </select>
                        @error('challenge_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">
                            Role Note <span class="text-[#9ba39c] font-normal">(optional, e.g. "Lead facilitator")</span>
                        </label>
                        <input wire:model="role_note" type="text"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="e.g. Lead facilitator, Co-facilitator" />
                    </div>
                    <button type="submit"
                        class="bg-[#416352] text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors flex items-center gap-2">
                        <span wire:loading.remove wire:target="assign">Assign Specialist</span>
                        <span wire:loading wire:target="assign">Assigning…</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- Current Assignments -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Current Assignments</h3>
            @if ($trip->specialists->isEmpty())
                <div class="py-10 text-center">
                    <span class="material-symbols-outlined text-[#c1c8c2] text-4xl block mb-2" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">psychology</span>
                    <p class="text-sm text-[#727973]">No specialists assigned yet.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($trip->specialists as $assignment)
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-[#fafbfa] border border-[#e4e7e5] hover:border-[#c1c8c2] transition-colors">
                            <div class="w-9 h-9 rounded-full bg-[#dbeafe] flex items-center justify-center text-blue-600 text-sm font-semibold shrink-0">
                                {{ substr($assignment->user?->name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#1b1c1a]">{{ $assignment->user?->name ?? '—' }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                    <span class="text-xs bg-[#f0faf5] text-[#416352] border border-[#c6ebd5] px-2 py-0.5 rounded-full font-medium">
                                        {{ $assignment->challenge?->name ?? '—' }}
                                    </span>
                                    @if ($assignment->role_note)
                                        <span class="text-xs text-[#727973]">{{ $assignment->role_note }}</span>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="remove({{ $assignment->id }})" wire:confirm="Remove this assignment?"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-[#9ba39c] hover:text-red-600 hover:bg-red-50 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">close</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
