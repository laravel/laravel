<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use PhpOption\Option;

final class Entry
{
    /**
     * The entry name.
     *
     * @var string
     */
    private $name;

    /**
     * The entry value.
     *
     * @var \Dotenv\Parser\Value|null
     */
    private $value;

    /**
     * Create a new entry instance.
     *
     * @param string                    $name
     * @param \Dotenv\Parser\Value|null $value
     *
     * @return void
     */
    public function __construct(string $name, ?Value $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the entry name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the entry value.
     *
     * @return \PhpOption\Option<\Dotenv\Parser\Value>
     */
    public function getValue()
    {
        /** @var \PhpOption\Option<\Dotenv\Parser\Value> */
        return Option::fromValue($this->value);
    }
}
