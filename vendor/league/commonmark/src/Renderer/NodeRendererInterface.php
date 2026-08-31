<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Renderer;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Node\Node;

interface NodeRendererInterface
{
    /**
     * @return \Stringable|string|null
     *
     * @throws InvalidArgumentException if the wrong type of Node is provided
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer);
}
