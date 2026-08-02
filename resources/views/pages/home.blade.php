<?php
// Livewire 4 SFC — Public\HomePage
// No PHP class body needed for a purely presentational page
?>

<x-layouts.public>
    <x-slot:title>TheTrain | Your Journey to Restoration</x-slot:title>

    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-[#fbf9f6]/80 backdrop-blur-md border-b border-[#c1c8c2]/30 shadow-sm transition-all duration-300" id="main-nav">
        <div class="flex justify-between items-center w-full px-4 md:px-8 py-4 max-w-7xl mx-auto">
            <a class="text-[32px] leading-[40px] font-semibold text-[#416352]" style="font-family:'Source Serif 4',serif;" href="{{ route('home') }}">TheTrain</a>
            <div class="hidden md:flex items-center space-x-6">
                <a class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#416352] border-b-2 border-[#416352] pb-1 transition-colors duration-300" href="{{ route('trips') }}">Browse Trips</a>
                <a class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#how">How it Works</a>
                <a class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#about">About</a>
            </div>
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="hidden md:block text-sm leading-5 font-semibold tracking-[0.01em] text-[#414844] hover:text-[#416352] transition-colors duration-300">Login</a>
                    <a href="{{ route('register') }}" class="bg-[#416352] text-white px-6 py-2 rounded-full text-sm leading-5 font-semibold tracking-[0.01em] hover:opacity-90 active:scale-[0.98] transition-all shadow-md">Register</a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="bg-[#416352] text-white px-6 py-2 rounded-full text-sm leading-5 font-semibold tracking-[0.01em] hover:opacity-90 active:scale-[0.98] transition-all shadow-md">My Account</a>
                @endguest
                <button class="md:hidden flex items-center justify-center p-2 text-[#414844] hover:text-[#416352] transition-colors" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">menu</span>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-[#fbf9f6] border-t border-[#c1c8c2]/30 px-4 py-4 space-y-3">
            <a class="block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors" href="{{ route('trips') }}">Browse Trips</a>
            <a class="block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors" href="#how">How it Works</a>
            @guest
                <a class="block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors" href="{{ route('login') }}">Login</a>
                <a class="block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors" href="{{ route('register') }}">Register</a>
            @else
                <a class="block text-sm font-semibold text-[#414844] hover:text-[#416352] transition-colors" href="{{ route('user.dashboard') }}">My Account</a>
            @endguest
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 md:pt-48 md:pb-40 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-r from-[#fbf9f6] via-[#fbf9f6]/60 to-transparent z-10"></div>
            <img class="w-full h-full object-cover"
                alt="A cinematic, panoramic landscape of misty morning mountains reflecting in a still lake at sunrise."
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuA83OPeJy0hl2K6WVDX-bTOwnQKQLzzinehTcwTsB0Z97SVjW3vbE9eHJPcJmM5DFBpqF6Z2eOjDauxj6H3bl00ydjZtz2Byt_KKn9DaAIdc2Nz5E41TZ3cglKx9y-YwnhkbiDCC_kbg0NIofWALvxmJYT7x2ucEm_l4rDDHtRTcnVv6iRwds-wcoCZthDAfitI4DaiWfz-7dAsF5AkZ7rKxmlyLCSPBIuXbAmylEYNDGqZxwA_rphgog" />
        </div>
        <div class="max-w-7xl mx-auto px-4 md:px-8 relative z-20">
            <div class="max-w-2xl">
                <span class="inline-block text-sm leading-5 font-semibold tracking-[0.01em] tracking-widest text-[#416352] bg-[#c6ebd5] px-3 py-1 rounded-full mb-4">RESTORATIVE RETREATS</span>
                <h1 class="text-4xl md:text-5xl lg:text-[48px] lg:leading-[56px] font-semibold lg:tracking-[-0.02em] mb-6 leading-tight text-[#1b1c1a]" style="font-family:'Source Serif 4',serif;">
                    Your Journey to <br /><span class="italic text-[#5a7c6a]">Restoration</span> Starts Here
                </h1>
                <p class="text-base md:text-lg text-[#414844] mb-10 max-w-lg">
                    Discover curated support retreats designed to provide emotional safety, steady guidance, and a supportive community for your personal growth.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a class="bg-[#416352] text-white px-10 py-6 rounded-xl text-sm font-semibold tracking-[0.01em] flex items-center justify-center gap-2 hover:-translate-y-1 hover:shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] transition-all duration-300 shadow-[0_4px_14px_0_rgba(0,0,0,0.1)]" href="{{ route('trips') }}">
                        Browse Upcoming Trips
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">arrow_forward</span>
                    </a>
                    <a class="bg-[#fbf9f6] border border-[#727973] text-[#1b1c1a] px-10 py-6 rounded-xl text-sm font-semibold tracking-[0.01em] flex items-center justify-center gap-2 hover:bg-[#f5f3f0] transition-all duration-300" href="#how">
                        How it Works
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section: Bento Grid -->
    <section class="py-10 bg-[#f5f3f0]">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-[32px] md:leading-[40px] font-semibold mb-4 text-[#1b1c1a]" style="font-family:'Source Serif 4',serif;">Built for Your Wellbeing</h2>
                <p class="text-base text-[#414844] max-w-xl mx-auto">We prioritize your emotional safety with intentional spaces and expert care.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Feature 1 -->
                <div class="lg:col-span-8 bg-[#fbf9f6] p-10 rounded-xl border border-[#c1c8c2]/30 hover:-translate-y-1 transition-transform duration-300 shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] group">
                    <div class="flex flex-col h-full">
                        <div class="bg-[#c6ebd5] w-14 h-14 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[#416352] text-3xl" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">psychology</span>
                        </div>
                        <h3 class="text-2xl md:text-[24px] md:leading-[32px] font-medium mb-4" style="font-family:'Source Serif 4',serif;">A Supportive Environment</h3>
                        <p class="text-base text-[#414844] mb-6 max-w-lg">
                            Every aspect of our retreats is curated to feel like a deep breath. From the serene locations to the paced activities, we create a sanctuary where you can truly let go and focus on healing.
                        </p>
                        <div class="mt-auto h-48 md:h-64 w-full rounded-lg overflow-hidden">
                            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                alt="An interior view of a minimalist meditation room with large windows overlooking a lush forest."
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXVtFMXFp-ZacjYTWK02yuj0Y97ATd_k9PBUBIIJM1vSJf0r4o50Kz0_PJ9Z8APyo_dUfz7JJ0cfxHtcY51kTYqGasHZQpePGQILe1eJlU_aWTEk7uXECFkImFyQZcd1IL7t8Yg7aSyA0DOGOE1peRxWhrApVqk3gEl54eNAjZgHPuDB3O6U-HkEHTEFsy7YKN3kx23WLMuK6coo6vwzQ6WLnafxZ7NHSfYqNUffKRy1kFTVik4KryqA" />
                        </div>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="lg:col-span-4 bg-[#416352] text-white p-10 rounded-xl border border-[#416352]/20 shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] hover:-translate-y-1 transition-transform duration-300 flex flex-col justify-between overflow-hidden relative">
                    <div class="relative z-10">
                        <div class="bg-white/10 w-14 h-14 rounded-full flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;">groups</span>
                        </div>
                        <h3 class="text-2xl md:text-[24px] md:leading-[32px] font-medium mb-4 text-white" style="font-family:'Source Serif 4',serif;">Matched Specialists</h3>
                        <p class="text-base opacity-90">Receive guidance from professionals specifically matched to your unique journey and needs.</p>
                    </div>
                    <div class="mt-10 relative z-10">
                        <ul class="space-y-4">
                            <li class="flex items-center gap-4 bg-white/5 p-4 rounded-lg hover:bg-white/10 transition-colors cursor-default">
                                <div class="w-10 h-10 rounded-full bg-[#aacfba] shrink-0"></div>
                                <span class="text-sm leading-5 font-semibold tracking-[0.01em]">Certified Facilitators</span>
                            </li>
                            <li class="flex items-center gap-4 bg-white/5 p-4 rounded-lg hover:bg-white/10 transition-colors cursor-default">
                                <div class="w-10 h-10 rounded-full bg-[#cee5f9] shrink-0"></div>
                                <span class="text-sm leading-5 font-semibold tracking-[0.01em]">Wellness Experts</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="lg:col-span-12 bg-[#fbf9f6] p-10 rounded-xl border border-[#c1c8c2]/30 hover:-translate-y-1 transition-transform duration-300 shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] flex flex-col md:flex-row items-center gap-10 group">
                    <div class="flex-1">
                        <div class="bg-[#ffdbd1] w-14 h-14 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[#884b3b] text-3xl" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">spa</span>
                        </div>
                        <h3 class="text-2xl md:text-[24px] md:leading-[32px] font-medium mb-4" style="font-family:'Source Serif 4',serif;">All-Inclusive Care</h3>
                        <p class="text-base text-[#414844]">We handle every logistical detail so you can remain fully present in your restoration process. From organic meals to comfortable accommodations and local transport, your focus remains entirely on your growth and community.</p>
                    </div>
                    <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="h-40 md:h-48 bg-[#eae8e5] rounded-lg overflow-hidden">
                            <img class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                                alt="A beautifully prepared organic meal on a handmade ceramic plate."
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAVt3lGMmnnc6WfEdA4NlUlijs906ED3p2hg-gAY2RdQqZn0Qt5CWLHpKGjDWPlUBVDKEEnRi_Rw_vhUoWTOfHn5wogoS-1MBYIpZmnj6Gv1sho39HhtSakkCHF94p3y7EYE4-BnzoqWFsav-zJEJ42ci5bhpowvnc3ilpHTmooJFhKTJdbwa7-RiuWL9KlN3mS536j2gn6Hs_wiYLahScJq6Tn2gm1FXeeovNzaRI-0sw_uO1PGzJ9FA" />
                        </div>
                        <div class="h-40 md:h-48 bg-[#eae8e5] rounded-lg overflow-hidden">
                            <img class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                                alt="A tranquil bedroom setup in a luxury retreat."
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmxWIniH0EV3yC-Dl_tnFOeUsxQ8EM4CpV7mVmUVxHqLfkGSGur-GN7Q6orcpsYsLDCOaz4_DGyAxqR1BdhGBVSoW982l2c_Yl_ZwHxMA-FTP2ccC0NApbvnNIcuPLoeAPETCX1GQt3RFGUFGwa44IczWQmOC0VNzUtDnj1hdJVujsnNmowihMe0rpH2fvEUrwL6g64lYampFCMmzGwg6vhvyGfQC8hC8nGbL4qIIIrDIAo8m4Xo742A" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section class="py-10" id="how">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-3xl md:text-[32px] md:leading-[40px] font-semibold mb-6" style="font-family:'Source Serif 4',serif;">How Your Journey Unfolds</h2>
                    <div class="space-y-10">
                        <div class="flex gap-6 group cursor-default">
                            <div class="shrink-0 w-12 h-12 rounded-full border-2 border-[#416352] flex items-center justify-center text-[24px] leading-[32px] font-medium text-[#416352] group-hover:bg-[#416352] group-hover:text-white transition-colors duration-300" style="font-family:'Source Serif 4',serif;">1</div>
                            <div>
                                <h4 class="text-xl md:text-[24px] md:leading-[32px] font-medium mb-2" style="font-family:'Source Serif 4',serif;">Find a Trip</h4>
                                <p class="text-[#414844]">Browse our curated selection of upcoming retreats based on theme, location, or support focus.</p>
                            </div>
                        </div>
                        <div class="flex gap-6 group cursor-default">
                            <div class="shrink-0 w-12 h-12 rounded-full border-2 border-[#416352] flex items-center justify-center text-[24px] leading-[32px] font-medium text-[#416352] group-hover:bg-[#416352] group-hover:text-white transition-colors duration-300" style="font-family:'Source Serif 4',serif;">2</div>
                            <div>
                                <h4 class="text-xl md:text-[24px] md:leading-[32px] font-medium mb-2" style="font-family:'Source Serif 4',serif;">Register &amp; Approve</h4>
                                <p class="text-[#414844]">Complete a brief orientation to ensure the retreat is the right match for your current needs.</p>
                            </div>
                        </div>
                        <div class="flex gap-6 group cursor-default">
                            <div class="shrink-0 w-12 h-12 rounded-full border-2 border-[#416352] flex items-center justify-center text-[24px] leading-[32px] font-medium text-[#416352] group-hover:bg-[#416352] group-hover:text-white transition-colors duration-300" style="font-family:'Source Serif 4',serif;">3</div>
                            <div>
                                <h4 class="text-xl md:text-[24px] md:leading-[32px] font-medium mb-2" style="font-family:'Source Serif 4',serif;">Book &amp; Heal</h4>
                                <p class="text-[#414844]">Finalize your spot and begin preparing for your guided transition into restoration.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative mt-8 md:mt-0">
                    <div class="bg-[#e4e2df] rounded-3xl overflow-hidden aspect-square border border-[#c1c8c2]/30 shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)]">
                        <img class="w-full h-full object-cover"
                            alt="Two people walking along a winding forest path in autumn."
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCpERke5Gc693VADOnEv5E2TaI9W8x1moSog6OwIbycaTvQRTYw1e_9ak6U0_6RSWkVfCI5KyPEA13vu-zD6MxCAA_OFfTgXDgaP_HIU0RW48XtR72IXDCCiYefYCDVCcQkZs_gDUqiQ9m2jl6XqVDyUA09eU4Vw8qxER7zXmm0JbEoCCb8IXKZ5GJDDuzplpYkyviFN_8KY6LhsiU9bwfDdDCbtC3RvVnvEVBSJEXPc3o3AJJ4Xa5eVw" />
                    </div>
                    <div class="absolute -bottom-6 -left-2 md:-left-6 bg-[#fbf9f6] p-6 rounded-xl shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] border border-[#c1c8c2]/50 w-64 md:max-w-xs animate-[bounce_3s_infinite]">
                        <div class="flex items-center gap-4 mb-2">
                            <span class="material-symbols-outlined text-[#416352]" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">fitbit_check_small</span>
                            <span class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#416352]">Support Track Active</span>
                        </div>
                        <div class="w-full bg-[#eae8e5] h-2 rounded-full overflow-hidden">
                            <div class="bg-[#416352] w-2/3 h-full rounded-full"></div>
                        </div>
                        <p class="text-[12px] mt-1 text-[#414844] text-right">Step 2 of 3</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-10 bg-[#c6ebd5]">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="text-center mb-10">
                <span class="material-symbols-outlined text-[#416352] text-5xl opacity-30 mb-4" style="font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;">format_quote</span>
                <h2 class="text-3xl md:text-[32px] md:leading-[40px] font-semibold text-[#002114]" style="font-family:'Source Serif 4',serif;">Voices of Healing</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#fbf9f6] p-10 rounded-xl shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] border border-[#c1c8c2]/30 hover:-translate-y-1 transition-transform duration-300">
                    <p class="text-base italic text-[#1b1c1a] mb-6">"I came to TheTrain feeling completely burnt out and lost. The quiet presence of the facilitators and the intentional community gave me the space I needed to find my breath again."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#eae8e5] overflow-hidden">
                            <img class="w-full h-full object-cover" alt="Sarah J." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAiXOeT30-m0opRgHP-B1U3eGv0oMoFzyWZGYJemxAUWKAAwTlXJip4i_AdoA3tPzEuVfT5w1a4Qw_HqPLGNjIj_nE2fgAp-rCutTwI-1DftOSzaT4eDtyHlitryK9zLfkK-8RSRSb1UDwjwmS6I36dNZs12UtOnNOnWag1U3zJqdcqVIMizAmeeze6IVUT2PjsMOU0Xocmimjfz2BFDcn-7Q04b5pmzMDs60RPRPJTxclmVW3FATc49w" />
                        </div>
                        <div>
                            <p class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#416352]">Sarah J.</p>
                            <p class="text-[12px] text-[#414844]">Coastal Retreat Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-[#fbf9f6] p-10 rounded-xl shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] border border-[#c1c8c2]/30 hover:-translate-y-1 transition-transform duration-300">
                    <p class="text-base italic text-[#1b1c1a] mb-6">"Everything was handled for me. For the first time in years, I didn't have to be 'the person in charge.' The restoration I found here was deep and lasting."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#eae8e5] overflow-hidden">
                            <img class="w-full h-full object-cover" alt="Marcus L." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDXHhfenh1CdETAssSP2ATqzPCBPqtsAO3IpDAkAAsaNwRbevSmLAgXKEKPvnvmXUxGOJ-RKKq-3aTwyFkFhoLy9bkyD-0Mf0xJf0Lh3EFmw3pLtCEl6s0-U-82TLqjoqPqY5Dxww98XdrpJLrQUKjhufFwI-CyFwx5Z317Wu-jIR3QeQTNJ-yykZocKD8FIn2Ab7SD6a-LdH2Zrpzk6tzdKdJKlYlEnTdTdTpjaoJOf3ehhtcSJ7flNA" />
                        </div>
                        <div>
                            <p class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#416352]">Marcus L.</p>
                            <p class="text-[12px] text-[#414844]">Mountain Cabin Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-[#fbf9f6] p-10 rounded-xl shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)] border border-[#c1c8c2]/30 hover:-translate-y-1 transition-transform duration-300">
                    <p class="text-base italic text-[#1b1c1a] mb-6">"The community aspect was surprisingly powerful. Meeting others on a similar track helped me realize I wasn't alone. It truly felt like a sanctuary."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#eae8e5] overflow-hidden">
                            <img class="w-full h-full object-cover" alt="Elena T." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCRNAscSstUcWwSUvSb2dXdYXSX55Amj-GK1TXL4wJR-nPz-nUYNN25wzakoj6_ZgdG1UtbP9_V7Z5aytSd3QsYWMCNKDl7767axveXRGpnydsQiYLxe6jLJQNdqbxqzul1trhv4CHXgc6R_5-jhBZ7__q1V8jlUqQCC6fNx7y3z4Xfc8fqatzF6q3vRvBpSFrqE75i5yekF2dun7LOpRvowCP3Xbeq8Yi5SPu3f1kWTQMgZacOxJQPNw" />
                        </div>
                        <div>
                            <p class="text-sm leading-5 font-semibold tracking-[0.01em] text-[#416352]">Elena T.</p>
                            <p class="text-[12px] text-[#414844]">Spring Equinox Participant</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-10 relative" id="about">
        <div class="max-w-4xl mx-auto px-4 md:px-8 text-center">
            <h2 class="text-4xl md:text-[48px] md:leading-[56px] font-semibold mb-6 text-[#1b1c1a]" style="font-family:'Source Serif 4',serif;">Ready to take the first step?</h2>
            <p class="text-base md:text-lg text-[#414844] mb-10">Join our next cohort of seekers and begin your restoration journey today. Our specialists are waiting to welcome you.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-[#416352] text-white px-10 py-6 rounded-xl text-sm font-semibold tracking-[0.01em] hover:scale-[1.02] hover:shadow-lg transition-all duration-300 shadow-[0_10px_30px_-10px_rgba(84,106,123,0.12)]">
                    Register for a Retreat
                </a>
                <a href="{{ route('trips') }}" class="bg-[#fbf9f6] border border-[#727973] text-[#1b1c1a] px-10 py-6 rounded-xl text-sm font-semibold tracking-[0.01em] hover:bg-[#f5f3f0] hover:border-[#416352] transition-all duration-300">
                    Browse Trips
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full mt-10 bg-[#f5f3f0] border-t border-[#c1c8c2]/50">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 px-4 md:px-8 py-10 max-w-7xl mx-auto">
            <div class="col-span-1 sm:col-span-2 md:col-span-1">
                <div class="text-[24px] leading-[32px] font-medium text-[#416352] mb-4" style="font-family:'Source Serif 4',serif;">TheTrain</div>
                <p class="text-[#414844] text-base mb-4">Guided retreats for emotional restoration and community-led growth.</p>
                <div class="flex gap-4">
                    <a class="text-[#416352] hover:text-[#5a7c6a] transition-colors duration-300" href="#"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">share</span></a>
                    <a class="text-[#416352] hover:text-[#5a7c6a] transition-colors duration-300" href="#"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">mail</span></a>
                </div>
            </div>
            <div>
                <h5 class="text-sm leading-5 font-semibold tracking-[0.01em] font-bold mb-4">Quick Links</h5>
                <ul class="space-y-2">
                    <li><a class="text-[#414844] hover:text-[#416352] transition-colors duration-300" href="{{ route('trips') }}">Browse Trips</a></li>
                    <li><a class="text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#how">How it Works</a></li>
                    <li><a class="text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#about">About</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-sm leading-5 font-semibold tracking-[0.01em] font-bold mb-4">Support</h5>
                <ul class="space-y-2">
                    <li><a class="text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#">Privacy Policy</a></li>
                    <li><a class="text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#">Terms of Service</a></li>
                    <li><a class="text-[#414844] hover:text-[#416352] transition-colors duration-300" href="#">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-sm leading-5 font-semibold tracking-[0.01em] font-bold mb-4">Newsletter</h5>
                <p class="text-[12px] text-[#414844] mb-4">Receive gentle reminders and retreat updates.</p>
                <div class="flex gap-1">
                    <input class="bg-white border border-[#c1c8c2] rounded-lg text-sm px-3 py-2 flex-1 focus:ring-2 focus:ring-[#416352] focus:border-[#416352] outline-none transition-shadow duration-300" placeholder="Email address" type="email" />
                    <button class="bg-[#416352] text-white p-2 rounded-lg hover:bg-[#5a7c6a] transition-colors duration-300">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">send</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 md:px-8 py-6 border-t border-[#c1c8c2]/30 text-center">
            <p class="text-[#414844] text-[12px]">© {{ date('Y') }} TheTrain Platform. Your journey to restoration starts here.</p>
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('main-nav');
            if (!nav) return;
            if (window.scrollY > 50) {
                nav.classList.add('py-2');
                nav.classList.remove('py-4');
                nav.classList.add('bg-[#fbf9f6]/95');
            } else {
                nav.classList.add('py-4');
                nav.classList.remove('py-2');
                nav.classList.remove('bg-[#fbf9f6]/95');
            }
        });
    </script>
</x-layouts.public>
