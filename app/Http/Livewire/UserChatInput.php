<?php

namespace App\Http\Livewire;

use App\Events\MessageSent;
use App\Models\Message;
use Livewire\Component;

class UserChatInput extends Component
{
    public $group = null;
    public $content = '';

    public function mount($group)
    {
        //
    }

    public function render()
    {
        return view('chat.partials.user-chat.input-form');
    }

    public function sendMessage()
    {
        try {
            $message = Message::create([
                'content'  => $this->content,
                'group_id' => $this->group->id,
                'user_id'  => auth()->id(),
            ]);

            $this->emitTo('user-chat-conversation-list', 'messageSaved', $message);
            $this->reset('content');

            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }
    }
}
