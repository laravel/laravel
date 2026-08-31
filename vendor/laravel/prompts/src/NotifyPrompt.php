<?php

namespace Laravel\Prompts;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class NotifyPrompt extends Prompt
{
    /**
     * Create a new NotifyPrompt instance.
     */
    public function __construct(
        public string $title,
        public string $body = '',
        public string $subtitle = '',
        public string $sound = '',
        public string $icon = '',
    ) {
        //
    }

    /**
     * Send the notification.
     */
    public function prompt(): bool
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => $this->sendMacOS(),
            'Linux' => $this->sendLinux(),
            default => false,
        };
    }

    /**
     * Send a notification on macOS using osascript.
     */
    protected function sendMacOS(): bool
    {
        $script = 'display notification '.$this->escapeAppleScript($this->body);
        $script .= ' with title '.$this->escapeAppleScript($this->title);

        if ($this->subtitle !== '') {
            $script .= ' subtitle '.$this->escapeAppleScript($this->subtitle);
        }

        if ($this->sound !== '') {
            $script .= ' sound name '.$this->escapeAppleScript($this->sound);
        }

        return $this->execute(['osascript', '-e', $script]);
    }

    /**
     * Send a notification on Linux, trying available notifiers.
     */
    protected function sendLinux(): bool
    {
        $finder = new ExecutableFinder;

        if ($finder->find('notify-send') !== null) {
            return $this->sendLinuxNotifySend();
        }

        if ($finder->find('kdialog') !== null) {
            return $this->sendLinuxKDialog();
        }

        return false;
    }

    /**
     * Send a notification using notify-send.
     */
    protected function sendLinuxNotifySend(): bool
    {
        $command = ['notify-send'];

        if ($this->icon !== '') {
            $command[] = '--icon';
            $command[] = $this->icon;
        }

        $command[] = $this->title;

        if ($this->body !== '') {
            $command[] = $this->body;
        }

        return $this->execute($command);
    }

    /**
     * Send a notification using kdialog.
     */
    protected function sendLinuxKDialog(): bool
    {
        $message = $this->body !== '' ? "{$this->title}: {$this->body}" : $this->title;

        return $this->execute(['kdialog', '--passivepopup', $message, '5', '--title', $this->title]);
    }

    /**
     * Execute a command and return whether it was successful.
     *
     * @param  array<int, string>  $command
     */
    protected function execute(array $command): bool
    {
        $process = new Process($command);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Escape a string for use in AppleScript.
     */
    protected function escapeAppleScript(string $value): string
    {
        return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
    }

    /**
     * Send the notification.
     */
    public function display(): void
    {
        $this->prompt();
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): bool
    {
        return true;
    }
}
