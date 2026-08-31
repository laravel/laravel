<?php

declare(strict_types=1);

namespace Termwind\Html;

use Termwind\Components\Element;
use Termwind\Termwind;
use Termwind\ValueObjects\Node;

/**
 * @internal
 */
final class PreRenderer
{
    /**
     * Gets HTML content from a given node and converts to the content element.
     */
    public function toElement(Node $node): Element
    {
        $lines = explode("\n", $node->getHtml());
        if (reset($lines) === '') {
            array_shift($lines);
        }

        if (end($lines) === '') {
            array_pop($lines);
        }

        $maxStrLen = array_reduce(
            $lines,
            static fn (int $max, string $line) => ($max < strlen($line)) ? strlen($line) : $max,
            0
        );

        $styles = $node->getClassAttribute();
        $html = array_map(
            static fn (string $line) => (string) Termwind::div(str_pad($line, $maxStrLen + 3), $styles),
            $lines
        );

        return Termwind::raw(
            implode('', $html)
        );
    }
}
