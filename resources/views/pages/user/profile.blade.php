<?php
// Livewire 4 SFC — User\ProfileBuilder
use Livewire\Volt\Component;
use App\Models\Profile;

new class extends Component {
    public string $first_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $bio = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $allergies = '';
    public string $mobility_or_accessibility_needs = '';
    public array $dietary_restrictions = [];

    protected function rules(): array
    {
        return [
            'first_name'                    => ['required', 'string', 'max:100'],
            'last_name'                     => ['required', 'string', 'max:100'],
            'date_of_birth'                 => ['nullable', 'date', 'before:today'],
            'bio'                           => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name'        => ['required', 'string', 'max:200'],
            'emergency_contact_phone'       => ['required', 'string', 'max:30'],
            'allergies'                     => ['nullable', 'string', 'max:500'],
            'mobility_or_accessibility_needs' => ['nullable', 'string', 'max:500'],
            'dietary_restrictions'          => ['array'],
        ];
    }

    public function mount(): void
    {
        $profile = auth()->user()->profile;
        if ($profile) {
            $this->first_name                     = $profile->first_name ?? '';
            $this->last_name                      = $profile->last_name ?? '';
            $this->date_of_birth                  = $profile->date_of_birth?->format('Y-m-d') ?? '';
            $this->bio                            = $profile->bio ?? '';
            $this->emergency_contact_name         = $profile->emergency_contact_name ?? '';
            $this->emergency_contact_phone        = $profile->emergency_contact_phone ?? '';
            $this->allergies                      = $profile->allergies ?? '';
            $this->mobility_or_accessibility_needs = $profile->mobility_or_accessibility_needs ?? '';
            $this->dietary_restrictions           = (array) ($profile->dietary_restrictions ?? []);
        }
    }

    public function save(): void
    {
        $this->validate();

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'first_name'                     => $this->first_name,
                'last_name'                      => $this->last_name,
                'date_of_birth'                  => $this->date_of_birth ?: null,
                'bio'                            => $this->bio ?: null,
                'emergency_contact_name'         => $this->emergency_contact_name,
                'emergency_contact_phone'        => $this->emergency_contact_phone,
                'allergies'                      => $this->allergies ?: null,
                'mobility_or_accessibility_needs' => $this->mobility_or_accessibility_needs ?: null,
                'dietary_restrictions'           => ! empty($this->dietary_restrictions) ? $this->dietary_restrictions : null,
                'profile_visibility'             => 'private',
            ]
        );

        session()->flash('success', 'Profile saved successfully.');
    }
}; ?>

<x-layouts.user>
    <x-slot:title>My Profile</x-slot:title>

    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">Care Context Profile</h1>
            <p class="text-sm text-[#727973]">This information is private and visible only to admin and the specialist assigned to your trip. It helps us provide you with the best support experience.</p>
        </div>

        @if (session('success'))
            <div class="mb-5 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Privacy note -->
        <div class="mb-6 flex gap-3 bg-[#f0faf5] border border-[#c6ebd5] rounded-xl px-5 py-4 text-sm text-[#2e4a3d]">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5 text-[#416352]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">lock</span>
            <p>Your profile is <strong>always private</strong>. It is never shown to other participants. Only admin and the specialist assigned to your challenge track can view it.</p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <!-- Personal -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">First name <span class="text-red-500">*</span></label>
                        <input wire:model="first_name" type="text"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                        @error('first_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Last name <span class="text-red-500">*</span></label>
                        <input wire:model="last_name" type="text"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                        @error('last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Date of birth <span class="text-[#9ba39c] font-normal">(optional)</span></label>
                        <input wire:model="date_of_birth" type="date"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow" />
                    </div>
                </div>
            </div>

            <!-- Brief note -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">What brings you here <span class="text-[#9ba39c] font-normal">(optional)</span></h3>
                <p class="text-xs text-[#727973] mb-3">A brief note for your specialist. Shared only with the specialist assigned to your trip's challenge track.</p>
                <textarea wire:model="bio" rows="3"
                    class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow resize-none"
                    placeholder="In a few words, you can describe what you're working through…"></textarea>
            </div>

            <!-- Emergency contact -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">Emergency Contact <span class="text-red-500">*</span></h3>
                <p class="text-xs text-[#727973] mb-4">Required before attending a trip.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Contact name</label>
                        <input wire:model="emergency_contact_name" type="text"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="Full name" />
                        @error('emergency_contact_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Contact phone</label>
                        <input wire:model="emergency_contact_phone" type="tel"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="+1 (000) 000-0000" />
                        @error('emergency_contact_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Health & dietary -->
            <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Health & Dietary <span class="text-[#9ba39c] font-normal">(optional)</span></h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Dietary restrictions</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(['Vegetarian','Vegan','Halal','Kosher','Gluten-free','Dairy-free','Nut-free','No restrictions'] as $diet)
                                <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-[#e4e7e5] hover:border-[#416352] hover:bg-[#f0faf5] transition-colors text-sm {{ in_array($diet, $dietary_restrictions) ? 'border-[#416352] bg-[#f0faf5]' : '' }}">
                                    <input type="checkbox" wire:model="dietary_restrictions" value="{{ $diet }}"
                                        class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                                    {{ $diet }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Allergies</label>
                        <input wire:model="allergies" type="text"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow"
                            placeholder="e.g. Peanuts, shellfish…" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#414844] mb-1.5">Mobility or accessibility needs</label>
                        <textarea wire:model="mobility_or_accessibility_needs" rows="2"
                            class="w-full px-4 py-2.5 rounded-lg border border-[#c1c8c2] bg-[#fbf9f6] text-sm focus:outline-none focus:ring-2 focus:ring-[#416352] transition-shadow resize-none"
                            placeholder="e.g. Wheelchair accessible venue required, hearing impairment…"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="bg-[#416352] text-white text-sm font-semibold px-6 py-3 rounded-xl hover:bg-[#2e4a3d] transition-colors shadow-sm flex items-center gap-2">
                <span wire:loading.remove wire:target="save">Save Profile</span>
                <span wire:loading wire:target="save">Saving…</span>
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check</span>
            </button>
        </form>
    </div>
</x-layouts.user>
