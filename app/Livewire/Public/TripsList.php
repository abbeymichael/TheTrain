<?php

namespace App\Livewire\Public;

use App\Models\Challenge;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Public browseable list of upcoming trips (agent.md Section 8, /trips).
 */
class TripsList extends Component
{
    use WithPagination;

    public string $cadence = 'all';
    public string $location = '';
    public array $selectedChallenges = [];

    public function updated($property): void
    {
        if (in_array($property, ['cadence', 'location', 'selectedChallenges'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->cadence = 'all';
        $this->location = '';
        $this->selectedChallenges = [];
        $this->resetPage();
    }

    #[Computed]
    public function trips(): object
    {
        return Trip::query()
            ->open()
            ->upcoming()
            ->when($this->cadence !== 'all', function (Builder $query): void {
                $query->whereHas('tripSeries', function (Builder $seriesQuery): void {
                    $seriesQuery->where('cadence', $this->cadence);
                });
            })
            ->when($this->location !== '', function (Builder $query): void {
                $query->where(function (Builder $locationQuery): void {
                    $locationQuery
                        ->where('city', 'like', '%'.$this->location.'%')
                        ->orWhere('venue', 'like', '%'.$this->location.'%');
                });
            })
            ->when(! empty($this->selectedChallenges), function (Builder $query): void {
                $query->whereHas('challenges', function (Builder $challengeQuery): void {
                    $challengeQuery->whereIn('challenges.id', $this->selectedChallenges);
                });
            })
            ->with(['challenges', 'tripSeries'])
            ->paginate(9);
    }

    #[Computed]
    public function challenges(): Collection
    {
        return Challenge::active()->get();
    }

    public function render()
    {
        return view('livewire.public.trips-list')
            ->layout('layouts.public', [
                'title' => 'Upcoming Trips',
            ]);
    }
}
