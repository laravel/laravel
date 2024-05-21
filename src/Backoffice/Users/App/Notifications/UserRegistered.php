<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Lightit\Backoffice\Users\Domain\Models\User;

class UserRegistered extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public function __construct()
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())
                    ->line("Welcome $notifiable->name, to our application Example.")
                    ->action('Our web', url('/'))
                    ->line('Thank you for using our application!');
    }
}
