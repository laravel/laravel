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

use League\CommonMark\Node\Node;

/**
 * Renders multiple nodes by delegating to the individual node renderers and adding spacing where needed
 */
interface ChildNodeRendererInterface
{
    /**
     * @param Node[] $nodes
     */
    public function renderNodes(iterable $nodes): string;

    public function getBlockSeparator(): string;

    public function getInnerSeparator(): string;
}
