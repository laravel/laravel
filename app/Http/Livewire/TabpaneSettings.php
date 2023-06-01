<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class TabpaneSettings extends Component
{
    public array $statuses = [
        User::STATUS_ACTIVE => ['class' => 'text-success', 'name' => 'Active'],
        User::STATUS_AWAY => ['class' => 'text-warning', 'name' => 'Away'],
        User::STATUS_DO_NOT_DISTURB => ['class' => 'text-danger', 'name' => 'Do not disturb'],
        User::STATUS_INVISIBLE => ['class' => 'text-light', 'name' => 'Invisible'],
    ];

    public function mount()
    {
        //
    }

    public function render()
    {
        return view('chat.partials.leftsidebar.tabpane-settings');
    }

    public function setUserStatus($value)
    {
        auth()->user()->status = $value;
        auth()->user()->push();
    }
}
