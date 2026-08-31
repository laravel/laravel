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
class DefaultHtmlToTextConverter implements HtmlToTextConverterInterface
{
    public function convert(string $html, string $charset): string
    {
        return strip_tags(preg_replace('{<(head|style)\b.*?</\1>}is', '', $html));
    }
}
