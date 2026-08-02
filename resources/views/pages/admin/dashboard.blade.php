<?php
// Livewire 4 SFC — Admin\Dashboard
use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Trip;
use App\Models\Booking;

new class extends Component {
    public function with(): array
    {
        return [
            'totalUsers'         => User::where('role', 'user')->count(),
            'pendingUsers'       => User::where('role', 'user')->where('status', 'pending')->count(),
            'totalSpecialists'   => User::where('role', 'specialist')->count(),
            'totalTrips'         => Trip::count(),
            'upcomingTrips'      => Trip::where('status', 'open')->count(),
            'totalBookings'      => Booking::count(),
            'confirmedBookings'  => Booking::where('status', 'confirmed')->count(),
            'recentBookings'     => Booking::with(['user', 'trip'])->latest()->take(8)->get(),
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:heading>Dashboard</x-slot:heading>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Users -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-[#c6ebd5] rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">group</span>
                </div>
                @if ($pendingUsers > 0)
                    <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $pendingUsers }} pending</span>
                @endif
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ number_format($totalUsers) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Registered Users</p>
        </div>

        <!-- Specialists -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-[#dbeafe] rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">psychology</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ number_format($totalSpecialists) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Specialists</p>
        </div>

        <!-- Trips -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-[#fce7f3] rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-pink-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">train</span>
                </div>
                @if ($upcomingTrips > 0)
                    <span class="text-xs font-semibold bg-[#c6ebd5] text-[#2e4a3d] px-2 py-0.5 rounded-full">{{ $upcomingTrips }} open</span>
                @endif
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ number_format($totalTrips) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Total Trips</p>
        </div>

        <!-- Bookings -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-[#fef9c3] rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">confirmation_number</span>
                </div>
                @if ($confirmedBookings > 0)
                    <span class="text-xs font-semibold bg-[#c6ebd5] text-[#2e4a3d] px-2 py-0.5 rounded-full">{{ $confirmedBookings }} confirmed</span>
                @endif
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ number_format($totalBookings) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Total Bookings</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('admin.trips.create') }}" class="flex items-center gap-4 bg-[#416352] text-white p-5 rounded-xl hover:bg-[#2e4a3d] transition-colors group shadow-sm">
            <div class="w-10 h-10 bg-white/15 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-white/25 transition-colors">
                <span class="material-symbols-outlined text-white text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">add</span>
            </div>
            <div>
                <p class="text-sm font-semibold">Create New Trip</p>
                <p class="text-xs text-white/70">Schedule a retreat</p>
            </div>
        </a>
        <a href="{{ route('admin.users') }}" class="flex items-center gap-4 bg-white border border-[#e4e7e5] p-5 rounded-xl hover:border-[#416352] transition-colors group shadow-sm">
            <div class="w-10 h-10 bg-[#f0faf5] rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">person_check</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#1b1c1a]">Review Users</p>
                <p class="text-xs text-[#727973]">Approve pending accounts</p>
            </div>
        </a>
        <a href="{{ route('admin.challenges') }}" class="flex items-center gap-4 bg-white border border-[#e4e7e5] p-5 rounded-xl hover:border-[#416352] transition-colors group shadow-sm">
            <div class="w-10 h-10 bg-[#f0faf5] rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">favorite</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#1b1c1a]">Manage Challenges</p>
                <p class="text-xs text-[#727973]">Edit challenge categories</p>
            </div>
        </a>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-white rounded-xl border border-[#e4e7e5] shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e4e7e5]">
            <h2 class="text-sm font-semibold text-[#1b1c1a]">Recent Bookings</h2>
            <a href="{{ route('admin.trips') }}" class="text-xs text-[#416352] font-medium hover:text-[#2e4a3d] transition-colors">View all trips →</a>
        </div>

        @if ($recentBookings->isEmpty())
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-[#c1c8c2] text-5xl block mb-3" style="font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 48;">confirmation_number</span>
                <p class="text-sm text-[#727973]">No bookings yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8f9f8] border-b border-[#e4e7e5]">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">User</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Trip</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Amount</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-[#727973] uppercase tracking-wide">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f1f0]">
                        @foreach ($recentBookings as $booking)
                            <tr class="hover:bg-[#fafbfa] transition-colors">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-[#c6ebd5] flex items-center justify-center text-[#416352] text-xs font-semibold shrink-0">
                                            {{ substr($booking->user?->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-[#1b1c1a]">{{ $booking->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-[#414844]">{{ $booking->trip?->title ?? '—' }}</td>
                                <td class="px-6 py-3.5">
                                    @php
                                        $color = match($booking->status) {
                                            'confirmed'       => 'bg-[#c6ebd5] text-[#2e4a3d]',
                                            'pending_payment' => 'bg-amber-100 text-amber-700',
                                            'cancelled'       => 'bg-red-100 text-red-700',
                                            default           => 'bg-[#f0f1f0] text-[#727973]',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                        {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-[#1b1c1a]">${{ number_format($booking->final_price ?? 0, 2) }}</td>
                                <td class="px-6 py-3.5 text-[#727973]">{{ $booking->created_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
