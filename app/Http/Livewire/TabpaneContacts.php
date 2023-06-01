<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TabpaneContacts extends Component
{
    public $groups = [];

    public function mount()
    {
        $users = User::whereKeyNot(auth()->id())
            ->orderBy('name')
            ->select('*')
            ->addSelect(DB::raw("UPPER(LEFT(name, 1)) as upper_left_name_1"))
            ->get();

        $this->groups = $users->groupBy('upper_left_name_1')
            ->toArray();
    }

    public function render()
    {
        return view('chat.partials.leftsidebar.tabpane-contacts');
    }
}
