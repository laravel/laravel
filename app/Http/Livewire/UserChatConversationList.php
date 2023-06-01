<?php

namespace App\Http\Livewire;

use Livewire\Component;

class UserChatConversationList extends Component
{
    public $group;
    public $messages = [];
    protected $listeners = [
        'messageSaved' => 'reRenderMessage',
        'messageSent' => 'reRenderMessage',
    ];

    public function mount($group)
    {
        $this->messages = $this->getMessages();
    }

    public function render()
    {
        return view('chat.partials.user-chat.conversation-list');
    }

    public function reRenderMessage($message)
    {
        // array_push($this->messages, $message['message']);
        $this->messages = $this->getMessages();
    }

    protected function getMessages()
    {
        return array_reverse(
            $this->group->messages()
                ->with('user')
                ->latest()
                ->take(10)
                ->get()
                ->toArray());
    }
}
