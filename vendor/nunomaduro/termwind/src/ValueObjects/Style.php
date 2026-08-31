<?php

declare(strict_types=1);

namespace Termwind\ValueObjects;

use Closure;
use Termwind\Actions\StyleToMethod;
use Termwind\Exceptions\InvalidColor;

/**
 * @internal
 */
final class Style
{
    /**
     * Creates a new value object instance.
     *
     * @param  Closure(Styles $styles, string|int ...$argument): Styles  $callback
     */
    public function __construct(private Closure $callback, private string $color = '')
    {
        // ..
    }

    /**
     * Apply the given set of styles to the styles.
     */
    public function apply(string $styles): void
    {
        $callback = clone $this->callback;

        $this->callback = static function (
            Styles $formatter,
            string|int ...$arguments
        ) use ($callback, $styles): Styles {
            $formatter = $callback($formatter, ...$arguments);

            return StyleToMethod::multiple($formatter, $styles);
        };
    }

    /**
     * Sets the color to the style.
     */
    public function color(string $color): void
    {
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color) < 1) {
            throw new InvalidColor(sprintf('The color %s is invalid.', $color));
        }

        $this->color = $color;
    }

    /**
     * Gets the color.
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Styles the given formatter with this style.
     */
    public function __invoke(Styles $styles, string|int ...$arguments): Styles
    {
        return ($this->callback)($styles, ...$arguments);
    }
}
