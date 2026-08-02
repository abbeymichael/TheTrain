<?php
// Livewire 4 SFC — Admin\TripsManager
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Trip;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function updateStatus(int $id, string $status): void
    {
        Trip::findOrFail($id)->update(['status' => $status]);
        session()->flash('success', 'Trip status updated.');
    }

    public function delete(int $id): void
    {
        Trip::findOrFail($id)->delete();
        session()->flash('success', 'Trip deleted.');
    }

    public function with(): array
    {
        return [
            'trips' => Trip::with('series')
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                      ->orWhere('city', 'like', '%'.$this->search.'%');
                }))
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->orderBy('start_date', 'desc')
                ->paginate(20),
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>Trips</x-slot:title>
    <x-slot:heading>Trips</x-slot:heading>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9ba39c] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">search</span>
            <input wire:model.live.debounce.300ms="search" type="search"
                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                placeholder="Search by title or city…" />
        </div>
        <select wire:model.live="statusFilter"
            class="px-3 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
            <option value="completed">Completed</option>
        </select>
        <a href="{{ route('admin.trips.create') }}"
            class="flex items-center gap-2 bg-[#416352] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors shadow-sm whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">add</span>
            New Trip
        </a>
    </div>

    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#f8f9f8] border-b border-[#e4e7e5]">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Trip</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Dates</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Price</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0f1f0]">
                    @forelse ($trips as $trip)
                        <tr class="hover:bg-[#fafbfa] transition-colors">
                            <td class="px-6 py-3.5">
                                <div>
                                    <a href="{{ route('admin.trip.show', $trip) }}" class="font-medium text-[#1b1c1a] hover:text-[#416352] transition-colors">{{ $trip->title }}</a>
                                    @if ($trip->city)
                                        <p class="text-xs text-[#727973] flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">location_on</span>
                                            {{ $trip->city }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-[#414844] text-xs">
                                {{ $trip->start_date?->format('d M Y') }}<br>
                                <span class="text-[#9ba39c]">→ {{ $trip->end_date?->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-medium text-[#1b1c1a]">${{ number_format($trip->base_price, 2) }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $statusColor = match($trip->status) {
                                        'open'      => 'bg-[#c6ebd5] text-[#2e4a3d]',
                                        'draft'     => 'bg-[#f0f1f0] text-[#727973]',
                                        'closed'    => 'bg-amber-100 text-amber-700',
                                        'completed' => 'bg-blue-100 text-blue-700',
                                        default     => 'bg-[#f0f1f0] text-[#727973]',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst($trip->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.trip.show', $trip) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#f0faf5] text-[#416352] hover:bg-[#c6ebd5] transition-colors">
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">visibility</span>
                                        View
                                    </a>
                                    <a href="{{ route('admin.trips.edit', $trip) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#f0f1f0] text-[#414844] hover:bg-[#e4e7e5] transition-colors">
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">edit</span>
                                        Edit
                                    </a>
                                    @if ($trip->status === 'draft')
                                        <button wire:click="updateStatus({{ $trip->id }}, 'open')" wire:confirm="Publish this trip?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#c6ebd5] text-[#2e4a3d] hover:bg-[#a8d9bc] transition-colors">
                                            Publish
                                        </button>
                                    @elseif ($trip->status === 'open')
                                        <button wire:click="updateStatus({{ $trip->id }}, 'closed')" wire:confirm="Close this trip?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors">
                                            Close
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-[#727973]">No trips found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($trips->hasPages())
            <div class="px-6 py-4 border-t border-[#e4e7e5]">{{ $trips->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
