<div x-data="{ showModal: @entangle('showModal') }" @challenge-saved.window="alert($event.detail.message)" @challenge-error.window="alert($event.detail.message)">
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-md mb-lg">
            <div>
                <p class="text-on-surface-variant text-sm max-w-xl">
                    Challenge categories are the core focus areas users select when booking. Keep this list dynamic and admin-managed — never hardcoded.
                </p>
            </div>
            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center justify-center gap-sm bg-primary text-on-primary px-lg py-md rounded-lg font-label-md text-label-md hover:opacity-90 transition-all shadow-md"
            >
                <span class="material-symbols-outlined">add</span>
                Add Challenge
            </button>
        </div>

        {{-- Search --}}
        <div class="bg-surface-container-low rounded-xl p-md mb-lg border border-outline-variant/30">
            <div class="flex flex-col sm:flex-row gap-md items-start sm:items-center">
                <label for="search" class="font-label-md text-label-md text-on-surface-variant">Search</label>
                <div class="relative flex-1 max-w-md">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-outline">search</span>
                    <input
                        id="search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search challenge names..."
                        class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 pr-md py-sm text-on-surface focus:ring-2 focus:ring-primary"
                    >
                </div>
                @if ($search !== '')
                    <button type="button" wire:click="$set('search', '')" class="text-primary font-label-md text-label-md hover:underline">Clear</button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-surface rounded-xl border border-outline-variant/30 overflow-hidden custom-shadow">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low border-b border-outline-variant/30">
                        <tr>
                            <th class="px-lg py-md font-label-md text-label-md text-on-surface">Name</th>
                            <th class="px-lg py-md font-label-md text-label-md text-on-surface">Description</th>
                            <th class="px-lg py-md font-label-md text-label-md text-on-surface text-center">Sensitive</th>
                            <th class="px-lg py-md font-label-md text-label-md text-on-surface text-center">Sort</th>
                            <th class="px-lg py-md font-label-md text-label-md text-on-surface text-center">Status</th>
                            <th class="px-lg py-md font-label-md text-label-md text-on-surface text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @forelse ($this->challenges as $challenge)
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="px-lg py-md">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $challenge->name }}</span>
                                    <p class="text-xs text-on-surface-variant mt-xs">{{ $challenge->slug }}</p>
                                </td>
                                <td class="px-lg py-md">
                                    <p class="text-sm text-on-surface-variant max-w-xs truncate">{{ $challenge->description }}</p>
                                </td>
                                <td class="px-lg py-md text-center">
                                    @if ($challenge->is_sensitive)
                                        <span class="inline-flex items-center gap-xs text-xs font-label-md text-tertiary bg-tertiary-fixed px-sm py-xs rounded">
                                            Yes
                                        </span>
                                    @else
                                        <span class="text-sm text-on-surface-variant">—</span>
                                    @endif
                                </td>
                                <td class="px-lg py-md text-center text-sm text-on-surface">{{ $challenge->sort_order }}</td>
                                <td class="px-lg py-md text-center">
                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $challenge->id }})"
                                        class="inline-flex items-center px-sm py-xs rounded-full text-xs font-label-md uppercase tracking-wide transition-colors {{ $challenge->is_active ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-surface-container-high text-on-surface-variant' }}"
                                    >
                                        {{ $challenge->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-lg py-md text-right">
                                    <div class="flex items-center justify-end gap-sm">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $challenge->id }})"
                                            class="text-primary hover:bg-primary/10 px-sm py-xs rounded transition-colors"
                                            title="Edit"
                                        >
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete({{ $challenge->id }})"
                                            wire:confirm="Are you sure you want to delete this challenge? This cannot be undone if it has no linked trips or bookings."
                                            class="text-error hover:bg-error/10 px-sm py-xs rounded transition-colors"
                                            title="Delete"
                                        >
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-lg py-xl text-center text-on-surface-variant">
                                    <p class="font-label-md text-label-md mb-sm">No challenges found</p>
                                    <p class="text-sm">Create a challenge to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->challenges->hasPages())
                <div class="px-lg py-md border-t border-outline-variant/30">
                    {{ $this->challenges->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-md"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-inverse-surface/60 backdrop-blur-sm" x-on:click="showModal = false"></div>
        <div class="relative w-full max-w-lg bg-surface rounded-xl custom-shadow border border-outline-variant/30 p-lg">
            <div class="flex items-center justify-between mb-lg">
                <h2 id="modal-title" class="font-display text-headline-lg text-on-surface">
                    {{ $editingId ? 'Edit Challenge' : 'Add Challenge' }}
                </h2>
                <button type="button" x-on:click="showModal = false" class="text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit.prevent="save">
                <div class="space-y-md">
                    <div>
                        <label for="name" class="block font-label-md text-label-md text-on-surface-variant mb-xs">Name</label>
                        <input id="name" type="text" wire:model="name" class="w-full bg-surface border border-outline-variant/50 rounded-lg px-md py-sm text-on-surface focus:ring-2 focus:ring-primary" placeholder="e.g. Grief & Loss">
                        @error('name') <p class="mt-xs text-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block font-label-md text-label-md text-on-surface-variant mb-xs">Description</label>
                        <textarea id="description" wire:model="description" rows="3" class="w-full bg-surface border border-outline-variant/50 rounded-lg px-md py-sm text-on-surface focus:ring-2 focus:ring-primary" placeholder="Shown to users when selecting this challenge..."></textarea>
                        @error('description') <p class="mt-xs text-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                        <div>
                            <label for="sortOrder" class="block font-label-md text-label-md text-on-surface-variant mb-xs">Sort Order</label>
                            <input id="sortOrder" type="number" min="0" wire:model="sortOrder" class="w-full bg-surface border border-outline-variant/50 rounded-lg px-md py-sm text-on-surface focus:ring-2 focus:ring-primary">
                            @error('sortOrder') <p class="mt-xs text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center gap-md sm:pt-6">
                            <label class="flex items-center gap-sm cursor-pointer">
                                <input type="checkbox" wire:model="isSensitive" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
                                <span class="font-label-md text-label-md text-on-surface">Sensitive category</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-sm">
                        <label class="flex items-center gap-sm cursor-pointer">
                            <input type="checkbox" wire:model="isActive" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
                            <span class="font-label-md text-label-md text-on-surface">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-xl flex justify-end gap-md">
                    <button type="button" wire:click="closeModal" class="px-lg py-md rounded-lg border border-outline text-on-surface font-label-md text-label-md hover:bg-surface-container-low transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-lg py-md rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:opacity-90 transition-colors">
                        {{ $editingId ? 'Update Challenge' : 'Create Challenge' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
