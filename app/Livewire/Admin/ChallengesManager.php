<?php

namespace App\Livewire\Admin;

use App\Models\Challenge;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * CRUD for the dynamic challenge category list (agent.md Section 10).
 *
 * Challenge names are user-facing, admin-managed, and must never be
 * hardcoded in code. This is the canonical interface for creating,
 * editing, activating, and soft-removing challenge categories.
 */
class ChallengesManager extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';
    public string $description = '';
    public bool $isSensitive = false;
    public int $sortOrder = 0;
    public bool $isActive = true;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('challenges', 'name')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'isSensitive' => ['boolean'],
            'sortOrder' => ['integer', 'min:0'],
            'isActive' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.unique' => 'A challenge with this name already exists.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $challenge = Challenge::findOrFail($id);

        $this->editingId = $challenge->id;
        $this->name = $challenge->name;
        $this->description = $challenge->description ?? '';
        $this->isSensitive = $challenge->is_sensitive;
        $this->sortOrder = $challenge->sort_order;
        $this->isActive = $challenge->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        Challenge::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $validated['name'],
                'description' => $validated['description'] ?: null,
                'is_sensitive' => $validated['isSensitive'],
                'sort_order' => $validated['sortOrder'],
                'is_active' => $validated['isActive'],
            ]
        );

        $this->dispatch('challenge-saved', message: $this->editingId ? 'Challenge updated.' : 'Challenge created.');
        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $challenge = Challenge::findOrFail($id);
        $challenge->update(['is_active' => ! $challenge->is_active]);

        $this->dispatch('challenge-saved', message: 'Challenge status updated.');
    }

    public function delete(int $id): void
    {
        $challenge = Challenge::findOrFail($id);

        // Prevent deletion if the challenge is tied to existing trips or bookings
        // to preserve data integrity. Admins can deactivate instead.
        if ($challenge->trips()->exists() || $challenge->bookings()->exists()) {
            $this->dispatch('challenge-error', message: 'Cannot delete a challenge that is attached to trips or bookings. Deactivate it instead.');
            return;
        }

        $challenge->delete();
        $this->dispatch('challenge-saved', message: 'Challenge deleted.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'description', 'isSensitive', 'sortOrder', 'isActive']);
        $this->editingId = null;
        $this->isActive = true;
        $this->sortOrder = 0;
        $this->isSensitive = false;
    }

    #[Computed]
    public function challenges(): object
    {
        return Challenge::query()
            ->when($this->search !== '', function ($query): void {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.challenges-manager')
            ->layout('layouts.admin', [
                'title' => 'Manage Challenges',
                'page_title' => 'Challenges',
            ]);
    }
}
