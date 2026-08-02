<?php
// Livewire 4 SFC — Admin\RefundManager
use Livewire\Volt\Component;
use App\Models\Trip;
use App\Models\Booking;

new class extends Component {
    public Trip $trip;
    public string $refundNote = '';

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load(['bookings.user']);
    }

    public function issueRefund(int $bookingId): void
    {
        $booking = Booking::where('trip_id', $this->trip->id)->findOrFail($bookingId);

        // Guard: only refund confirmed bookings with Stripe verified
        if (! $booking->stripe_verified || $booking->status !== 'confirmed') {
            session()->flash('error', 'Cannot refund: booking is not confirmed or payment not verified.');
            return;
        }

        if ($booking->refund_issued) {
            session()->flash('error', 'Refund already issued for this booking.');
            return;
        }

        // NOTE: Actual Stripe API call goes here when Stripe is integrated.
        // For now, mark the record — Stripe integration in next phase.
        $booking->update([
            'refund_issued' => true,
            'refunded_at'   => now(),
            'status'        => 'cancelled',
        ]);

        session()->flash('success', "Refund marked for {$booking->user?->name}. (Stripe API call required in production.)");
        $this->trip->load(['bookings.user']);
    }

    public function with(): array
    {
        return [
            'eligibleBookings' => $this->trip->bookings()
                ->with('user')
                ->whereIn('status', ['confirmed', 'cancelled'])
                ->where('stripe_verified', true)
                ->orderByRaw('refund_issued ASC')
                ->orderBy('confirmed_at', 'desc')
                ->get(),
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>Refunds — {{ $trip->title }}</x-slot:title>
    <x-slot:heading>Refund Manager</x-slot:heading>

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
    @if (session('error'))
        <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">error</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Caution banner -->
    <div class="mb-5 flex gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 text-sm text-amber-800">
        <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">warning</span>
        <div>
            <p class="font-semibold mb-0.5">Refunds are irreversible.</p>
            <p>Only confirmed, Stripe-verified bookings can be refunded. Issuing a refund will mark the booking as cancelled and trigger the Stripe API call (Stripe integration required in production).</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#e4e7e5]">
            <h3 class="text-sm font-semibold text-[#1b1c1a]">Eligible Bookings</h3>
        </div>

        @if ($eligibleBookings->isEmpty())
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">payments</span>
                <p class="text-sm text-[#727973]">No confirmed, Stripe-verified bookings to refund.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8f9f8] border-b border-[#e4e7e5]">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Participant</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Amount Paid</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Confirmed</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Refund Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f1f0]">
                        @foreach ($eligibleBookings as $booking)
                            <tr class="hover:bg-[#fafbfa] transition-colors {{ $booking->refund_issued ? 'opacity-60' : '' }}">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-[#c6ebd5] flex items-center justify-center text-[#416352] text-xs font-semibold shrink-0">
                                            {{ substr($booking->user?->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-[#1b1c1a]">{{ $booking->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-[#1b1c1a]">${{ number_format($booking->final_price ?? 0, 2) }}</td>
                                <td class="px-6 py-3.5 text-[#727973] text-xs">{{ $booking->confirmed_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-6 py-3.5">
                                    @if ($booking->refund_issued)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check</span>
                                            Refunded {{ $booking->refunded_at?->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#f0f1f0] text-[#727973]">Not refunded</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    @if (! $booking->refund_issued)
                                        <button wire:click="issueRefund({{ $booking->id }})"
                                            wire:confirm="Issue refund of ${{ number_format($booking->final_price ?? 0, 2) }} to {{ $booking->user?->name }}? This is irreversible."
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors">
                                            Issue Refund
                                        </button>
                                    @else
                                        <span class="text-xs text-[#9ba39c]">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
