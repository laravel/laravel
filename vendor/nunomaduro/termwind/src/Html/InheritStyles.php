<?php

declare(strict_types=1);

namespace Termwind\Html;

use Termwind\Components\Element;
use Termwind\Termwind;
use Termwind\ValueObjects\Styles;

/**
 * @internal
 */
final class InheritStyles
{
    /**
     * Applies styles from parent element to child elements.
     *
     * @param  array<int, Element|string>  $elements
     * @return array<int, Element|string>
     */
    public function __invoke(array $elements, Styles $styles): array
    {
        $elements = array_values($elements);

        foreach ($elements as &$element) {
            if (is_string($element)) {
                $element = Termwind::raw($element);
            }

            $element->inheritFromStyles($styles);
        }

        /** @var Element[] $elements */
        if (($styles->getProperties()['styles']['display'] ?? 'inline') === 'flex') {
            $elements = $this->applyFlex($elements);
        }

        return match ($styles->getProperties()['styles']['justifyContent'] ?? false) {
            'between' => $this->applyJustifyBetween($elements),
            'evenly' => $this->applyJustifyEvenly($elements),
            'around' => $this->applyJustifyAround($elements),
            'center' => $this->applyJustifyCenter($elements),
            default => $elements,
        };
    }

    /**
     * Applies flex-1 to child elements with the class.
     *
     * @param  array<int, Element>  $elements
     * @return array<int, Element>
     */
    private function applyFlex(array $elements): array
    {
        [$totalWidth, $parentWidth] = $this->getWidthFromElements($elements);

        $width = max(0, array_reduce($elements, function ($carry, $element) {
            return $carry += $element->hasStyle('flex-1') ? $element->getInnerWidth() : 0;
        }, $parentWidth - $totalWidth));

        $flexed = array_values(array_filter(
            $elements, fn ($element) => $element->hasStyle('flex-1')
        ));

        foreach ($flexed as $index => &$element) {
            if ($width === 0 && ! ($element->getProperties()['styles']['contentRepeat'] ?? false)) {
                continue;
            }

            $float = $width / count($flexed);
            $elementWidth = floor($float);

            if ($index === count($flexed) - 1) {
                $elementWidth += ($float - floor($float)) * count($flexed);
            }

            $element->addStyle("w-{$elementWidth}");
        }

        return $elements;
    }

    /**
     * Applies the space between the elements.
     *
     * @param  array<int, Element>  $elements
     * @return array<int, Element|string>
     */
    private function applyJustifyBetween(array $elements): array
    {
        if (count($elements) <= 1) {
            return $elements;
        }

        [$totalWidth, $parentWidth] = $this->getWidthFromElements($elements);
        $space = ($parentWidth - $totalWidth) / (count($elements) - 1);

        if ($space < 1) {
            return $elements;
        }

        $arr = [];

        foreach ($elements as $index => &$element) {
            if ($index !== 0) {
                // Since there is no float pixel, on the last one it should round up...
                $length = $index === count($elements) - 1 ? ceil($space) : floor($space);
                $arr[] = str_repeat(' ', (int) $length);
            }

            $arr[] = $element;
        }

        return $arr;
    }

    /**
     * Applies the space between and around the elements.
     *
     * @param  array<int, Element>  $elements
     * @return array<int, Element|string>
     */
    private function applyJustifyEvenly(array $elements): array
    {
        [$totalWidth, $parentWidth] = $this->getWidthFromElements($elements);
        $space = ($parentWidth - $totalWidth) / (count($elements) + 1);

        if ($space < 1) {
            return $elements;
        }

        $arr = [];
        foreach ($elements as &$element) {
            $arr[] = str_repeat(' ', (int) floor($space));
            $arr[] = $element;
        }

        $decimals = ceil(($space - floor($space)) * (count($elements) + 1));
        $arr[] = str_repeat(' ', (int) (floor($space) + $decimals));

        return $arr;
    }

    /**
     * Applies the space around the elements.
     *
     * @param  array<int, Element>  $elements
     * @return array<int, Element|string>
     */
    private function applyJustifyAround(array $elements): array
    {
        if (count($elements) === 0) {
            return $elements;
        }

        [$totalWidth, $parentWidth] = $this->getWidthFromElements($elements);
        $space = ($parentWidth - $totalWidth) / count($elements);

        if ($space < 1) {
            return $elements;
        }

        $contentSize = $totalWidth;
        $arr = [];

        foreach ($elements as $index => &$element) {
            if ($index !== 0) {
                $arr[] = str_repeat(' ', (int) ceil($space));
                $contentSize += ceil($space);
            }

            $arr[] = $element;
        }

        return [
            str_repeat(' ', (int) floor(($parentWidth - $contentSize) / 2)),
            ...$arr,
            str_repeat(' ', (int) ceil(($parentWidth - $contentSize) / 2)),
        ];
    }

    /**
     * Applies the space on before first element and after last element.
     *
     * @param  array<int, Element>  $elements
     * @return array<int, Element|string>
     */
    private function applyJustifyCenter(array $elements): array
    {
        [$totalWidth, $parentWidth] = $this->getWidthFromElements($elements);
        $space = $parentWidth - $totalWidth;

        if ($space < 1) {
            return $elements;
        }

        return [
            str_repeat(' ', (int) floor($space / 2)),
            ...$elements,
            str_repeat(' ', (int) ceil($space / 2)),
        ];
    }

    /**
     * Gets the total width for the elements and their parent width.
     *
     * @param  array<int, Element>  $elements
     * @return int[]
     */
    private function getWidthFromElements(array $elements)
    {
        $totalWidth = (int) array_reduce($elements, fn ($carry, $element) => $carry += $element->getLength(), 0);
        $parentWidth = Styles::getParentWidth($elements[0]->getProperties()['parentStyles'] ?? []);

        return [$totalWidth, $parentWidth];
    }
}
