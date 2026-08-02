<?php
// Livewire 4 SFC — Admin\UsersTable
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\User;

new
#[Layout('layouts::admin')]
#[Title('Users')]
class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        User::where('id', $id)->where('role', 'user')->update(['status' => 'approved']);
        session()->flash('success', 'User approved.');
    }

    public function reject(int $id): void
    {
        User::where('id', $id)->where('role', 'user')->update(['status' => 'rejected']);
        session()->flash('success', 'User rejected.');
    }

    public function suspend(int $id): void
    {
        User::where('id', $id)->where('role', 'user')->update(['status' => 'suspended']);
        session()->flash('success', 'User suspended.');
    }

    public function reinstate(int $id): void
    {
        User::where('id', $id)->where('role', 'user')->update(['status' => 'approved']);
        session()->flash('success', 'User reinstated.');
    }

    public function with(): array
    {
        return [
            'users' => User::where('role', 'user')
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
                }))
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate(20),
        ];
    }
}; ?>

<x-slot:heading>Users</x-slot:heading>

<div>

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
                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                placeholder="Search by name or email…" />
        </div>
        <select wire:model.live="statusFilter"
            class="px-3 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#f8f9f8] border-b border-[#e4e7e5]">
                    <tr>
                        <th class="text-left px-6 py-3">
                            <button wire:click="sort('name')" class="flex items-center gap-1 text-xs font-semibold text-[#727973] uppercase tracking-wide hover:text-[#1b1c1a] transition-colors">
                                Name
                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">{{ $sortBy === 'name' ? ($sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward') : 'unfold_more' }}</span>
                            </button>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Status</th>
                        <th class="text-left px-6 py-3">
                            <button wire:click="sort('created_at')" class="flex items-center gap-1 text-xs font-semibold text-[#727973] uppercase tracking-wide hover:text-[#1b1c1a] transition-colors">
                                Joined
                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">{{ $sortBy === 'created_at' ? ($sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward') : 'unfold_more' }}</span>
                            </button>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0f1f0]">
                    @forelse ($users as $user)
                        <tr class="hover:bg-[#fafbfa] transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#c6ebd5] flex items-center justify-center text-[#416352] text-xs font-semibold shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.user.show', $user) }}" class="font-medium text-[#1b1c1a] hover:text-[#416352] transition-colors">{{ $user->name }}</a>
                                        @if ($user->email_verified_at)
                                            <span class="block text-[11px] text-[#416352]">✓ Verified</span>
                                        @else
                                            <span class="block text-[11px] text-amber-600">Email unverified</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-[#414844]">{{ $user->email }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $statusColor = match($user->status) {
                                        'approved'  => 'bg-[#c6ebd5] text-[#2e4a3d]',
                                        'pending'   => 'bg-amber-100 text-amber-700',
                                        'rejected'  => 'bg-red-100 text-red-700',
                                        'suspended' => 'bg-orange-100 text-orange-700',
                                        default     => 'bg-[#f0f1f0] text-[#727973]',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-[#727973]">{{ $user->created_at?->format('d M Y') }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.user.show', $user) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#f0faf5] text-[#416352] hover:bg-[#c6ebd5] transition-colors">
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">visibility</span>
                                        View
                                    </a>
                                    @if ($user->status === 'pending')
                                        <button wire:click="approve({{ $user->id }})" wire:confirm="Approve this user?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#c6ebd5] text-[#2e4a3d] hover:bg-[#a8d9bc] transition-colors">
                                            Approve
                                        </button>
                                        <button wire:click="reject({{ $user->id }})" wire:confirm="Reject this user?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                            Reject
                                        </button>
                                    @elseif ($user->status === 'approved')
                                        <button wire:click="suspend({{ $user->id }})" wire:confirm="Suspend this user?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors">
                                            Suspend
                                        </button>
                                    @elseif ($user->status === 'suspended')
                                        <button wire:click="reinstate({{ $user->id }})" wire:confirm="Reinstate this user?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-[#c6ebd5] text-[#2e4a3d] hover:bg-[#a8d9bc] transition-colors">
                                            Reinstate
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-[#727973]">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-[#e4e7e5]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
