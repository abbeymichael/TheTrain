<?php
// Livewire 4 SFC — Admin\TripEditor (create + edit)
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Trip;
use App\Models\TripSeries;
use App\Models\Challenge;

new class extends Component {
    use WithFileUploads;

    // Mode
    public ?Trip $trip = null;
    public bool $isEditing = false;

    // Fields
    public ?int $trip_series_id = null;
    public string $title = '';
    public string $description = '';
    public string $venue = '';
    public string $city = '';
    public string $start_date = '';
    public string $end_date = '';
    public int $capacity = 20;
    public string $base_price = '';
    public string $accommodation_cost = '0';
    public string $feeding_cost = '0';
    public string $food_deduction_type = 'flat';
    public string $food_deduction_value = '0';
    public string $status = 'draft';
    public $cover_image = null;
    public array $selectedChallenges = [];

    protected function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:200'],
            'description'         => ['nullable', 'string'],
            'venue'               => ['nullable', 'string', 'max:200'],
            'city'                => ['nullable', 'string', 'max:100'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['required', 'date', 'after_or_equal:start_date'],
            'capacity'            => ['required', 'integer', 'min:1'],
            'base_price'          => ['required', 'numeric', 'min:0'],
            'accommodation_cost'  => ['required', 'numeric', 'min:0'],
            'feeding_cost'        => ['required', 'numeric', 'min:0'],
            'food_deduction_type' => ['required', 'in:flat,percentage'],
            'food_deduction_value' => ['required', 'numeric', 'min:0'],
            'status'              => ['required', 'in:draft,open,closed,completed'],
            'cover_image'         => ['nullable', 'image', 'max:2048'],
            'selectedChallenges'  => ['array'],
        ];
    }

    public function mount(?Trip $trip = null): void
    {
        if ($trip && $trip->exists) {
            $this->isEditing = true;
            $this->trip = $trip->load('challenges');
            $this->title               = $trip->title;
            $this->description         = $trip->description ?? '';
            $this->venue               = $trip->venue ?? '';
            $this->city                = $trip->city ?? '';
            $this->start_date          = $trip->start_date?->format('Y-m-d') ?? '';
            $this->end_date            = $trip->end_date?->format('Y-m-d') ?? '';
            $this->capacity            = $trip->capacity;
            $this->base_price          = $trip->base_price;
            $this->accommodation_cost  = $trip->accommodation_cost;
            $this->feeding_cost        = $trip->feeding_cost;
            $this->food_deduction_type = $trip->food_deduction_type;
            $this->food_deduction_value = $trip->food_deduction_value;
            $this->status              = $trip->status;
            $this->trip_series_id      = $trip->trip_series_id;
            $this->selectedChallenges  = $trip->challenges->pluck('id')->toArray();
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'trip_series_id'      => $this->trip_series_id ?: null,
            'title'               => $this->title,
            'description'         => $this->description ?: null,
            'venue'               => $this->venue ?: null,
            'city'                => $this->city ?: null,
            'start_date'          => $this->start_date,
            'end_date'            => $this->end_date,
            'capacity'            => $this->capacity,
            'base_price'          => $this->base_price,
            'accommodation_cost'  => $this->accommodation_cost,
            'feeding_cost'        => $this->feeding_cost,
            'food_deduction_type' => $this->food_deduction_type,
            'food_deduction_value' => $this->food_deduction_value,
            'status'              => $this->status,
        ];

        if ($this->cover_image) {
            $data['cover_image'] = $this->cover_image->store('trips', 'public');
        }

        if ($this->isEditing) {
            $this->trip->update($data);
            $this->trip->challenges()->sync($this->selectedChallenges);
            session()->flash('success', 'Trip updated successfully.');
            $this->redirect(route('admin.trip.show', $this->trip), navigate: true);
        } else {
            $trip = Trip::create($data);
            $trip->challenges()->sync($this->selectedChallenges);
            session()->flash('success', 'Trip created successfully.');
            $this->redirect(route('admin.trip.show', $trip), navigate: true);
        }
    }

    public function with(): array
    {
        return [
            'seriesList' => TripSeries::where('is_active', true)->orderBy('title')->get(),
            'challenges' => Challenge::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>{{ $isEditing ? 'Edit Trip' : 'New Trip' }}</x-slot:title>
    <x-slot:heading>{{ $isEditing ? 'Edit Trip' : 'Create Trip' }}</x-slot:heading>

    <div class="mb-4">
        <a href="{{ route('admin.trips') }}" class="inline-flex items-center gap-1.5 text-sm text-[#416352] hover:text-[#2e4a3d] transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_back</span>
            Back to Trips
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#416352] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">info</span>
                Basic Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="e.g. Coastal Grief & Renewal Retreat" />
                    @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Description</label>
                    <textarea wire:model="description" rows="4"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow resize-none"
                        placeholder="Describe the retreat, its theme, and what participants can expect…"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Venue</label>
                    <input wire:model="venue" type="text"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="e.g. Seacliff Lodge" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">City</label>
                    <input wire:model="city" type="text"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="e.g. Cape Town" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Start Date <span class="text-red-500">*</span></label>
                    <input wire:model="start_date" type="date"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                    @error('start_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">End Date <span class="text-red-500">*</span></label>
                    <input wire:model="end_date" type="date"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                    @error('end_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Capacity</label>
                    <input wire:model="capacity" type="number" min="1"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Status</label>
                    <select wire:model="status"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                        <option value="draft">Draft</option>
                        <option value="open">Open for Booking</option>
                        <option value="closed">Closed</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Trip Series (optional)</label>
                    <select wire:model="trip_series_id"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                        <option value="">One-off trip (no series)</option>
                        @foreach ($seriesList as $s)
                            <option value="{{ $s->id }}">{{ $s->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Cover Image</label>
                    <input wire:model="cover_image" type="file" accept="image/*"
                        class="w-full text-sm text-[#414844] file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-[#c6ebd5] file:text-[#2e4a3d] file:font-medium file:text-xs hover:file:bg-[#a8d9bc] cursor-pointer" />
                    @if ($isEditing && $trip->cover_image)
                        <p class="text-xs text-[#727973] mt-1">Current image retained if no new file selected.</p>
                    @endif
                    @error('cover_image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#416352] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">payments</span>
                Pricing Breakdown
            </h3>
            <p class="text-xs text-[#727973] mb-5">The <strong>Base Price</strong> is the all-inclusive amount charged to participants. The accommodation and feeding lines are informational breakdowns for admin visibility.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Base Price (all-inclusive) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#727973] text-sm">$</span>
                        <input wire:model="base_price" type="number" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="0.00" />
                    </div>
                    @error('base_price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Accommodation Cost (Airbnb, informational)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#727973] text-sm">$</span>
                        <input wire:model="accommodation_cost" type="number" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="0.00" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Feeding Cost (informational)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#727973] text-sm">$</span>
                        <input wire:model="feeding_cost" type="number" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="0.00" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Food Opt-out Deduction Type</label>
                    <select wire:model="food_deduction_type"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                        <option value="flat">Flat amount ($)</option>
                        <option value="percentage">Percentage (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Food Opt-out Deduction Value</label>
                    <input wire:model="food_deduction_value" type="number" step="0.01" min="0"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="{{ $food_deduction_type === 'percentage' ? '0–100' : '0.00' }}" />
                    @error('food_deduction_value') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Price preview -->
            @if ($base_price > 0 && $food_deduction_value > 0)
                <div class="mt-4 bg-[#f0faf5] border border-[#c6ebd5] rounded-lg p-4 text-sm">
                    <p class="font-semibold text-[#2e4a3d] mb-1">Price Preview</p>
                    <p class="text-[#414844]">
                        Full price: <strong>${{ number_format((float)$base_price, 2) }}</strong>
                        &nbsp;→&nbsp;
                        Without food:
                        @if ($food_deduction_type === 'flat')
                            <strong>${{ number_format(max(0, (float)$base_price - (float)$food_deduction_value), 2) }}</strong>
                        @else
                            <strong>${{ number_format(max(0, (float)$base_price - ((float)$base_price * (float)$food_deduction_value / 100)), 2) }}</strong>
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Challenge Tracks -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#416352] text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">favorite</span>
                Challenge Tracks
            </h3>
            <p class="text-xs text-[#727973] mb-4">Select which challenge categories will be supported on this trip.</p>
            @if ($challenges->isEmpty())
                <p class="text-sm text-[#727973]">No challenges defined. <a href="{{ route('admin.challenges') }}" class="text-[#416352] font-medium hover:underline">Add challenges first</a>.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($challenges as $challenge)
                        <label class="flex items-center gap-2.5 p-3 rounded-lg border border-[#e4e7e5] hover:border-[#416352] hover:bg-[#f0faf5] cursor-pointer transition-colors {{ in_array($challenge->id, $selectedChallenges) ? 'border-[#416352] bg-[#f0faf5]' : '' }}">
                            <input type="checkbox" wire:model="selectedChallenges" value="{{ $challenge->id }}"
                                class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                            <span class="text-sm font-medium text-[#1b1c1a]">{{ $challenge->name }}</span>
                            @if ($challenge->is_sensitive)
                                <span class="ml-auto text-[9px] text-amber-600 bg-amber-50 border border-amber-200 px-1 py-0.5 rounded-full font-bold uppercase tracking-wide shrink-0">!</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Submit -->
        <div class="flex gap-3">
            <button type="submit"
                class="bg-[#416352] text-white text-sm font-semibold px-6 py-3 rounded-xl hover:bg-[#2e4a3d] transition-colors shadow-sm flex items-center gap-2">
                <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Save Changes' : 'Create Trip' }}</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
            <a href="{{ route('admin.trips') }}" class="text-sm text-[#727973] hover:text-[#1b1c1a] px-5 py-3 rounded-xl hover:bg-[#f0f1f0] transition-colors">
                Cancel
            </a>
        </div>
    </form>
</x-layouts.admin>
