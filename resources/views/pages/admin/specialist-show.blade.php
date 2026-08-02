<?php
// Livewire 4 SFC — Admin\SpecialistReviewPanel
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;

new
#[Layout('layouts::admin')]
class extends Component {
    public function render()
    {
        return $this->view()->title($this->specialist->name);
    }

    public User $specialist;
    public string $search = '';
    // Challenge coverage checkboxes
    public array $selectedChallenges = [];
    public array $challenges = [];

    public function mount(User $specialist): void
    {
        abort_unless($specialist->role === 'specialist', 404);
        $this->specialist = $specialist->load(['specialist.challenges']);
        $this->challenges = \App\Models\Challenge::where('is_active', true)->orderBy('sort_order')->get()->toArray();
        $this->selectedChallenges = $specialist->specialist?->challenges->pluck('id')->toArray() ?? [];
    }

    public function saveChallenges(): void
    {
        if ($this->specialist->specialist) {
            $this->specialist->specialist->challenges()->sync($this->selectedChallenges);
            session()->flash('success', 'Challenge coverage updated.');
        }
    }

    public function activate(): void
    {
        $this->specialist->update(['status' => 'active']);
        $this->specialist->refresh();
        session()->flash('success', 'Specialist activated.');
    }

    public function deactivate(): void
    {
        $this->specialist->update(['status' => 'inactive']);
        $this->specialist->refresh();
        session()->flash('success', 'Specialist deactivated.');
    }

    public function reject(): void
    {
        $this->specialist->update(['status' => 'rejected']);
        $this->specialist->refresh();
        session()->flash('success', 'Specialist rejected.');
    }
}; ?>

<x-slot:heading>Specialist Review</x-slot:heading>

<div>

    <div class="mb-4">
        <a href="{{ route('admin.specialists') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            Back to Specialists
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile card -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm text-center">
                <div class="w-16 h-16 rounded-full bg-[#dbeafe] flex items-center justify-center text-blue-600 text-2xl font-semibold mx-auto mb-3">
                    {{ substr($specialist->name, 0, 1) }}
                </div>
                <h2 class="text-lg font-semibold text-[#1b1c1a]">{{ $specialist->name }}</h2>
                <p class="text-sm text-[#727973] mb-1">{{ $specialist->email }}</p>
                @if ($specialist->specialist?->credentials)
                    <p class="text-xs text-[#416352] font-medium">{{ $specialist->specialist->credentials }}</p>
                @endif
                @php
                    $statusColor = match($specialist->status) {
                        'active'               => 'bg-[#c6ebd5] text-[#2e4a3d]',
                        'verified'             => 'bg-blue-100 text-blue-700',
                        'pending_verification' => 'bg-amber-100 text-amber-700',
                        'inactive'             => 'bg-[#f0f1f0] text-[#727973]',
                        'rejected'             => 'bg-red-100 text-red-700',
                        default                => 'bg-[#f0f1f0] text-[#727973]',
                    };
                @endphp
                <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                    {{ str_replace('_', ' ', ucfirst($specialist->status)) }}
                </span>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm space-y-2">
                <h3 class="text-xs font-semibold text-[#727973] uppercase tracking-wide mb-3">Status Actions</h3>
                @if (in_array($specialist->status, ['pending_verification', 'verified', 'inactive']))
                    <button wire:click="activate" wire:confirm="Activate this specialist?"
                        class="w-full bg-[#416352] text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors">
                        ✓ Activate
                    </button>
                @endif
                @if ($specialist->status === 'active')
                    <button wire:click="deactivate" wire:confirm="Deactivate this specialist?"
                        class="w-full bg-orange-50 text-orange-700 border border-orange-200 text-sm font-semibold py-2.5 rounded-lg hover:bg-orange-100 transition-colors">
                        Deactivate
                    </button>
                @endif
                @if (!in_array($specialist->status, ['rejected']))
                    <button wire:click="reject" wire:confirm="Reject this specialist permanently?"
                        class="w-full bg-red-50 text-red-700 border border-red-200 text-sm font-semibold py-2.5 rounded-lg hover:bg-red-100 transition-colors">
                        Reject
                    </button>
                @endif
            </div>
        </div>

        <!-- Bio + Challenge coverage -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bio -->
            @if ($specialist->specialist)
                <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Professional Profile</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <p class="text-[#727973] text-xs mb-0.5">Display name</p>
                            <p class="font-medium text-[#1b1c1a]">{{ $specialist->specialist->display_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[#727973] text-xs mb-0.5">Years of experience</p>
                            <p class="font-medium text-[#1b1c1a]">{{ $specialist->specialist->years_experience ? $specialist->specialist->years_experience.' yrs' : '—' }}</p>
                        </div>
                    </div>
                    @if ($specialist->specialist->bio)
                        <div>
                            <p class="text-[#727973] text-xs mb-1">Bio</p>
                            <p class="text-sm text-[#414844] bg-[#fafbfa] border border-[#e4e7e5] rounded-lg px-4 py-3">{{ $specialist->specialist->bio }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Challenge coverage -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">Challenge Coverage</h3>
                <p class="text-xs text-[#727973] mb-4">Select which challenge categories this specialist is qualified to facilitate.</p>

                @if (empty($challenges))
                    <p class="text-sm text-[#727973]">No challenges defined yet. <a href="{{ route('admin.challenges') }}" class="text-[#416352] font-medium hover:underline">Add challenges</a></p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                        @foreach ($challenges as $challenge)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-[#e4e7e5] hover:border-[#416352] hover:bg-[#f0faf5] cursor-pointer transition-colors {{ in_array($challenge['id'], $selectedChallenges) ? 'border-[#416352] bg-[#f0faf5]' : '' }}">
                                <input type="checkbox" wire:model="selectedChallenges" value="{{ $challenge['id'] }}"
                                    class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                                <span class="text-sm font-medium text-[#1b1c1a]">{{ $challenge['name'] }}</span>
                                @if ($challenge['is_sensitive'])
                                    <span class="ml-auto text-[10px] text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-full font-semibold">Sensitive</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    <button wire:click="saveChallenges"
                        class="bg-[#416352] text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-[#2e4a3d] transition-colors flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveChallenges">Save Coverage</span>
                        <span wire:loading wire:target="saveChallenges">Saving…</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
