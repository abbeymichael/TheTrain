<?php
// Livewire 4 SFC — User\Dashboard
use Livewire\Volt\Component;
use App\Models\Booking;
use App\Models\Trip;

new class extends Component {
    public function with(): array
    {
        $user = auth()->user();
        return [
            'user'           => $user,
            'upcomingTrips'  => Booking::with('trip')->where('user_id', $user->id)->where('status', 'confirmed')
                ->whereHas('trip', fn ($q) => $q->where('start_date', '>=', now()))
                ->orderBy('created_at', 'desc')->take(3)->get(),
            'totalBookings'  => Booking::where('user_id', $user->id)->where('status', 'confirmed')->count(),
            'openTrips'      => Trip::where('status', 'open')->count(),
        ];
    }
}; ?>

<x-layouts.user>
    <x-slot:title>My Dashboard</x-slot:title>

    <!-- Welcome -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">
            Welcome back, {{ $user->first_name ?? explode(' ', $user->name)[0] }}
        </h1>
        <p class="text-sm text-[#727973]">Here's an overview of your restoration journey.</p>
    </div>

    <!-- Account status notice -->
    @if ($user->status === 'pending')
        <div class="mb-6 flex gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 text-sm text-amber-800">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">schedule</span>
            <div>
                <p class="font-semibold mb-0.5">Account pending approval</p>
                <p>Your account is under a quick admin review. You'll receive an email once approved, after which you can book trips.</p>
            </div>
        </div>
    @elseif ($user->status === 'rejected')
        <div class="mb-6 flex gap-3 bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">block</span>
            <p>Your account application was not approved. Please contact support for more information.</p>
        </div>
    @elseif ($user->status === 'suspended')
        <div class="mb-6 flex gap-3 bg-orange-50 border border-orange-200 rounded-xl px-5 py-4 text-sm text-orange-800">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">pause_circle</span>
            <p>Your account is currently suspended. Please contact support to reinstate your account.</p>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="w-10 h-10 bg-[#c6ebd5] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">confirmation_number</span>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ $totalBookings }}</p>
            <p class="text-xs text-[#727973]">Trips Booked</p>
        </div>
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="w-10 h-10 bg-[#dbeafe] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-blue-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">explore</span>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ $openTrips }}</p>
            <p class="text-xs text-[#727973]">Trips Available</p>
        </div>
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm col-span-2 md:col-span-1">
            <div class="w-10 h-10 bg-[#fce7f3] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-pink-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">person</span>
            </div>
            <p class="text-sm font-semibold text-[#1b1c1a]">{{ ucfirst($user->status) }}</p>
            <p class="text-xs text-[#727973]">Account Status</p>
        </div>
    </div>

    <!-- Upcoming trips -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e4e7e5]">
            <h2 class="text-sm font-semibold text-[#1b1c1a]">Your Upcoming Trips</h2>
            <a href="{{ route('user.trips') }}" class="text-xs text-[#416352] font-medium hover:text-[#2e4a3d] transition-colors">All trips →</a>
        </div>
        @if ($upcomingTrips->isEmpty())
            <div class="py-12 text-center">
                <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">train</span>
                <p class="text-sm text-[#727973] mb-3">No upcoming trips yet.</p>
                @if ($user->status === 'approved')
                    <a href="{{ route('trips') }}" class="inline-flex items-center gap-1.5 text-sm font-medium bg-[#416352] text-white px-4 py-2 rounded-lg hover:bg-[#2e4a3d] transition-colors">
                        Browse Trips
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
                    </a>
                @endif
            </div>
        @else
            <div class="divide-y divide-[#f0f1f0]">
                @foreach ($upcomingTrips as $booking)
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-[#fafbfa] transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-[#f0faf5] flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">train</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-[#1b1c1a] truncate">{{ $booking->trip?->title }}</p>
                            <p class="text-xs text-[#727973]">{{ $booking->trip?->start_date?->format('d M Y') }} · {{ $booking->trip?->city }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-semibold text-[#1b1c1a]">${{ number_format($booking->final_price ?? 0, 2) }}</p>
                            <a href="{{ route('user.trip.details', $booking->trip_id) }}" class="text-xs text-[#416352] hover:underline">Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- CTA: Browse trips -->
    @if ($user->status === 'approved')
        <div class="bg-[#416352] rounded-xl p-6 flex flex-col md:flex-row items-start md:items-center gap-4">
            <div class="flex-1">
                <h3 class="text-base font-semibold text-white mb-1">Ready for your next retreat?</h3>
                <p class="text-sm text-white/80">Browse our upcoming schedule and find your next restorative experience.</p>
            </div>
            <a href="{{ route('trips') }}" class="bg-white text-[#416352] text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-[#f0faf5] transition-colors shrink-0 flex items-center gap-2">
                Browse Trips
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
            </a>
        </div>
    @endif
</x-layouts.user>
