<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use Dotenv\Util\Str;

final class Value
{
    /**
     * The string representation of the parsed value.
     *
     * @var string
     */
    private $chars;

    /**
     * The locations of the variables in the value.
     *
     * @var int[]
     */
    private $vars;

    /**
     * Internal constructor for a value.
     *
     * @param string $chars
     * @param int[]  $vars
     *
     * @return void
     */
    private function __construct(string $chars, array $vars)
    {
        $this->chars = $chars;
        $this->vars = $vars;
    }

    /**
     * Create an empty value instance.
     *
     * @return \Dotenv\Parser\Value
     */
    public static function blank()
    {
        return new self('', []);
    }

    /**
     * Create a new value instance, appending the characters.
     *
     * @param string $chars
     * @param bool   $var
     *
     * @return \Dotenv\Parser\Value
     */
    public function append(string $chars, bool $var)
    {
        return new self(
            $this->chars.$chars,
            $var ? \array_merge($this->vars, [Str::len($this->chars)]) : $this->vars
        );
    }

    /**
     * Get the string representation of the parsed value.
     *
     * @return string
     */
    public function getChars()
    {
        return $this->chars;
    }

    /**
     * Get the locations of the variables in the value.
     *
     * @return int[]
     */
    public function getVars()
    {
        $vars = $this->vars;

        \rsort($vars);

        return $vars;
    }
}
