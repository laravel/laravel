<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class UserChatContent extends Component
{
    public $group = null;
    public $user = null;
    protected $listeners = ['userSelected' => 'openChat'];

    public function mount()
    {
        //
    }

    public function render()
    {
        return view('chat.partials.user-chat.chat-content');
    }

    public function openChat($user)
    {
        $this->user = $user;

        $this->group = Group::has('users', '=', 2)
            ->whereHas('users', function (Builder $query) {
                $query->where('id', $this->user['id']);
            })
            ->whereHas('users', function (Builder $query) {
                $query->where('id', auth()->id());
            })
            ->first();

        if (! $this->group) {
            $this->group = Group::create(['type' => Group::TYPE_USER]);
            $this->group->users()->sync([auth()->id(), $this->user['id']]);
        }
    }
}
