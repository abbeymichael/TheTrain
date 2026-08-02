<?php
// Livewire 4 SFC — Admin\TripShow
use Livewire\Volt\Component;
use App\Models\Trip;

new class extends Component {
    public Trip $trip;

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load(['challenges', 'specialists.user', 'specialists.challenge', 'bookings.user', 'bookings.challenges', 'series']);
    }

    public function updateStatus(string $status): void
    {
        $this->trip->update(['status' => $status]);
        $this->trip->refresh();
        session()->flash('success', 'Trip status updated.');
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>{{ $trip->title }}</x-slot:title>
    <x-slot:heading>Trip Detail</x-slot:heading>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <a href="{{ route('admin.trips') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            Back to Trips
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.trips.edit', $trip) }}"
                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-white border border-[#e4e7e5] text-[#414844] hover:border-[#416352] hover:text-[#416352] transition-colors">
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">edit</span>
                Edit
            </a>
            <a href="{{ route('admin.trip.specialists', $trip) }}"
                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-[#416352] text-white hover:bg-[#2e4a3d] transition-colors">
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">psychology</span>
                Assign Specialists
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Trip info + challenge tracks -->
        <div class="lg:col-span-1 space-y-5">
            <!-- Cover image -->
            @if ($trip->cover_image)
                <div class="bg-white rounded-xl overflow-hidden border border-[#e4e7e5] shadow-sm aspect-video">
                    <img src="{{ asset('storage/'.$trip->cover_image) }}" alt="{{ $trip->title }}" class="w-full h-full object-cover" />
                </div>
            @endif

            <!-- Summary card -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <h2 class="text-base font-semibold text-[#1b1c1a]">{{ $trip->title }}</h2>
                    @php
                        $statusColor = match($trip->status) {
                            'open'      => 'bg-[#c6ebd5] text-[#2e4a3d]',
                            'draft'     => 'bg-[#f0f1f0] text-[#727973]',
                            'closed'    => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-blue-100 text-blue-700',
                            default     => 'bg-[#f0f1f0] text-[#727973]',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }} shrink-0">
                        {{ ucfirst($trip->status) }}
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    @if ($trip->city)
                        <div class="flex items-center gap-2 text-[#414844]">
                            <span class="material-symbols-outlined text-[16px] text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">location_on</span>
                            {{ $trip->venue ? $trip->venue.', '.$trip->city : $trip->city }}
                        </div>
                    @endif
                    <div class="flex items-center gap-2 text-[#414844]">
                        <span class="material-symbols-outlined text-[16px] text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">calendar_today</span>
                        {{ $trip->start_date?->format('d M Y') }} → {{ $trip->end_date?->format('d M Y') }}
                    </div>
                    <div class="flex items-center gap-2 text-[#414844]">
                        <span class="material-symbols-outlined text-[16px] text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">group</span>
                        {{ $trip->bookings->where('status', 'confirmed')->count() }} / {{ $trip->capacity }} booked
                    </div>
                    <div class="flex items-center gap-2 text-[#414844]">
                        <span class="material-symbols-outlined text-[16px] text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">payments</span>
                        Base: <strong>${{ number_format($trip->base_price, 2) }}</strong>
                    </div>
                    <div class="text-xs text-[#9ba39c] pl-6">
                        Accommodation: ${{ number_format($trip->accommodation_cost, 2) }} · Feeding: ${{ number_format($trip->feeding_cost, 2) }}
                    </div>
                    <div class="text-xs text-[#9ba39c] pl-6">
                        Food opt-out: {{ $trip->food_deduction_type === 'percentage' ? $trip->food_deduction_value.'%' : '$'.number_format($trip->food_deduction_value, 2) }} deduction
                    </div>
                </div>

                <!-- Status change -->
                <div class="mt-4 pt-4 border-t border-[#f0f1f0] flex flex-wrap gap-2">
                    @if ($trip->status === 'draft')
                        <button wire:click="updateStatus('open')" wire:confirm="Publish this trip?"
                            class="flex-1 bg-[#c6ebd5] text-[#2e4a3d] text-xs font-semibold py-2 rounded-lg hover:bg-[#a8d9bc] transition-colors">Publish</button>
                    @elseif ($trip->status === 'open')
                        <button wire:click="updateStatus('closed')" wire:confirm="Close booking for this trip?"
                            class="flex-1 bg-amber-100 text-amber-700 text-xs font-semibold py-2 rounded-lg hover:bg-amber-200 transition-colors">Close Booking</button>
                        <button wire:click="updateStatus('completed')" wire:confirm="Mark as completed?"
                            class="flex-1 bg-blue-100 text-blue-700 text-xs font-semibold py-2 rounded-lg hover:bg-blue-200 transition-colors">Mark Completed</button>
                    @endif
                </div>
            </div>

            <!-- Challenge Tracks -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-3">Challenge Tracks</h3>
                @if ($trip->challenges->isEmpty())
                    <p class="text-xs text-[#727973]">No challenge tracks assigned. <a href="{{ route('admin.trips.edit', $trip) }}" class="text-[#416352] font-medium hover:underline">Edit trip</a> to add tracks.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($trip->challenges as $challenge)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-[#f0faf5] text-[#416352] border border-[#c6ebd5]">
                                {{ $challenge->name }}
                                @if ($challenge->is_sensitive)
                                    <span class="text-amber-500 font-bold">!</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Specialists + Roster -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Assigned Specialists -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-[#1b1c1a]">Assigned Specialists</h3>
                    <a href="{{ route('admin.trip.specialists', $trip) }}" class="text-xs text-[#416352] font-medium hover:text-[#2e4a3d] transition-colors">Manage →</a>
                </div>
                @if ($trip->specialists->isEmpty())
                    <p class="text-xs text-[#727973]">No specialists assigned yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($trip->specialists as $ts)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-[#fafbfa] border border-[#f0f1f0]">
                                <div class="w-8 h-8 rounded-full bg-[#dbeafe] flex items-center justify-center text-blue-600 text-xs font-semibold shrink-0">
                                    {{ substr($ts->user?->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#1b1c1a]">{{ $ts->user?->name ?? '—' }}</p>
                                    <p class="text-xs text-[#727973]">{{ $ts->challenge?->name }} {{ $ts->role_note ? '· '.$ts->role_note : '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Booking Roster -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-[#e4e7e5]">
                    <h3 class="text-sm font-semibold text-[#1b1c1a]">Participant Roster ({{ $trip->bookings->count() }})</h3>
                    <a href="{{ route('admin.trip.refunds', $trip) }}" class="text-xs text-[#416352] font-medium hover:text-[#2e4a3d] transition-colors">Refunds →</a>
                </div>
                @if ($trip->bookings->isEmpty())
                    <div class="py-12 text-center">
                        <p class="text-sm text-[#727973]">No bookings yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-[#f8f9f8] border-b border-[#e4e7e5]">
                                <tr>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Participant</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Primary Challenge</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Food</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Paid</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f0f1f0]">
                                @foreach ($trip->bookings as $booking)
                                    <tr class="hover:bg-[#fafbfa] transition-colors">
                                        <td class="px-5 py-3">
                                            <a href="{{ route('admin.user.show', $booking->user_id) }}" class="font-medium text-[#1b1c1a] hover:text-[#416352] transition-colors">
                                                {{ $booking->user?->name ?? '—' }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-3 text-xs text-[#414844]">
                                            {{ $booking->challenges?->firstWhere('pivot.is_primary', true)?->name ?? ($booking->challenges?->first()?->name ?? '—') }}
                                        </td>
                                        <td class="px-5 py-3 text-xs">
                                            <span class="{{ $booking->opted_out_of_food ? 'text-amber-600' : 'text-[#416352]' }}">
                                                {{ $booking->opted_out_of_food ? 'Opted out' : 'Included' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-xs font-medium text-[#1b1c1a]">${{ number_format($booking->final_price ?? 0, 2) }}</td>
                                        <td class="px-5 py-3">
                                            @php
                                                $c = match($booking->status) {
                                                    'confirmed'       => 'bg-[#c6ebd5] text-[#2e4a3d]',
                                                    'pending_payment' => 'bg-amber-100 text-amber-700',
                                                    'cancelled'       => 'bg-red-100 text-red-700',
                                                    default           => 'bg-[#f0f1f0] text-[#727973]',
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $c }}">
                                                {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
