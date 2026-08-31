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

namespace League\CommonMark\Extension\Embed;

/**
 * Interface for a service which updates the embed code(s) for the given array of embeds
 */
interface EmbedAdapterInterface
{
    /**
     * @param Embed[] $embeds
     */
    public function updateEmbeds(array $embeds): void;
}
