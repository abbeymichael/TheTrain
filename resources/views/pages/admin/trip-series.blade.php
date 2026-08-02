<?php
// Livewire 4 SFC — Admin\TripSeriesManager
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\TripSeries;

new
#[Layout('layouts::admin')]
#[Title('Trip Series')]
class extends Component {
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $cadence = 'weekly';
    public ?int $day_of_week = null;
    public ?int $day_of_month = null;
    public int $default_capacity = 20;
    public string $default_base_price = '';
    public string $default_accommodation_cost = '0';
    public string $default_feeding_cost = '0';
    public string $default_food_deduction_type = 'flat';
    public string $default_food_deduction_value = '0';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'title'                         => ['required', 'string', 'max:200'],
            'description'                   => ['nullable', 'string'],
            'cadence'                       => ['required', 'in:weekly,monthly'],
            'day_of_week'                   => ['nullable', 'integer', 'min:0', 'max:6'],
            'day_of_month'                  => ['nullable', 'integer', 'min:1', 'max:28'],
            'default_capacity'              => ['required', 'integer', 'min:1'],
            'default_base_price'            => ['required', 'numeric', 'min:0'],
            'default_accommodation_cost'    => ['required', 'numeric', 'min:0'],
            'default_feeding_cost'          => ['required', 'numeric', 'min:0'],
            'default_food_deduction_type'   => ['required', 'in:flat,percentage'],
            'default_food_deduction_value'  => ['required', 'numeric', 'min:0'],
            'is_active'                     => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $series = TripSeries::findOrFail($id);
        $this->editingId                    = $id;
        $this->title                        = $series->title;
        $this->description                  = $series->description ?? '';
        $this->cadence                      = $series->cadence;
        $this->day_of_week                  = $series->day_of_week;
        $this->day_of_month                 = $series->day_of_month;
        $this->default_capacity             = $series->default_capacity;
        $this->default_base_price           = $series->default_base_price;
        $this->default_accommodation_cost   = $series->default_accommodation_cost;
        $this->default_feeding_cost         = $series->default_feeding_cost;
        $this->default_food_deduction_type  = $series->default_food_deduction_type;
        $this->default_food_deduction_value = $series->default_food_deduction_value;
        $this->is_active                    = $series->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title'                         => $this->title,
            'description'                   => $this->description ?: null,
            'cadence'                       => $this->cadence,
            'day_of_week'                   => $this->cadence === 'weekly' ? $this->day_of_week : null,
            'day_of_month'                  => $this->cadence === 'monthly' ? $this->day_of_month : null,
            'default_capacity'              => $this->default_capacity,
            'default_base_price'            => $this->default_base_price,
            'default_accommodation_cost'    => $this->default_accommodation_cost,
            'default_feeding_cost'          => $this->default_feeding_cost,
            'default_food_deduction_type'   => $this->default_food_deduction_type,
            'default_food_deduction_value'  => $this->default_food_deduction_value,
            'is_active'                     => $this->is_active,
        ];

        if ($this->editingId) {
            TripSeries::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Trip series updated.');
        } else {
            TripSeries::create($data);
            session()->flash('success', 'Trip series created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $series = TripSeries::findOrFail($id);
        $series->update(['is_active' => !$series->is_active]);
    }

    public function delete(int $id): void
    {
        TripSeries::findOrFail($id)->delete();
        session()->flash('success', 'Series deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->description = '';
        $this->cadence = 'weekly';
        $this->day_of_week = null;
        $this->day_of_month = null;
        $this->default_capacity = 20;
        $this->default_base_price = '';
        $this->default_accommodation_cost = '0';
        $this->default_feeding_cost = '0';
        $this->default_food_deduction_type = 'flat';
        $this->default_food_deduction_value = '0';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function with(): array
    {
        return ['series' => TripSeries::withCount('trips')->orderBy('is_active', 'desc')->orderBy('title')->get()];
    }
}; ?>

<x-slot:heading>Trip Series</x-slot:heading>

<div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-5">
        <button wire:click="openCreate"
            class="flex items-center gap-2 bg-[#416352] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[#2e4a3d] transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">add</span>
            New Series
        </button>
    </div>

    <!-- Form -->
    @if ($showForm)
        <div class="bg-white rounded-xl border border-[#416352]/30 p-6 mb-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-5">{{ $editingId ? 'Edit Series' : 'New Trip Series' }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="e.g. Weekend Grief Support Retreat" />
                    @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Cadence <span class="text-red-500">*</span></label>
                    <select wire:model.live="cadence"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                @if ($cadence === 'weekly')
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Day of Week</label>
                        <select wire:model="day_of_week"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                            <option value="">Select day…</option>
                            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                                <option value="{{ $i }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Day of Month</label>
                        <input wire:model="day_of_month" type="number" min="1" max="28"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="1–28" />
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Default Capacity</label>
                    <input wire:model="default_capacity" type="number" min="1"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                </div>

                <!-- Pricing -->
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Base Price (all-inclusive) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#727973] text-sm">$</span>
                        <input wire:model="default_base_price" type="number" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="0.00" />
                    </div>
                    @error('default_base_price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Accommodation Cost (Airbnb)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#727973] text-sm">$</span>
                        <input wire:model="default_accommodation_cost" type="number" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="0.00" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Feeding Cost</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#727973] text-sm">$</span>
                        <input wire:model="default_feeding_cost" type="number" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="0.00" />
                    </div>
                </div>

                <!-- Food deduction -->
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Food Deduction Type</label>
                    <select wire:model="default_food_deduction_type"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow">
                        <option value="flat">Flat amount ($)</option>
                        <option value="percentage">Percentage (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#414844] mb-1.5">Food Deduction Value</label>
                    <input wire:model="default_food_deduction_value" type="number" step="0.01" min="0"
                        class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                        placeholder="{{ $default_food_deduction_type === 'percentage' ? '0–100' : '0.00' }}" />
                </div>

                <div class="flex items-center gap-2 self-end pb-1">
                    <input wire:model="is_active" id="series_active" type="checkbox" class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                    <label for="series_active" class="text-sm font-medium text-[#414844]">Active series</label>
                </div>
            </div>

            <div class="flex gap-3 mt-5">
                <button wire:click="save"
                    class="bg-[#416352] text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-[#2e4a3d] transition-colors">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Create Series' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
                <button wire:click="cancel" class="text-sm text-[#727973] hover:text-[#1b1c1a] px-4 py-2 rounded-lg hover:bg-[#f0f1f0] transition-colors">Cancel</button>
            </div>
        </div>
    @endif

    <!-- Series List -->
    <div class="space-y-3">
        @forelse ($series as $s)
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm flex items-start gap-4 hover:border-[#c1c8c2] transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="font-semibold text-[#1b1c1a]">{{ $s->title }}</span>
                        <span class="text-[10px] font-semibold tracking-wide uppercase px-2 py-0.5 rounded-full {{ $s->cadence === 'weekly' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $s->cadence }}
                        </span>
                        @if (! $s->is_active)
                            <span class="text-[10px] font-semibold text-[#727973] bg-[#f0f1f0] px-2 py-0.5 rounded-full">Paused</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs text-[#727973]">
                        <span>Capacity: <strong class="text-[#1b1c1a]">{{ $s->default_capacity }}</strong></span>
                        <span>Base: <strong class="text-[#1b1c1a]">${{ number_format($s->default_base_price, 2) }}</strong></span>
                        <span>Accommodation: <strong class="text-[#1b1c1a]">${{ number_format($s->default_accommodation_cost, 2) }}</strong></span>
                        <span>Feeding: <strong class="text-[#1b1c1a]">${{ number_format($s->default_feeding_cost, 2) }}</strong></span>
                        <span>Food opt-out: <strong class="text-[#1b1c1a]">{{ $s->default_food_deduction_type === 'percentage' ? $s->default_food_deduction_value.'%' : '$'.number_format($s->default_food_deduction_value, 2) }}</strong></span>
                        <span>Trips: <strong class="text-[#1b1c1a]">{{ $s->trips_count }}</strong></span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button wire:click="toggleActive({{ $s->id }})" title="{{ $s->is_active ? 'Pause' : 'Activate' }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors {{ $s->is_active ? 'text-[#416352] hover:bg-[#f0faf5]' : 'text-[#9ba39c] hover:bg-[#f0f1f0]' }}">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ $s->is_active ? '1' : '0' }},'wght' 400,'GRAD' 0,'opsz' 24;">toggle_on</span>
                    </button>
                    <button wire:click="openEdit({{ $s->id }})"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-[#727973] hover:text-[#416352] hover:bg-[#f0faf5] transition-colors">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">edit</span>
                    </button>
                    <button wire:click="delete({{ $s->id }})" wire:confirm="Delete '{{ $s->title }}'? This cannot be undone."
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-[#9ba39c] hover:text-red-600 hover:bg-red-50 transition-colors">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-[#e4e7e5] py-16 text-center shadow-sm">
                <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">repeat</span>
                <p class="text-sm text-[#727973] mb-3">No trip series yet.</p>
                <button wire:click="openCreate" class="text-sm text-[#416352] font-medium hover:underline">Create the first series →</button>
            </div>
        @endforelse
    </div>
</div>
