<?php

namespace Illuminate\View;

use Illuminate\Contracts\Support\Htmlable;
use InvalidArgumentException;
use Stringable;

class ComponentSlot implements Htmlable, Stringable
{
    /**
     * The slot attribute bag.
     *
     * @var \Illuminate\View\ComponentAttributeBag
     */
    public $attributes;

    /**
     * The slot contents.
     *
     * @var string
     */
    protected $contents;

    /**
     * Create a new slot instance.
     *
     * @param  string  $contents
     * @param  array  $attributes
     */
    public function __construct($contents = '', $attributes = [])
    {
        $this->contents = $contents;

        $this->withAttributes($attributes);
    }

    /**
     * Set the extra attributes that the slot should make available.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function withAttributes(array $attributes)
    {
        $this->attributes = new ComponentAttributeBag($attributes);

        return $this;
    }

    /**
     * Get the slot's HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->contents;
    }

    /**
     * Determine if the slot is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->contents === '';
    }

    /**
     * Determine if the slot is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * Determine if the slot has non-comment content.
     *
     * @param  callable|string|null  $callable
     * @return bool
     */
    public function hasActualContent(callable|string|null $callable = null)
    {
        if (is_string($callable) && ! function_exists($callable)) {
            throw new InvalidArgumentException('Callable does not exist.');
        }

        return filter_var(
            $this->contents,
            FILTER_CALLBACK,
            ['options' => $callable ?? fn ($input) => trim(preg_replace("/<!--([\s\S]*?)-->/", '', $input))]
        ) !== '';
    }

    /**
     * Get the slot's HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
