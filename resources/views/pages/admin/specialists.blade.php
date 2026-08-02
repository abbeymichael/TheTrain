<?php
// Livewire 4 SFC — Admin\SpecialistsTable
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function verify(int $userId): void
    {
        User::where('id', $userId)->where('role', 'specialist')->update(['status' => 'active']);
        session()->flash('success', 'Specialist verified and set to active.');
    }

    public function deactivate(int $userId): void
    {
        User::where('id', $userId)->where('role', 'specialist')->update(['status' => 'inactive']);
        session()->flash('success', 'Specialist set to inactive.');
    }

    public function activate(int $userId): void
    {
        User::where('id', $userId)->where('role', 'specialist')->update(['status' => 'active']);
        session()->flash('success', 'Specialist reactivated.');
    }

    public function reject(int $userId): void
    {
        User::where('id', $userId)->where('role', 'specialist')->update(['status' => 'rejected']);
        session()->flash('success', 'Specialist rejected.');
    }

    public function with(): array
    {
        return [
            'specialists' => User::where('role', 'specialist')
                ->with('specialist')
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
                }))
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(20),
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>Specialists</x-slot:title>
    <x-slot:heading>Specialists</x-slot:heading>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] p-4 mb-5 flex flex-col sm:flex-row gap-3 shadow-sm">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9ba39c] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">search</span>
            <input wire:model.live.debounce.300ms="search" type="search"
                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                placeholder="Search by name or email…" />
        </div>
        <select wire:model.live="statusFilter"
            class="px-3 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
            <option value="">All statuses</option>
            <option value="pending_verification">Pending Verification</option>
            <option value="verified">Verified</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#f8f9f8] border-b border-[#e4e7e5]">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Specialist</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Credentials</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0f1f0]">
                    @forelse ($specialists as $specialist)
                        <tr class="hover:bg-[#fafbfa] transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#dbeafe] flex items-center justify-center text-blue-600 text-xs font-semibold shrink-0">
                                        {{ substr($specialist->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.specialist.show', $specialist) }}" class="font-medium text-[#1b1c1a] hover:text-[#416352] transition-colors">{{ $specialist->name }}</a>
                                        <p class="text-xs text-[#727973]">{{ $specialist->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-[#414844] text-xs">
                                {{ $specialist->specialist?->credentials ?? '—' }}
                            </td>
                            <td class="px-6 py-3.5">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ str_replace('_', ' ', ucfirst($specialist->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.specialist.show', $specialist) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#f0faf5] text-[#416352] hover:bg-[#c6ebd5] transition-colors">
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">visibility</span>
                                        View
                                    </a>
                                    @if (in_array($specialist->status, ['pending_verification', 'verified']))
                                        <button wire:click="verify({{ $specialist->id }})" wire:confirm="Verify and activate this specialist?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#c6ebd5] text-[#2e4a3d] hover:bg-[#a8d9bc] transition-colors">
                                            Activate
                                        </button>
                                        <button wire:click="reject({{ $specialist->id }})" wire:confirm="Reject this specialist?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                            Reject
                                        </button>
                                    @elseif ($specialist->status === 'active')
                                        <button wire:click="deactivate({{ $specialist->id }})" wire:confirm="Deactivate this specialist?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors">
                                            Deactivate
                                        </button>
                                    @elseif ($specialist->status === 'inactive')
                                        <button wire:click="activate({{ $specialist->id }})" wire:confirm="Reactivate this specialist?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#c6ebd5] text-[#2e4a3d] hover:bg-[#a8d9bc] transition-colors">
                                            Reactivate
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-sm text-[#727973]">No specialists found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($specialists->hasPages())
            <div class="px-6 py-4 border-t border-[#e4e7e5]">
                {{ $specialists->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
