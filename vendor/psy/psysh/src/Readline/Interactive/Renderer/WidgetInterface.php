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
 * Something that draws itself into a Frame within an Area.
 */
interface WidgetInterface
{
    /**
     * Append rendered lines to $frame, consuming at most $area->getHeight() rows.
     *
     * @return int Rows actually consumed (must be <= $area->getHeight())
     */
    public function render(Frame $frame, Area $area): int;
}
