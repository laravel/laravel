<?php

declare(strict_types=1);

namespace Termwind;

use Closure;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Termwind\Components\Element;
use Termwind\Exceptions\InvalidChild;

/**
 * @internal
 */
final class Termwind
{
    /**
     * The implementation of the output.
     */
    private static ?OutputInterface $renderer;

    /**
     * Sets the renderer implementation.
     */
    public static function renderUsing(?OutputInterface $renderer): void
    {
        self::$renderer = $renderer ?? new ConsoleOutput;
    }

    /**
     * Creates a div element instance.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function div(array|string $content = '', string $styles = '', array $properties = []): Components\Div
    {
        $content = self::prepareElements($content, $styles);

        return Components\Div::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates a paragraph element instance.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function paragraph(array|string $content = '', string $styles = '', array $properties = []): Components\Paragraph
    {
        $content = self::prepareElements($content, $styles);

        return Components\Paragraph::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates a span element instance with the given style.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function span(array|string $content = '', string $styles = '', array $properties = []): Components\Span
    {
        $content = self::prepareElements($content, $styles);

        return Components\Span::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates an element instance with raw content.
     *
     * @param  array<int, Element|string>|string  $content
     */
    public static function raw(array|string $content = ''): Components\Raw
    {
        return Components\Raw::fromStyles(
            self::getRenderer(), $content
        );
    }

    /**
     * Creates an anchor element instance with the given style.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function anchor(array|string $content = '', string $styles = '', array $properties = []): Components\Anchor
    {
        $content = self::prepareElements($content, $styles);

        return Components\Anchor::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates an unordered list instance.
     *
     * @param  array<int, string|Element>  $content
     * @param  array<string, mixed>  $properties
     */
    public static function ul(array $content = [], string $styles = '', array $properties = []): Components\Ul
    {
        $ul = Components\Ul::fromStyles(
            self::getRenderer(), '', $styles, $properties
        );

        $content = self::prepareElements(
            $content,
            $styles,
            static function ($li) use ($ul): string|Element {
                if (is_string($li)) {
                    return $li;
                }

                if (! $li instanceof Components\Li) {
                    throw new InvalidChild('Unordered lists only accept `li` as child');
                }

                return match (true) {
                    $li->hasStyle('list-none') => $li,
                    $ul->hasStyle('list-none') => $li->addStyle('list-none'),
                    $ul->hasStyle('list-square') => $li->addStyle('list-square'),
                    $ul->hasStyle('list-disc') => $li->addStyle('list-disc'),
                    default => $li->addStyle('list-none'),
                };
            }
        );

        return $ul->setContent($content);
    }

    /**
     * Creates an ordered list instance.
     *
     * @param  array<int, string|Element>  $content
     * @param  array<string, mixed>  $properties
     */
    public static function ol(array $content = [], string $styles = '', array $properties = []): Components\Ol
    {
        $ol = Components\Ol::fromStyles(
            self::getRenderer(), '', $styles, $properties
        );

        $index = 0;

        $content = self::prepareElements(
            $content,
            $styles,
            static function ($li) use ($ol, &$index): string|Element {
                if (is_string($li)) {
                    return $li;
                }

                if (! $li instanceof Components\Li) {
                    throw new InvalidChild('Ordered lists only accept `li` as child');
                }

                return match (true) {
                    $li->hasStyle('list-none') => $li->addStyle('list-none'),
                    $ol->hasStyle('list-none') => $li->addStyle('list-none'),
                    $ol->hasStyle('list-decimal') => $li->addStyle('list-decimal-'.(++$index)),
                    default => $li->addStyle('list-none'),
                };
            }
        );

        return $ol->setContent($content);
    }

    /**
     * Creates a list item instance.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function li(array|string $content = '', string $styles = '', array $properties = []): Components\Li
    {
        $content = self::prepareElements($content, $styles);

        return Components\Li::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates a description list instance.
     *
     * @param  array<int, string|Element>  $content
     * @param  array<string, mixed>  $properties
     */
    public static function dl(array $content = [], string $styles = '', array $properties = []): Components\Dl
    {
        $content = self::prepareElements(
            $content,
            $styles,
            static function ($element): string|Element {
                if (is_string($element)) {
                    return $element;
                }

                if (! $element instanceof Components\Dt && ! $element instanceof Components\Dd) {
                    throw new InvalidChild('Description lists only accept `dt` and `dd` as children');
                }

                return $element;
            }
        );

        return Components\Dl::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates a description term instance.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function dt(array|string $content = '', string $styles = '', array $properties = []): Components\Dt
    {
        $content = self::prepareElements($content, $styles);

        return Components\Dt::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates a description details instance.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     */
    public static function dd(array|string $content = '', string $styles = '', array $properties = []): Components\Dd
    {
        $content = self::prepareElements($content, $styles);

        return Components\Dd::fromStyles(
            self::getRenderer(), $content, $styles, $properties
        );
    }

    /**
     * Creates a horizontal rule instance.
     *
     * @param  array<string, mixed>  $properties
     */
    public static function hr(string $styles = '', array $properties = []): Components\Hr
    {
        return Components\Hr::fromStyles(
            self::getRenderer(), '', $styles, $properties
        );
    }

    /**
     * Creates an break line element instance.
     *
     * @param  array<string, mixed>  $properties
     */
    public static function breakLine(string $styles = '', array $properties = []): Components\BreakLine
    {
        return Components\BreakLine::fromStyles(
            self::getRenderer(), '', $styles, $properties
        );
    }

    /**
     * Gets the current renderer instance.
     */
    public static function getRenderer(): OutputInterface
    {
        return self::$renderer ??= new ConsoleOutput;
    }

    /**
     * Convert child elements to a string.
     *
     * @param  array<int, string|Element>|string  $elements
     * @return array<int, string|Element>
     */
    private static function prepareElements($elements, string $styles = '', ?Closure $callback = null): array
    {
        if ($callback === null) {
            $callback = static fn ($element): string|Element => $element;
        }

        $elements = is_array($elements) ? $elements : [$elements];

        return array_map($callback, $elements);
    }
}
