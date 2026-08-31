<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Notifications\Action;

class SimpleMessage
{
    /**
     * The "level" of the notification (info, success, error).
     *
     * @var string
     */
    public $level = 'info';

    /**
     * The subject of the notification.
     *
     * @var string
     */
    public $subject;

    /**
     * The notification's greeting.
     *
     * @var string
     */
    public $greeting;

    /**
     * The notification's salutation.
     *
     * @var string
     */
    public $salutation;

    /**
     * The "intro" lines of the notification.
     *
     * @var array
     */
    public $introLines = [];

    /**
     * The "outro" lines of the notification.
     *
     * @var array
     */
    public $outroLines = [];

    /**
     * The text / label for the action.
     *
     * @var string
     */
    public $actionText;

    /**
     * The action URL.
     *
     * @var string
     */
    public $actionUrl;

    /**
     * The name of the mailer that should send the notification.
     *
     * @var string
     */
    public $mailer;

    /**
     * Indicate that the notification gives information about a successful operation.
     *
     * @return $this
     */
    public function success()
    {
        $this->level = 'success';

        return $this;
    }

    /**
     * Indicate that the notification gives information about an error.
     *
     * @return $this
     */
    public function error()
    {
        $this->level = 'error';

        return $this;
    }

    /**
     * Set the "level" of the notification (success, error, etc.).
     *
     * @param  string  $level
     * @return $this
     */
    public function level($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Set the subject of the notification.
     *
     * @param  string  $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the greeting of the notification.
     *
     * @param  string  $greeting
     * @return $this
     */
    public function greeting($greeting)
    {
        $this->greeting = $greeting;

        return $this;
    }

    /**
     * Set the salutation of the notification.
     *
     * @param  string  $salutation
     * @return $this
     */
    public function salutation($salutation)
    {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * Add a line of text to the notification.
     *
     * @param  mixed  $line
     * @return $this
     */
    public function line($line)
    {
        return $this->with($line);
    }

    /**
     * Add a line of text to the notification if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  mixed  $line
     * @return $this
     */
    public function lineIf($boolean, $line)
    {
        if ($boolean) {
            return $this->line($line);
        }

        return $this;
    }

    /**
     * Add lines of text to the notification.
     *
     * @param  iterable  $lines
     * @return $this
     */
    public function lines($lines)
    {
        foreach ($lines as $line) {
            $this->line($line);
        }

        return $this;
    }

    /**
     * Add lines of text to the notification if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  iterable  $lines
     * @return $this
     */
    public function linesIf($boolean, $lines)
    {
        if ($boolean) {
            return $this->lines($lines);
        }

        return $this;
    }

    /**
     * Add a line of text to the notification.
     *
     * @param  mixed  $line
     * @return $this
     */
    public function with($line)
    {
        if ($line instanceof Action) {
            $this->action($line->text, $line->url);
        } elseif (! $this->actionText) {
            $this->introLines[] = $this->formatLine($line);
        } else {
            $this->outroLines[] = $this->formatLine($line);
        }

        return $this;
    }

    /**
     * Format the given line of text.
     *
     * @param  \Illuminate\Contracts\Support\Htmlable|string|array|null  $line
     * @return \Illuminate\Contracts\Support\Htmlable|string
     */
    protected function formatLine($line)
    {
        if ($line instanceof Htmlable) {
            return $line;
        }

        if (is_array($line)) {
            return implode(' ', array_map(trim(...), $line));
        }

        return trim(implode(' ', array_map(trim(...), preg_split('/\\r\\n|\\r|\\n/', $line ?? ''))));
    }

    /**
     * Configure the "call to action" button.
     *
     * @param  string  $text
     * @param  string  $url
     * @return $this
     */
    public function action($text, $url)
    {
        $this->actionText = $text;
        $this->actionUrl = $url;

        return $this;
    }

    /**
     * Set the name of the mailer that should send the notification.
     *
     * @param  string  $mailer
     * @return $this
     */
    public function mailer($mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'level' => $this->level,
            'subject' => $this->subject,
            'greeting' => $this->greeting,
            'salutation' => $this->salutation,
            'introLines' => $this->introLines,
            'outroLines' => $this->outroLines,
            'actionText' => $this->actionText,
            'actionUrl' => $this->actionUrl,
            'displayableActionUrl' => str_replace(['mailto:', 'tel:'], '', $this->actionUrl ?? ''),
        ];
    }
}
