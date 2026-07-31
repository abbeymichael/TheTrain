<div>
    {{-- Hero Section --}}
    <header class="relative pt-32 pb-20 md:pt-48 md:pb-40 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-r from-surface via-surface/60 to-transparent z-10"></div>
            <img
                src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80"
                alt="Misty morning mountains reflecting in a still lake at sunrise"
                class="w-full h-full object-cover"
            >
        </div>

        <div class="max-w-7xl mx-auto px-container-margin relative z-20">
            <div class="max-w-2xl">
                <span class="inline-block font-label-md text-label-md text-primary tracking-widest bg-primary-fixed px-3 py-1 rounded-full mb-md">RESTORATIVE RETREATS</span>
                <h1 class="font-display text-display-lg mb-lg leading-tight text-on-surface">
                    Your Journey to <br><span class="italic text-primary-container">Restoration</span> Starts Here
                </h1>
                <p class="font-body text-body-lg text-on-surface-variant mb-xl max-w-lg">
                    Discover curated support retreats designed to provide emotional safety, steady guidance, and a supportive community for your personal growth.
                </p>
                <div class="flex flex-col sm:flex-row gap-md">
                    <a href="{{ route('trips') }}" class="bg-primary text-on-primary px-xl py-lg rounded-xl font-label-md text-label-md flex items-center justify-center gap-sm hover:translate-y-[-2px] transition-all custom-shadow">
                        Browse Upcoming Trips
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="#how" class="bg-surface border border-outline text-on-surface px-xl py-lg rounded-xl font-label-md text-label-md flex items-center justify-center gap-sm hover:bg-surface-container-low transition-all">
                        How it Works
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Features Section: Bento Grid --}}
    <section class="py-xl bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-container-margin">
            <div class="text-center mb-xl">
                <h2 class="font-display text-headline-lg mb-md text-on-surface">Built for Your Wellbeing</h2>
                <p class="font-body text-body-md text-on-surface-variant max-w-xl mx-auto">We prioritize your emotional safety with intentional spaces and expert care.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-lg">
                {{-- Feature 1 --}}
                <div class="md:col-span-8 bg-surface p-xl rounded-xl border border-outline-variant/30 custom-shadow group">
                    <div class="flex flex-col h-full">
                        <div class="bg-primary-fixed w-14 h-14 rounded-full flex items-center justify-center mb-lg group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-3xl">psychology</span>
                        </div>
                        <h3 class="font-display text-headline-md mb-md">A Supportive Environment</h3>
                        <p class="font-body text-body-md text-on-surface-variant mb-lg max-w-lg">
                            Every aspect of our retreats is curated to feel like a deep breath. From the serene locations to the paced activities, we create a sanctuary where you can truly let go and focus on healing.
                        </p>
                        <div class="mt-auto h-64 w-full rounded-lg overflow-hidden">
                            <img
                                src="https://images.unsplash.com/photo-1518005052357-e9871951f3a2?auto=format&fit=crop&w=1200&q=80"
                                alt="Minimalist meditation room with large windows overlooking a forest"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="md:col-span-4 bg-primary text-on-primary p-xl rounded-xl border border-primary/20 custom-shadow flex flex-col justify-between overflow-hidden relative">
                    <div class="relative z-10">
                        <div class="bg-on-primary/10 w-14 h-14 rounded-full flex items-center justify-center mb-lg">
                            <span class="material-symbols-outlined text-on-primary text-3xl" style="font-variation-settings: 'FILL' 1;">groups</span>
                        </div>
                        <h3 class="font-display text-headline-md mb-md text-on-primary">Matched Specialists</h3>
                        <p class="font-body text-body-md opacity-90">
                            Receive guidance from professionals specifically matched to your unique journey and needs.
                        </p>
                    </div>
                    <div class="mt-xl relative z-10">
                        <ul class="space-y-md">
                            <li class="flex items-center gap-md bg-on-primary/5 p-md rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-primary-fixed-dim shrink-0"></div>
                                <span class="font-label-md">Certified Facilitators</span>
                            </li>
                            <li class="flex items-center gap-md bg-on-primary/5 p-md rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-secondary-fixed shrink-0"></div>
                                <span class="font-label-md">Wellness Experts</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="md:col-span-12 bg-surface p-xl rounded-xl border border-outline-variant/30 custom-shadow flex flex-col md:flex-row items-center gap-xl">
                    <div class="flex-1">
                        <div class="bg-tertiary-fixed w-14 h-14 rounded-full flex items-center justify-center mb-lg">
                            <span class="material-symbols-outlined text-tertiary text-3xl">spa</span>
                        </div>
                        <h3 class="font-display text-headline-md mb-md">All-Inclusive Care</h3>
                        <p class="font-body text-body-md text-on-surface-variant">
                            We handle every logistical detail so you can remain fully present in your restoration process. From organic meals to comfortable accommodations and local transport, your focus remains entirely on your growth and community.
                        </p>
                    </div>
                    <div class="flex-1 w-full grid grid-cols-2 gap-md">
                        <div class="h-40 bg-surface-container-high rounded-lg overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=600&q=80" alt="Beautifully prepared organic meal" class="w-full h-full object-cover">
                        </div>
                        <div class="h-40 bg-surface-container-high rounded-lg overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1522771753035-641ab7d9c28c?auto=format&fit=crop&w=600&q=80" alt="Tranquil bedroom setup in a luxury retreat" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it Works Section --}}
    <section class="py-xl" id="how">
        <div class="max-w-7xl mx-auto px-container-margin">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-xl items-center">
                <div>
                    <h2 class="font-display text-headline-lg mb-lg">How Your Journey Unfolds</h2>
                    <div class="space-y-xl">
                        <div class="flex gap-lg">
                            <div class="shrink-0 w-12 h-12 rounded-full border-2 border-primary flex items-center justify-center font-display text-headline-md text-primary">1</div>
                            <div>
                                <h4 class="font-display text-headline-md mb-sm">Find a Trip</h4>
                                <p class="text-on-surface-variant">Browse our curated selection of upcoming retreats based on theme, location, or support focus.</p>
                            </div>
                        </div>
                        <div class="flex gap-lg">
                            <div class="shrink-0 w-12 h-12 rounded-full border-2 border-primary flex items-center justify-center font-display text-headline-md text-primary">2</div>
                            <div>
                                <h4 class="font-display text-headline-md mb-sm">Register &amp; Approve</h4>
                                <p class="text-on-surface-variant">Create your free account and complete our lightweight trust-and-safety review.</p>
                            </div>
                        </div>
                        <div class="flex gap-lg">
                            <div class="shrink-0 w-12 h-12 rounded-full border-2 border-primary flex items-center justify-center font-display text-headline-md text-primary">3</div>
                            <div>
                                <h4 class="font-display text-headline-md mb-sm">Book &amp; Heal</h4>
                                <p class="text-on-surface-variant">Select your challenge track, choose your meal preference, and finalize your spot through Stripe.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-surface-container-highest rounded-3xl overflow-hidden aspect-square border border-outline-variant/30 custom-shadow">
                        <img src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=800&q=80" alt="Two people walking slowly along a winding forest path" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-surface p-lg rounded-xl custom-shadow border border-outline-variant/50 max-w-xs animate-bounce" style="animation-duration: 3s">
                        <div class="flex items-center gap-md mb-sm">
                            <span class="material-symbols-outlined text-primary">check_circle</span>
                            <span class="font-label-md text-primary">Support Track Active</span>
                        </div>
                        <div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
                            <div class="bg-primary w-2/3 h-full rounded-full"></div>
                        </div>
                        <p class="text-[12px] mt-xs text-on-surface-variant text-right">Step 2 of 3</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    <section class="py-xl bg-primary-fixed">
        <div class="max-w-7xl mx-auto px-container-margin">
            <div class="text-center mb-xl">
                <span class="material-symbols-outlined text-primary text-5xl opacity-30 mb-md" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                <h2 class="font-display text-headline-lg text-on-primary-fixed">Voices of Healing</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                <div class="bg-surface p-xl rounded-xl custom-shadow border border-outline-variant/30">
                    <p class="font-body text-body-md italic text-on-surface mb-lg">"I came to TheTrain feeling completely burnt out and lost. The quiet presence of the facilitators and the intentional community gave me the space I needed to find my breath again."</p>
                    <div class="flex items-center gap-md">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80" alt="Sarah J." class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-label-md text-primary">Sarah J.</p>
                            <p class="text-[12px] text-on-surface-variant">Coastal Retreat Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-surface p-xl rounded-xl custom-shadow border border-outline-variant/30">
                    <p class="font-body text-body-md italic text-on-surface mb-lg">"Everything was handled for me. For the first time in years, I didn't have to be 'the person in charge.' The restoration I found here was deep and lasting."</p>
                    <div class="flex items-center gap-md">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=100&q=80" alt="Marcus L." class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-label-md text-primary">Marcus L.</p>
                            <p class="text-[12px] text-on-surface-variant">Mountain Cabin Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-surface p-xl rounded-xl custom-shadow border border-outline-variant/30">
                    <p class="font-body text-body-md italic text-on-surface mb-lg">"The community aspect was surprisingly powerful. Meeting others on a similar track helped me realize I wasn't alone. It truly felt like a sanctuary."</p>
                    <div class="flex items-center gap-md">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=80" alt="Elena T." class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-label-md text-primary">Elena T.</p>
                            <p class="text-[12px] text-on-surface-variant">Spring Equinox Participant</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Final Call to Action --}}
    <section class="py-xl relative">
        <div class="max-w-4xl mx-auto px-container-margin text-center">
            <h2 class="font-display text-display-lg mb-lg text-on-surface">Ready to take the first step?</h2>
            <p class="font-body text-body-lg text-on-surface-variant mb-xl">
                Join our next cohort of seekers and begin your restoration journey today. Our specialists are waiting to welcome you.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-md">
                <a href="{{ route('register') }}" class="bg-primary text-on-primary px-xl py-lg rounded-xl font-label-md text-label-md hover:scale-[1.02] transition-transform custom-shadow">
                    Register for a Retreat
                </a>
                <a href="{{ route('trips') }}" class="bg-surface border border-outline text-on-surface px-xl py-lg rounded-xl font-label-md text-label-md hover:bg-surface-container-low transition-colors">
                    Browse Trips
                </a>
            </div>
        </div>
    </section>
</div>
