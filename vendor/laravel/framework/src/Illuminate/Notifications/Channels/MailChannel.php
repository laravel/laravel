<?php

namespace Illuminate\Notifications\Channels;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\Header\TagHeader;

class MailChannel
{
    /**
     * The mailer implementation.
     *
     * @var \Illuminate\Contracts\Mail\Factory
     */
    protected $mailer;

    /**
     * The markdown implementation.
     *
     * @var \Illuminate\Mail\Markdown
     */
    protected $markdown;

    /**
     * Create a new mail channel instance.
     *
     * @param  \Illuminate\Contracts\Mail\Factory  $mailer
     * @param  \Illuminate\Mail\Markdown  $markdown
     */
    public function __construct(MailFactory $mailer, Markdown $markdown)
    {
        $this->mailer = $mailer;
        $this->markdown = $markdown;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \Illuminate\Mail\SentMessage|null
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toMail($notifiable);

        if (! $notifiable->routeNotificationFor('mail', $notification) &&
            ! $message instanceof Mailable) {
            return;
        }

        if ($message instanceof Mailable) {
            return $message->send($this->mailer);
        }

        return $this->mailer->mailer($message->mailer ?? null)->send(
            $this->buildView($message),
            array_merge($message->data(), $this->additionalMessageData($notification)),
            $this->messageBuilder($notifiable, $notification, $message)
        );
    }

    /**
     * Get the mailer Closure for the message.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return \Closure
     */
    protected function messageBuilder($notifiable, $notification, $message)
    {
        return function ($mailMessage) use ($notifiable, $notification, $message) {
            $this->buildMessage($mailMessage, $notifiable, $notification, $message);
        };
    }

    /**
     * Build the notification's view.
     *
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return string|array
     */
    protected function buildView($message)
    {
        if ($message->view) {
            return $message->view;
        }

        return [
            'html' => $this->buildMarkdownHtml($message),
            'text' => $this->buildMarkdownText($message),
        ];
    }

    /**
     * Build the HTML view for a Markdown message.
     *
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return \Closure
     */
    protected function buildMarkdownHtml($message)
    {
        return fn ($data) => $this->markdownRenderer($message)->render(
            $message->markdown, array_merge($data, $message->data()),
        );
    }

    /**
     * Build the text view for a Markdown message.
     *
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return \Closure
     */
    protected function buildMarkdownText($message)
    {
        return fn ($data) => $this->markdownRenderer($message)->renderText(
            $message->markdown, array_merge($data, $message->data()),
        );
    }

    /**
     * Get the Markdown implementation.
     *
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return \Illuminate\Mail\Markdown
     */
    protected function markdownRenderer($message)
    {
        $config = Container::getInstance()->get(ConfigRepository::class);

        $theme = $message->theme ?? $config->get('mail.markdown.theme', 'default');

        return $this->markdown->theme($theme);
    }

    /**
     * Get additional meta-data to pass along with the view data.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function additionalMessageData($notification)
    {
        return [
            '__laravel_notification_id' => $notification->id,
            '__laravel_notification' => get_class($notification),
            '__laravel_notification_queued' => in_array(
                ShouldQueue::class,
                class_implements($notification)
            ),
        ];
    }

    /**
     * Build the mail message.
     *
     * @param  \Illuminate\Mail\Message  $mailMessage
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return void
     */
    protected function buildMessage($mailMessage, $notifiable, $notification, $message)
    {
        $this->addressMessage($mailMessage, $notifiable, $notification, $message);

        $mailMessage->subject($message->subject ?: Str::title(
            Str::snake(class_basename($notification), ' ')
        ));

        $this->addAttachments($mailMessage, $message);

        if (! is_null($message->priority)) {
            $mailMessage->priority($message->priority);
        }

        if ($message->tags) {
            foreach ($message->tags as $tag) {
                $mailMessage->getHeaders()->add(new TagHeader($tag));
            }
        }

        if ($message->metadata) {
            foreach ($message->metadata as $key => $value) {
                $mailMessage->getHeaders()->add(new MetadataHeader($key, $value));
            }
        }

        $this->runCallbacks($mailMessage, $message);
    }

    /**
     * Address the mail message.
     *
     * @param  \Illuminate\Mail\Message  $mailMessage
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return void
     */
    protected function addressMessage($mailMessage, $notifiable, $notification, $message)
    {
        $this->addSender($mailMessage, $message);

        $mailMessage->to($this->getRecipients($notifiable, $notification, $message));

        if (! empty($message->cc)) {
            foreach ($message->cc as $cc) {
                $mailMessage->cc($cc[0], Arr::get($cc, 1));
            }
        }

        if (! empty($message->bcc)) {
            foreach ($message->bcc as $bcc) {
                $mailMessage->bcc($bcc[0], Arr::get($bcc, 1));
            }
        }
    }

    /**
     * Add the "from" and "reply to" addresses to the message.
     *
     * @param  \Illuminate\Mail\Message  $mailMessage
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return void
     */
    protected function addSender($mailMessage, $message)
    {
        if (! empty($message->from)) {
            $mailMessage->from($message->from[0], Arr::get($message->from, 1));
        }

        if (! empty($message->replyTo)) {
            foreach ($message->replyTo as $replyTo) {
                $mailMessage->replyTo($replyTo[0], Arr::get($replyTo, 1));
            }
        }
    }

    /**
     * Get the recipients of the given message.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return mixed
     */
    protected function getRecipients($notifiable, $notification, $message)
    {
        if (is_string($recipients = $notifiable->routeNotificationFor('mail', $notification))) {
            $recipients = [$recipients];
        }

        return (new Collection($recipients))
            ->mapWithKeys(function ($recipient, $email) {
                return is_numeric($email)
                    ? [$email => (is_string($recipient) ? $recipient : $recipient->email)]
                    : [$email => $recipient];
            })
            ->all();
    }

    /**
     * Add the attachments to the message.
     *
     * @param  \Illuminate\Mail\Message  $mailMessage
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return void
     */
    protected function addAttachments($mailMessage, $message)
    {
        foreach ($message->attachments as $attachment) {
            $mailMessage->attach($attachment['file'], $attachment['options']);
        }

        foreach ($message->rawAttachments as $attachment) {
            $mailMessage->attachData($attachment['data'], $attachment['name'], $attachment['options']);
        }
    }

    /**
     * Run the callbacks for the message.
     *
     * @param  \Illuminate\Mail\Message  $mailMessage
     * @param  \Illuminate\Notifications\Messages\MailMessage  $message
     * @return $this
     */
    protected function runCallbacks($mailMessage, $message)
    {
        foreach ($message->callbacks as $callback) {
            $callback($mailMessage->getSymfonyMessage());
        }

        return $this;
    }
}
