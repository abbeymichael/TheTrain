<?php
// Livewire 4 SFC — Admin\ChallengesManager
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Challenge;

new
#[Layout('layouts::admin')]
#[Title('Challenges')]
class extends Component {
    // List
    public string $search = '';

    // Form (create / edit)
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $description = '';
    public bool $is_sensitive = false;
    public bool $is_active = true;
    public int $sort_order = 0;

    protected function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_sensitive' => ['boolean'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer', 'min:0'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $challenge = Challenge::findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $challenge->name;
        $this->description = $challenge->description ?? '';
        $this->is_sensitive = $challenge->is_sensitive;
        $this->is_active   = $challenge->is_active;
        $this->sort_order  = $challenge->sort_order;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'         => $this->name,
            'slug'         => \Illuminate\Support\Str::slug($this->name),
            'description'  => $this->description ?: null,
            'is_sensitive' => $this->is_sensitive,
            'is_active'    => $this->is_active,
            'sort_order'   => $this->sort_order,
        ];

        if ($this->editingId) {
            Challenge::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Challenge updated.');
        } else {
            Challenge::create($data);
            session()->flash('success', 'Challenge created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $challenge = Challenge::findOrFail($id);
        $challenge->update(['is_active' => !$challenge->is_active]);
    }

    public function delete(int $id): void
    {
        Challenge::findOrFail($id)->delete();
        session()->flash('success', 'Challenge deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId    = null;
        $this->name         = '';
        $this->description  = '';
        $this->is_sensitive = false;
        $this->is_active    = true;
        $this->sort_order   = 0;
        $this->resetValidation();
    }

    public function with(): array
    {
        return [
            'challenges' => Challenge::when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ];
    }
}; ?>

<x-slot:heading>Challenge Categories</x-slot:heading>

<div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
        <div class="relative flex-1 max-w-xs">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9ba39c] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">search</span>
            <input wire:model.live.debounce.300ms="search" type="search"
                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                placeholder="Search challenges…" />
        </div>
        <button wire:click="openCreate"
            class="ml-auto flex items-center gap-2 bg-[#416352] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">add</span>
            Add Challenge
        </button>
    </div>

    <!-- Create / Edit Form -->
    @if ($showForm)
        <div class="bg-white rounded-xl border border-[#416352]/30 p-6 mb-5 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">{{ $editingId ? 'Edit Challenge' : 'New Challenge' }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="e.g. Grief & Loss" />
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Sort Order</label>
                    <input wire:model="sort_order" type="number" min="0"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Description (shown to users)</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow resize-none"
                        placeholder="Brief description shown to participants when selecting challenges…"></textarea>
                    @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="is_sensitive" type="checkbox" class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                        <span class="text-sm font-medium text-[#414844]">Sensitive category</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="is_active" type="checkbox" class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                        <span class="text-sm font-medium text-[#414844]">Active (visible to users)</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="save"
                    class="bg-[#416352] text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-[#2e4a3d] transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Create Challenge' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
                <button wire:click="cancel" class="text-sm text-[#727973] hover:text-[#1b1c1a] px-4 py-2 rounded-lg hover:bg-[#f0f1f0] transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    <!-- Challenges List -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
        @if ($challenges->isEmpty())
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">favorite</span>
                <p class="text-sm text-[#727973] mb-3">No challenge categories yet.</p>
                <button wire:click="openCreate" class="text-sm text-[#416352] font-medium hover:underline">Add the first challenge →</button>
            </div>
        @else
            <div class="divide-y divide-[#f0f1f0]">
                @foreach ($challenges as $challenge)
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-[#fafbfa] transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-[#1b1c1a] text-sm">{{ $challenge->name }}</span>
                                @if ($challenge->is_sensitive)
                                    <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Sensitive</span>
                                @endif
                                @if (! $challenge->is_active)
                                    <span class="text-[10px] font-semibold text-[#727973] bg-[#f0f1f0] px-2 py-0.5 rounded-full">Inactive</span>
                                @endif
                            </div>
                            @if ($challenge->description)
                                <p class="text-xs text-[#727973] mt-0.5 truncate max-w-xl">{{ $challenge->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <!-- Toggle active -->
                            <button wire:click="toggleActive({{ $challenge->id }})" title="{{ $challenge->is_active ? 'Deactivate' : 'Activate' }}"
                                class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors {{ $challenge->is_active ? 'text-[#416352] hover:bg-[#f0faf5]' : 'text-[#9ba39c] hover:bg-[#f0f1f0]' }}">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ $challenge->is_active ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">toggle_on</span>
                            </button>
                            <button wire:click="openEdit({{ $challenge->id }})"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-[#727973] hover:text-[#416352] hover:bg-[#f0faf5] transition-colors">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">edit</span>
                            </button>
                            <button wire:click="delete({{ $challenge->id }})" wire:confirm="Delete '{{ $challenge->name }}'? This cannot be undone."
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-[#9ba39c] hover:text-red-600 hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">delete</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
