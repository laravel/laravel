<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\HtmlToTextConverter;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface HtmlToTextConverterInterface
{
    /**
     * Converts an HTML representation of a Message to a text representation.
     *
     * The output must use the same charset as the HTML one.
     */
    public function convert(string $html, string $charset): string;
}
