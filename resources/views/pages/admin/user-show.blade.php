<?php
// Livewire 4 SFC — Admin\UserReviewPanel
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Booking;

new
#[Layout('layouts::admin')]
class extends Component {
    public function render()
    {
        return $this->view()->title($this->user->name);
    }

    public User $user;
    public string $note = '';

    public function mount(User $user): void
    {
        $this->user = $user->load(['profile', 'bookings.trip']);
    }

    public function approve(): void
    {
        $this->user->update(['status' => 'approved']);
        $this->user->refresh();
        session()->flash('success', 'User approved.');
    }

    public function reject(): void
    {
        $this->user->update(['status' => 'rejected']);
        $this->user->refresh();
        session()->flash('success', 'User rejected.');
    }

    public function suspend(): void
    {
        $this->user->update(['status' => 'suspended']);
        $this->user->refresh();
        session()->flash('success', 'User suspended.');
    }

    public function reinstate(): void
    {
        $this->user->update(['status' => 'approved']);
        $this->user->refresh();
        session()->flash('success', 'User reinstated.');
    }
}; ?>

<x-slot:heading>User Review</x-slot:heading>

<div>

    <div class="mb-4">
        <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            Back to Users
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Profile card -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <div class="flex flex-col items-center text-center mb-5">
                    <div class="w-16 h-16 rounded-full bg-[#c6ebd5] flex items-center justify-center text-[#416352] text-2xl font-semibold mb-3">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h2 class="text-lg font-semibold text-[#1b1c1a]">{{ $user->name }}</h2>
                    <p class="text-sm text-[#727973]">{{ $user->email }}</p>
                    @php
                        $statusColor = match($user->status) {
                            'approved'  => 'bg-[#c6ebd5] text-[#2e4a3d]',
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            'suspended' => 'bg-orange-100 text-orange-700',
                            default     => 'bg-[#f0f1f0] text-[#727973]',
                        };
                    @endphp
                    <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>

                <div class="space-y-2 text-sm border-t border-[#f0f1f0] pt-4">
                    <div class="flex justify-between">
                        <span class="text-[#727973]">Phone</span>
                        <span class="text-[#1b1c1a] font-medium">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#727973]">Email verified</span>
                        <span class="{{ $user->email_verified_at ? 'text-[#416352]' : 'text-amber-600' }} font-medium">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#727973]">Joined</span>
                        <span class="text-[#1b1c1a] font-medium">{{ $user->created_at?->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#727973]">Last active</span>
                        <span class="text-[#1b1c1a] font-medium">{{ $user->last_active_at?->diffForHumans() ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm space-y-2">
                <h3 class="text-xs font-semibold text-[#727973] uppercase tracking-wide mb-3">Status Actions</h3>
                @if ($user->status === 'pending')
                    <button wire:click="approve" wire:confirm="Approve this user?"
                        class="w-full bg-[#416352] text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors">
                        ✓ Approve User
                    </button>
                    <button wire:click="reject" wire:confirm="Reject this user?"
                        class="w-full bg-red-50 text-red-700 border border-red-200 text-sm font-semibold py-2.5 rounded-lg hover:bg-red-100 transition-colors">
                        ✕ Reject User
                    </button>
                @elseif ($user->status === 'approved')
                    <button wire:click="suspend" wire:confirm="Suspend this user?"
                        class="w-full bg-orange-50 text-orange-700 border border-orange-200 text-sm font-semibold py-2.5 rounded-lg hover:bg-orange-100 transition-colors">
                        Suspend User
                    </button>
                @elseif ($user->status === 'suspended')
                    <button wire:click="reinstate" wire:confirm="Reinstate this user?"
                        class="w-full bg-[#c6ebd5] text-[#2e4a3d] text-sm font-semibold py-2.5 rounded-lg hover:bg-[#a8d9bc] transition-colors">
                        Reinstate User
                    </button>
                @else
                    <p class="text-sm text-[#727973] text-center py-2">No actions available for this status.</p>
                @endif
            </div>
        </div>

        <!-- Right: Profile details + Booking history -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Care-context profile (admin-only) -->
            @if ($user->profile)
                <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#416352] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">person</span>
                        Care Context Profile
                        <span class="text-[10px] font-semibold tracking-widest text-[#727973] bg-[#f0f1f0] px-2 py-0.5 rounded-full uppercase ml-auto">Admin only</span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-[#727973] text-xs mb-0.5">Full name</p>
                            <p class="font-medium text-[#1b1c1a]">{{ $user->profile->first_name }} {{ $user->profile->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-[#727973] text-xs mb-0.5">Date of birth</p>
                            <p class="font-medium text-[#1b1c1a]">{{ $user->profile->date_of_birth?->format('d M Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[#727973] text-xs mb-0.5">Emergency contact</p>
                            <p class="font-medium text-[#1b1c1a]">{{ $user->profile->emergency_contact_name ?? '—' }}</p>
                            <p class="text-[#727973] text-xs">{{ $user->profile->emergency_contact_phone ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-[#727973] text-xs mb-0.5">Dietary restrictions</p>
                            <p class="font-medium text-[#1b1c1a]">{{ $user->profile->dietary_restrictions ? implode(', ', (array) $user->profile->dietary_restrictions) : '—' }}</p>
                        </div>
                        @if ($user->profile->bio)
                            <div class="col-span-2">
                                <p class="text-[#727973] text-xs mb-0.5">Personal note (private)</p>
                                <p class="text-[#1b1c1a] bg-[#fafbfa] border border-[#e4e7e5] rounded-lg px-3 py-2">{{ $user->profile->bio }}</p>
                            </div>
                        @endif
                        @if ($user->profile->mobility_or_accessibility_needs)
                            <div class="col-span-2">
                                <p class="text-[#727973] text-xs mb-0.5">Accessibility needs</p>
                                <p class="text-[#1b1c1a]">{{ $user->profile->mobility_or_accessibility_needs }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                    <p class="text-sm text-[#727973] text-center py-4">This user hasn't completed their care-context profile yet.</p>
                </div>
            @endif

            <!-- Booking History -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e4e7e5]">
                    <h3 class="text-sm font-semibold text-[#1b1c1a]">Booking History</h3>
                </div>
                @if ($user->bookings->isEmpty())
                    <div class="py-10 text-center">
                        <p class="text-sm text-[#727973]">No bookings yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-[#f0f1f0]">
                        @foreach ($user->bookings as $booking)
                            <div class="flex items-center justify-between px-6 py-3.5">
                                <div>
                                    <p class="text-sm font-medium text-[#1b1c1a]">{{ $booking->trip?->title ?? '—' }}</p>
                                    <p class="text-xs text-[#727973]">{{ $booking->trip?->start_date?->format('d M Y') ?? '' }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $c = match($booking->status) {
                                            'confirmed'       => 'bg-[#c6ebd5] text-[#2e4a3d]',
                                            'pending_payment' => 'bg-amber-100 text-amber-700',
                                            'cancelled'       => 'bg-red-100 text-red-700',
                                            default           => 'bg-[#f0f1f0] text-[#727973]',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $c }}">
                                        {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                                    </span>
                                    <p class="text-xs font-medium text-[#1b1c1a] mt-1">${{ number_format($booking->final_price ?? 0, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
