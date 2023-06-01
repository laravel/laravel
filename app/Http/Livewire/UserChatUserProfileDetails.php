<?php

namespace App\Http\Livewire;

use Livewire\Component;

class UserChatUserProfileDetails extends Component
{
    public $user;

    public function mount($user)
    {
        //
    }

    public function render()
    {
        return view('chat.partials.user-chat.user-profile-details');
    }
}
