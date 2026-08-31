<?php

namespace Illuminate\Mail\Mailables;

use InvalidArgumentException;

class Address
{
    /**
     * The recipient's email address.
     *
     * @var string
     */
    public $address;

    /**
     * The recipient's name.
     *
     * @var string|null
     */
    public $name;

    /**
     * Create a new address instance.
     *
     * @param  string  $address
     * @param  string|null  $name
     */
    public function __construct(string $address, ?string $name = null)
    {
        if (preg_match('/[\r\n]/', $address) > 0) {
            throw new InvalidArgumentException('Email addresses may not contain line break characters.');
        }

        $this->address = $address;
        $this->name = $name;
    }
}
