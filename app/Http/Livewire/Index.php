<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.index')->layout('layouts.app');
    }
}
