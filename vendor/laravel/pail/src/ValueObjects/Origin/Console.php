<?php

namespace Laravel\Pail\ValueObjects\Origin;

class Console
{
    /**
     * Creates a new instance of the console origin.
     */
    public function __construct(
        public ?string $command,
    ) {
        //
    }

    /**
     * Creates a new instance of the console origin from the given json string.
     *
     * @param  array{command?: string}  $array
     */
    public static function fromArray(array $array): static
    {
        $command = $array['command'] ?? null;

        return new static($command);
    }
}
