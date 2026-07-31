<?php

namespace App\Livewire\Public;

use Livewire\Component;

/**
 * Public marketing landing page (agent.md Section 8, /).
 */
class HomePage extends Component
{
    public function render()
    {
        return view('livewire.public.home-page')
            ->layout('layouts.public', [
                'title' => 'Your Journey to Restoration',
            ]);
    }
}
