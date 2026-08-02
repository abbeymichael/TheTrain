<?php

namespace App\Livewire\Public;

use App\Models\Trip;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Public single trip detail page (agent.md Section 8, /trips/{id}).
 */
class TripShow extends Component
{
    public Trip $trip;

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->loadMissing(['challenges', 'specialists.user', 'specialists.challenges', 'tripSeries']);
    }

    public function render(): View
    {
        return view('livewire.public.trip-show')
            ->layout('layouts.public', [
                'title' => $this->trip->title,
            ]);
    }
}
