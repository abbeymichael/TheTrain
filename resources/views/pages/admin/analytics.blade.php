<?php
// Livewire 4 SFC — Admin\AnalyticsPanel
use Livewire\Volt\Component;
use App\Models\Booking;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Trip;

new class extends Component {
    public function with(): array
    {
        $totalRevenue       = Booking::where('status', 'confirmed')->sum('final_price');
        $confirmedBookings  = Booking::where('status', 'confirmed')->count();
        $foodOptOutCount    = Booking::where('status', 'confirmed')->where('opted_out_of_food', true)->count();
        $foodOptOutRate     = $confirmedBookings > 0 ? round($foodOptOutCount / $confirmedBookings * 100, 1) : 0;

        // Challenge distribution (top challenges by booking count)
        $challengeDist = \DB::table('booking_challenges')
            ->join('challenges', 'challenges.id', '=', 'booking_challenges.challenge_id')
            ->join('bookings', 'bookings.id', '=', 'booking_challenges.booking_id')
            ->where('bookings.status', 'confirmed')
            ->selectRaw('challenges.name, COUNT(*) as total')
            ->groupBy('challenges.id', 'challenges.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $maxChallengeDist = $challengeDist->max('total') ?: 1;

        // Monthly booking trend (last 6 months)
        $monthlyTrend = Booking::where('status', 'confirmed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("strftime('%Y-%m', created_at) as month, COUNT(*) as total, SUM(final_price) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'totalRevenue'      => $totalRevenue,
            'confirmedBookings' => $confirmedBookings,
            'totalUsers'        => User::where('role', 'user')->count(),
            'pendingUsers'      => User::where('role', 'user')->where('status', 'pending')->count(),
            'openTrips'         => Trip::where('status', 'open')->count(),
            'totalTrips'        => Trip::count(),
            'foodOptOutCount'   => $foodOptOutCount,
            'foodOptOutRate'    => $foodOptOutRate,
            'challengeDist'     => $challengeDist,
            'maxChallengeDist'  => $maxChallengeDist,
            'monthlyTrend'      => $monthlyTrend,
        ];
    }
}; ?>

<x-layouts.admin>
    <x-slot:title>Analytics</x-slot:title>
    <x-slot:heading>Analytics</x-slot:heading>

    <!-- KPI Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="w-10 h-10 bg-[#c6ebd5] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[#416352] text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">payments</span>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">${{ number_format($totalRevenue, 0) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Total Revenue</p>
        </div>
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="w-10 h-10 bg-[#dbeafe] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-blue-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">confirmation_number</span>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ number_format($confirmedBookings) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Confirmed Bookings</p>
        </div>
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="w-10 h-10 bg-[#fef9c3] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-yellow-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">no_meals</span>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ $foodOptOutRate }}%</p>
            <p class="text-xs text-[#727973] mt-0.5">Food Opt-out Rate ({{ $foodOptOutCount }} bookings)</p>
        </div>
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-5 shadow-sm">
            <div class="w-10 h-10 bg-[#fce7f3] rounded-lg flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-pink-600 text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;">group</span>
            </div>
            <p class="text-2xl font-bold text-[#1b1c1a]">{{ number_format($totalUsers) }}</p>
            <p class="text-xs text-[#727973] mt-0.5">Registered Users <span class="{{ $pendingUsers > 0 ? 'text-amber-600' : '' }}">{{ $pendingUsers > 0 ? '('.$pendingUsers.' pending)' : '' }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Challenge Distribution -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-5">Challenge Category Distribution</h3>
            <p class="text-xs text-[#727973] mb-4">Confirmed bookings by challenge category selected.</p>
            @if ($challengeDist->isEmpty())
                <div class="py-10 text-center">
                    <p class="text-sm text-[#727973]">No booking data yet.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($challengeDist as $row)
                        <div>
                            <div class="flex items-center justify-between mb-1 text-sm">
                                <span class="font-medium text-[#1b1c1a]">{{ $row->name }}</span>
                                <span class="text-[#727973]">{{ $row->total }}</span>
                            </div>
                            <div class="w-full bg-[#f0f1f0] rounded-full h-2">
                                <div class="bg-[#416352] h-2 rounded-full transition-all duration-500"
                                    style="width: {{ round($row->total / $maxChallengeDist * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Monthly Booking Trend -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">Booking Trend (Last 6 Months)</h3>
            <p class="text-xs text-[#727973] mb-5">Confirmed bookings and revenue per month.</p>
            @if ($monthlyTrend->isEmpty())
                <div class="py-10 text-center">
                    <p class="text-sm text-[#727973]">No booking data in the last 6 months.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#e4e7e5]">
                                <th class="text-left py-2 pr-4 text-xs font-semibold text-[#727973] uppercase tracking-wide">Month</th>
                                <th class="text-right py-2 pr-4 text-xs font-semibold text-[#727973] uppercase tracking-wide">Bookings</th>
                                <th class="text-right py-2 text-xs font-semibold text-[#727973] uppercase tracking-wide">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f1f0]">
                            @foreach ($monthlyTrend as $month)
                                <tr class="hover:bg-[#fafbfa] transition-colors">
                                    <td class="py-2.5 pr-4 font-medium text-[#1b1c1a]">{{ \Carbon\Carbon::parse($month->month.'-01')->format('M Y') }}</td>
                                    <td class="py-2.5 pr-4 text-right text-[#414844]">{{ $month->total }}</td>
                                    <td class="py-2.5 text-right font-medium text-[#1b1c1a]">${{ number_format($month->revenue ?? 0, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Trips Overview -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-4">Trips Overview</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-[#f0faf5] rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-[#416352]">{{ $openTrips }}</p>
                    <p class="text-xs text-[#414844] mt-1">Open for Booking</p>
                </div>
                <div class="bg-[#f8f9f8] rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-[#1b1c1a]">{{ $totalTrips }}</p>
                    <p class="text-xs text-[#727973] mt-1">Total Trips</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-[#f0f1f0]">
                <a href="{{ route('admin.trips.create') }}" class="flex items-center gap-2 text-sm font-medium text-[#416352] hover:text-[#2e4a3d] transition-colors">
                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">add_circle</span>
                    Create new trip
                </a>
            </div>
        </div>

        <!-- Food Opt-out Summary -->
        <div class="bg-white rounded-xl border border-[#e4e7e5] p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-[#1b1c1a] mb-1">Food Opt-out Summary</h3>
            <p class="text-xs text-[#727973] mb-5">How many participants opted out of the included meal plan.</p>
            <div class="flex items-center gap-6">
                <div class="relative w-24 h-24 shrink-0">
                    {{-- CSS-only donut --}}
                    <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f0f1f0" stroke-width="3.8" />
                        @php $pct = min(100, $foodOptOutRate); @endphp
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#416352" stroke-width="3.8"
                            stroke-dasharray="{{ $pct }} {{ 100 - $pct }}"
                            stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-base font-bold text-[#416352]">{{ $foodOptOutRate }}%</span>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#416352]"></div>
                        <span class="text-[#414844]">Opted out: <strong>{{ $foodOptOutCount }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#f0f1f0]"></div>
                        <span class="text-[#414844]">Food included: <strong>{{ $confirmedBookings - $foodOptOutCount }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-[#9ba39c]">
                        <div class="w-3 h-3"></div>
                        <span>Total confirmed: {{ $confirmedBookings }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
