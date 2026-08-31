<?php

namespace Illuminate\Notifications\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class NotificationSending
{
    use Queueable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $notifiable  The notifiable entity who received the notification.
     * @param  \Illuminate\Notifications\Notification  $notification  The notification instance.
     * @param  string  $channel  The channel name.
     */
    public function __construct(
        public $notifiable,
        public $notification,
        public $channel,
    ) {
    }
}
