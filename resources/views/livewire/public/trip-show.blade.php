<div>
    <main class="pt-20">
        {{-- Hero Section --}}
        <section class="relative h-[70vh] w-full overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $trip->cover_image ?: 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1600&q=80' }}')">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-on-surface/60 to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-full px-container-margin py-xl max-w-7xl mx-auto text-on-primary">
                <div class="flex flex-wrap gap-sm mb-md">
                    @foreach ($trip->challenges->take(2) as $challenge)
                        <span class="bg-primary/20 backdrop-blur-md px-md py-xs rounded-full border border-white/20 font-label-md text-label-md">{{ $challenge->name }}</span>
                    @endforeach
                    <span class="bg-primary/20 backdrop-blur-md px-md py-xs rounded-full border border-white/20 font-label-md text-label-md">{{ $trip->start_date->diffInDays($trip->end_date) + 1 }} Days</span>
                </div>
                <h1 class="font-display text-display-lg mb-sm">{{ $trip->title }}</h1>
                <div class="flex flex-wrap items-center gap-lg">
                    <div class="flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                        <span class="font-label-md text-label-md">{{ $trip->start_date->format('M j') }} — {{ $trip->end_date->format('M j, Y') }}</span>
                    </div>
                    <div class="flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                        <span class="font-label-md text-label-md">{{ $trip->city ?: $trip->venue }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pricing & Value Card --}}
        <section class="px-container-margin py-xl max-w-7xl mx-auto -mt-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
                <div class="lg:col-span-2 bg-surface-container-lowest p-lg rounded-xl custom-shadow border border-outline-variant/30">
                    <div class="flex flex-col md:flex-row justify-between gap-lg">
                        <div>
                            <h2 class="font-display text-headline-lg text-primary mb-sm">The All-Inclusive Journey</h2>
                            <p class="text-on-surface-variant max-w-xl">{{ $trip->description ?: 'Every detail of your restoration is handled. From specialized support to comfortable accommodation and tailored nutrition, we ensure your only focus is your well-being.' }}</p>
                        </div>
                        <div class="bg-surface-container-low p-lg rounded-xl flex flex-col items-center justify-center min-w-[200px] border border-outline-variant/20">
                            <span class="text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">Starting at</span>
                            <div class="text-primary font-display text-headline-lg font-bold">${{ number_format($trip->base_price, 0) }}</div>
                            <span class="text-on-surface-variant text-sm text-center mt-xs">All-inclusive fee</span>
                        </div>
                    </div>

                    @if ($trip->food_deduction_type && $trip->food_deduction_value > 0)
                        <div class="mt-lg pt-lg border-t border-outline-variant/30 flex items-start gap-md bg-primary-fixed/20 p-md rounded-lg">
                            <span class="material-symbols-outlined text-primary">restaurant</span>
                            <div>
                                <h4 class="font-label-md text-label-md text-on-primary-fixed mb-xs">Food Opt-out Benefit</h4>
                                <p class="text-sm text-on-primary-fixed-variant leading-relaxed">
                                    Prefer to explore local cuisine or follow a strictly personal dietary plan? Opt-out of the internal meal program to receive a
                                    <strong class="text-primary">
                                        @if ($trip->food_deduction_type === 'flat')
                                            ${{ number_format($trip->food_deduction_value, 0) }} credit
                                        @else
                                            {{ $trip->food_deduction_value }}% credit
                                        @endif
                                    </strong>
                                    toward your trip balance.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-lg grid grid-cols-1 sm:grid-cols-3 gap-md">
                        <div class="bg-surface-container-high p-md rounded-lg">
                            <span class="text-on-surface-variant text-xs uppercase tracking-wide font-bold block mb-xs">Trip</span>
                            <span class="font-label-md text-on-surface">${{ number_format(max(0, $trip->base_price - $trip->accommodation_cost - $trip->feeding_cost), 0) }}</span>
                        </div>
                        <div class="bg-surface-container-high p-md rounded-lg">
                            <span class="text-on-surface-variant text-xs uppercase tracking-wide font-bold block mb-xs">Accommodation</span>
                            <span class="font-label-md text-on-surface">${{ number_format($trip->accommodation_cost, 0) }}</span>
                        </div>
                        <div class="bg-surface-container-high p-md rounded-lg">
                            <span class="text-on-surface-variant text-xs uppercase tracking-wide font-bold block mb-xs">Feeding</span>
                            <span class="font-label-md text-on-surface">${{ number_format($trip->feeding_cost, 0) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-primary p-lg rounded-xl custom-shadow text-on-primary flex flex-col justify-between">
                    <div>
                        <h3 class="font-display text-headline-md mb-md">Ready for Restoration?</h3>
                        <ul class="space-y-md">
                            <li class="flex items-center gap-md">
                                <span class="material-symbols-outlined text-inverse-primary">check_circle</span>
                                <span class="text-sm">Personalized Wellness Roadmap</span>
                            </li>
                            <li class="flex items-center gap-md">
                                <span class="material-symbols-outlined text-inverse-primary">check_circle</span>
                                <span class="text-sm">Private Eco-Suite Stay</span>
                            </li>
                            <li class="flex items-center gap-md">
                                <span class="material-symbols-outlined text-inverse-primary">check_circle</span>
                                <span class="text-sm">24/7 Support Access</span>
                            </li>
                        </ul>
                    </div>
                    @auth
                        <a href="{{ route('user.trips.book', $trip) }}" class="block w-full bg-white text-primary font-label-md text-label-md py-md rounded-xl mt-xl hover:bg-surface-container-low transition-colors font-bold uppercase tracking-wide text-center">
                            Book This Trip
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-white text-primary font-label-md text-label-md py-md rounded-xl mt-xl hover:bg-surface-container-low transition-colors font-bold uppercase tracking-wide text-center">
                            Register to Book
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- Challenge Tracks --}}
        <section class="bg-surface-container-low py-xl">
            <div class="px-container-margin max-w-7xl mx-auto">
                <div class="text-center mb-xl">
                    <h2 class="font-display text-headline-lg text-on-surface mb-xs">Focused Support Tracks</h2>
                    <p class="text-on-surface-variant">Tailored therapeutic paths integrated into your travel experience.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                    @foreach ($trip->challenges as $challenge)
                        <div class="bg-white p-lg rounded-xl border border-outline-variant/30 card-hover flex gap-lg items-start">
                            <div class="bg-secondary-fixed p-md rounded-lg text-secondary">
                                <span class="material-symbols-outlined text-[32px]">psychology</span>
                            </div>
                            <div>
                                <h3 class="font-display text-headline-md text-on-surface mb-sm">{{ $challenge->name }}</h3>
                                <p class="text-on-surface-variant text-sm leading-relaxed mb-md">{{ $challenge->description }}</p>
                                @if ($challenge->is_sensitive)
                                    <span class="inline-flex items-center gap-xs text-xs font-label-md text-tertiary bg-tertiary-fixed px-sm py-xs rounded">
                                        <span class="material-symbols-outlined text-[16px]">shield</span>
                                        Sensitive Track — Handled with Extra Care
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Specialists --}}
        @if ($trip->specialists->isNotEmpty())
            <section class="bg-surface-container-highest/30 py-xl">
                <div class="px-container-margin max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-xl gap-md">
                        <div>
                            <h2 class="font-display text-headline-lg text-on-surface">Expert Guidance</h2>
                            <p class="text-on-surface-variant">Meet the clinical and holistic practitioners assigned to this journey.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                        @foreach ($trip->specialists as $specialist)
                            <div class="bg-white overflow-hidden rounded-xl custom-shadow border border-outline-variant/30">
                                <div class="h-64 bg-surface-container-high overflow-hidden">
                                    <img
                                        src="{{ $specialist->photo_path ?: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80' }}"
                                        alt="{{ $specialist->display_name }}"
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                                <div class="p-lg">
                                    <h3 class="font-display text-headline-md text-on-surface mb-xs">{{ $specialist->display_name }}</h3>
                                    <p class="text-primary font-label-md text-label-md mb-md">{{ $specialist->credentials }}</p>
                                    <p class="text-sm text-on-surface-variant leading-relaxed">{{ $specialist->bio }}</p>
                                    @if ($specialist->pivot->role_note)
                                        <p class="mt-md text-xs font-label-md text-on-surface-variant uppercase tracking-wide">{{ $specialist->pivot->role_note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CTA Bottom --}}
        <section class="py-xl px-container-margin text-center max-w-4xl mx-auto">
            <h2 class="font-display text-display-lg text-on-surface mb-md">Take the first step toward restoration.</h2>
            <p class="text-on-surface-variant text-body-lg mb-xl">Spaces are limited to {{ $trip->capacity }} participants per journey to ensure deep personal attention and safety.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-md">
                @auth
                    <a href="{{ route('user.trips.book', $trip) }}" class="px-xl py-md bg-primary text-on-primary font-label-md text-label-md rounded-xl custom-shadow hover:scale-105 transition-all w-full sm:w-auto uppercase tracking-widest font-bold text-center">
                        Reserve Your Spot
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-xl py-md bg-primary text-on-primary font-label-md text-label-md rounded-xl custom-shadow hover:scale-105 transition-all w-full sm:w-auto uppercase tracking-widest font-bold text-center">
                        Register to Reserve
                    </a>
                @endauth
                <a href="{{ route('trips') }}" class="px-xl py-md bg-transparent border-2 border-outline text-on-surface font-label-md text-label-md rounded-xl hover:bg-surface-container-low transition-all w-full sm:w-auto font-bold uppercase tracking-widest text-center">
                    Browse More Trips
                </a>
            </div>
        </section>
    </main>
</div>
