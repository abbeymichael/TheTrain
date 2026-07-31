<div>
    <main class="pt-32 pb-xl px-container-margin max-w-7xl mx-auto">
        {{-- Header --}}
        <section class="mb-xl max-w-3xl">
            <h1 class="font-display text-display-lg text-on-surface mb-md">Upcoming Trips</h1>
            <p class="font-body text-body-lg text-on-surface-variant">
                Find your path to restoration. Each journey is thoughtfully curated to provide a safe haven for healing, community, and personal growth. Select a challenge track that resonates with your current chapter.
            </p>
        </section>

        {{-- Filters --}}
        <section class="mb-xl">
            <div class="bg-surface-container-low rounded-xl p-lg custom-shadow border border-outline-variant/20">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-lg">
                    {{-- Cadence --}}
                    <div class="flex flex-col gap-xs">
                        <label for="cadence" class="font-label-md text-label-md text-on-surface-variant">Cadence</label>
                        <select id="cadence" wire:model.live="cadence" class="bg-surface-container-highest border border-outline-variant/50 rounded-lg font-body text-on-surface focus:ring-2 focus:ring-primary h-12 px-md">
                            <option value="all">All Frequencies</option>
                            <option value="weekly">Weekly Retreats</option>
                            <option value="monthly">Monthly Immersions</option>
                        </select>
                    </div>

                    {{-- Challenges --}}
                    <div class="flex flex-col gap-xs md:col-span-2">
                        <label class="font-label-md text-label-md text-on-surface-variant">Focus Areas</label>
                        <div class="flex flex-wrap gap-sm pt-xs">
                            <button
                                type="button"
                                wire:click="$set('selectedChallenges', [])"
                                class="px-md py-xs rounded-full border font-label-md text-label-md transition-colors {{ empty($selectedChallenges) ? 'border-primary bg-primary-fixed text-on-primary-fixed' : 'border-outline bg-surface hover:bg-surface-container-high' }}"
                            >
                                All Challenges
                            </button>
                            @foreach ($this->challenges as $challenge)
                                <button
                                    type="button"
                                    wire:click="$toggle('selectedChallenges', {{ $challenge->id }})"
                                    class="px-md py-xs rounded-full border font-label-md text-label-md transition-colors {{ in_array($challenge->id, $selectedChallenges) ? 'border-primary bg-primary-fixed text-on-primary-fixed' : 'border-outline bg-surface hover:bg-surface-container-high' }}"
                                >
                                    {{ $challenge->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="flex flex-col gap-xs">
                        <label for="location" class="font-label-md text-label-md text-on-surface-variant">Location</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-[20px]">location_on</span>
                            <input id="location" wire:model.live.debounce.300ms="location" type="text" placeholder="Search cities..." class="w-full bg-surface-container-highest border border-outline-variant/50 rounded-lg font-body text-on-surface focus:ring-2 focus:ring-primary h-12 pl-10 pr-md">
                        </div>
                    </div>
                </div>

                @if ($cadence !== 'all' || $location !== '' || ! empty($selectedChallenges))
                    <div class="mt-lg flex justify-end">
                        <button type="button" wire:click="resetFilters" class="text-primary font-label-md text-label-md hover:underline">
                            Reset all filters
                        </button>
                    </div>
                @endif
            </div>
        </section>

        {{-- Trip Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-lg" id="trips-grid">
            @forelse ($this->trips as $trip)
                <div class="group bg-surface-container-lowest rounded-xl overflow-hidden slate-shadow card-hover border border-outline-variant/10">
                    <div class="relative h-64 overflow-hidden">
                        <img
                            src="{{ $trip->cover_image ?: 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=800&q=80' }}"
                            alt="{{ $trip->title }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        >
                        @if ($loop->first)
                            <div class="absolute top-4 left-4">
                                <span class="bg-primary/90 text-on-primary text-[12px] px-sm py-xs rounded font-bold uppercase tracking-wider">Most Popular</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-lg">
                        <div class="flex justify-between items-start mb-sm">
                            <h3 class="font-display text-headline-md text-on-surface">{{ $trip->title }}</h3>
                            <div class="text-right">
                                <span class="block font-label-md text-label-md text-on-surface-variant">All-Inclusive</span>
                                <span class="font-bold text-primary">${{ number_format($trip->base_price, 0) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-xs text-on-surface-variant font-body mb-md">
                            <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                            <span>{{ $trip->start_date->format('M j') }} — {{ $trip->end_date->format('M j, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-xs text-on-surface-variant font-body mb-md">
                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                            <span>{{ $trip->city ?: $trip->venue }}</span>
                        </div>
                        <div class="flex flex-wrap gap-xs mb-lg">
                            @foreach ($trip->challenges->take(3) as $challenge)
                                <span class="px-sm py-1 bg-secondary-container text-on-secondary-container rounded font-label-md text-label-md">{{ $challenge->name }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('trip.show', $trip) }}" class="block w-full py-md border border-primary text-primary font-label-md text-label-md rounded-lg hover:bg-primary hover:text-on-primary transition-all text-center">
                            View Itinerary
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-xl flex flex-col items-center text-center max-w-lg mx-auto">
                    <div class="w-24 h-24 bg-surface-container-high rounded-full flex items-center justify-center mb-lg">
                        <span class="material-symbols-outlined text-outline text-[48px]">nature_people</span>
                    </div>
                    <h2 class="font-display text-headline-lg text-on-surface mb-sm">A New Path Awaits</h2>
                    <p class="font-body text-body-lg text-on-surface-variant mb-xl">
                        We couldn't find any trips matching your current filters. Sometimes the best journeys are the ones we didn't expect to take.
                    </p>
                    <button type="button" wire:click="resetFilters" class="bg-primary text-on-primary px-xl py-md rounded-full font-label-md text-label-md hover:opacity-90 transition-all">
                        View All Trips
                    </button>
                </div>
            @endforelse
        </section>

        {{-- Pagination --}}
        @if ($this->trips->hasPages())
            <div class="mt-xl">
                {{ $this->trips->links() }}
            </div>
        @endif
    </main>
</div>
