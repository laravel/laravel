<?php

namespace Laravel\Pail\ValueObjects;

use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Stringable;

class MessageLogged implements Stringable
{
    /**
     * Creates a new instance of the message logged.
     *
     * @param  array{__pail: array{origin: array{trace: array<int, array{file: string, line: int}>|null, type: string, queue: string, job: string, command: string, method: string, path: string, auth_id: ?string, auth_email: ?string}}, exception: array{class: string, file: string}}  $context
     */
    protected function __construct(
        protected string $message,
        protected string $datetime,
        protected string $levelName,
        protected array $context,
    ) {
        //
    }

    /**
     * Creates a new instance of the message logged from a json string.
     */
    public static function fromJson(string $json): static
    {
        /** @var array{message: string, context: array{__pail: array{origin: array{trace: array<int, array{file: string, line: int}>|null, type: string, queue: string, job: string, command: string, method: string, path: string, auth_id: ?string, auth_email: ?string}}, exception: array{class: string, file: string}}, level_name: string, datetime: string} $array */
        $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        [
            'message' => $message,
            'datetime' => $datetime,
            'level_name' => $levelName,
            'context' => $context,
        ] = $array;

        return new static($message, $datetime, $levelName, $context);
    }

    /**
     * Gets the log message's message.
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * Gets the log message's date.
     */
    public function date(): string
    {
        if (Env::get('PAIL_TESTS') ?? false) {
            return '2024-01-01 03:04:05';
        }

        $time = Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $this->datetime);

        assert($time instanceof Carbon);

        return $time->format('Y-m-d H:i:s');
    }

    /**
     * Gets the log message's time.
     */
    public function time(): string
    {
        if (Env::get('PAIL_TESTS') ?? false) {
            return '03:04:05';
        }

        $time = Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $this->datetime);

        assert($time instanceof Carbon);

        return $time->format('H:i:s');
    }

    /**
     * Gets the log message's class.
     */
    public function classOrType(): string
    {
        return $this->context['exception']['class'] ?? strtoupper($this->levelName);
    }

    /**
     * Gets the log message's color.
     */
    public function color(): string
    {
        return match ($this->levelName) {
            'DEBUG' => 'gray',
            'INFO' => 'blue',
            'NOTICE' => 'yellow',
            'WARNING' => 'yellow',
            'ERROR' => 'red',
            'CRITICAL' => 'red',
            'ALERT' => 'red',
            'EMERGENCY' => 'red',
            default => 'gray',
        };
    }

    /**
     * Gets the log message's level.
     */
    public function level(): string
    {
        return $this->levelName;
    }

    /**
     * Gets the log message's file, if any.
     */
    public function file(): ?string
    {
        return $this->context['exception']['file'] ?? null;
    }

    /**
     * Gets the log message's auth id.
     */
    public function authId(): ?string
    {
        return $this->context['__pail']['origin']['auth_id'] ?? null;
    }

    /**
     * Gets the log message's origin.
     */
    public function origin(): Origin\Console|Origin\Http|Origin\Queue
    {
        return match ($this->context['__pail']['origin']['type']) {
            'console' => Origin\Console::fromArray($this->context['__pail']['origin']),
            'queue' => Origin\Queue::fromArray($this->context['__pail']['origin']),
            default => Origin\Http::fromArray($this->context['__pail']['origin']),
        };
    }

    /**
     * Gets the log message's trace, if any.
     *
     * @return array<int, array{file: string, line: int}>|null
     */
    public function trace(): ?array
    {
        return $this->context['__pail']['origin']['trace'] ?? null;
    }

    /**
     * Gets the log message's context.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return collect($this->context)->except([
            '__pail',
            'exception',
            'userId',
        ])->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return json_encode([
            'message' => $this->message,
            'datetime' => $this->datetime,
            'level_name' => $this->levelName,
            'context' => $this->context,
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
