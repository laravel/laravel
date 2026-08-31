<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Renderer;

/**
 * A Widget that appends a fixed list of pre-rendered lines.
 *
 * Useful when overlay content is already computed as string[] and the
 * caller has no reason to do Area-driven layout itself.
 */
class LinesWidget implements WidgetInterface
{
    /** @var string[] */
    private array $lines;

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Frame $frame, Area $area): int
    {
        $count = 0;
        foreach ($this->lines as $line) {
            if ($count >= $area->getHeight()) {
                break;
            }
            $frame->appendLine($line);
            $count++;
        }

        return $count;
    }
}
