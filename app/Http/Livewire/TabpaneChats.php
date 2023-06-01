<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class TabpaneChats extends Component
{
    public $users = [];

    public function mount()
    {
        $this->users = User::whereKeyNot(auth()->id())
            ->orderBy('name')
            ->select('*')
            ->get();
    }

    public function render()
    {
        return view('chat.partials.leftsidebar.tabpane-chats');
    }
}
