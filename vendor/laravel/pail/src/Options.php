<?php

namespace Laravel\Pail;

use Illuminate\Console\Command;
use Laravel\Pail\ValueObjects\MessageLogged;

class Options
{
    /**
     * Creates a new instance of the tail options.
     */
    public function __construct(
        protected int $timeout,
        protected ?string $authId,
        protected ?string $level,
        protected ?string $filter,
        protected ?string $message,
    ) {
        //
    }

    /**
     * Creates a new instance of the tail options from the given console command.
     */
    public static function fromCommand(Command $command): static
    {
        $authId = $command->option('auth') ?? $command->option('user');
        assert(is_string($authId) || $authId === null);

        $level = $command->option('level');
        assert(is_string($level) || $level === null);

        $filter = $command->option('filter');
        assert(is_string($filter) || $filter === null);

        $message = $command->option('message');
        assert(is_string($message) || $message === null);

        $timeout = (int) $command->option('timeout');

        return new static($timeout, $authId, $level, $filter, $message);
    }

    /**
     * Whether the tail options accept the given message logged.
     */
    public function accepts(MessageLogged $messageLogged): bool
    {
        if (is_string($this->authId) && $messageLogged->authId() !== $this->authId) {
            return false;
        }

        if (is_string($this->level) && strtolower($messageLogged->level()) !== strtolower($this->level)) {
            return false;
        }

        if (is_string($this->filter) && ! str_contains(strtolower((string) $messageLogged), strtolower($this->filter))) {
            return false;
        }

        if (is_string($this->message) && ! str_contains(strtolower($messageLogged->message()), strtolower($this->message))) {
            return false;
        }

        return true;
    }

    /**
     * Returns the number of seconds before the process is killed.
     */
    public function timeout(): int
    {
        return $this->timeout;
    }
}
