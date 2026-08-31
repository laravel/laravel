<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Part\Multipart;

use Symfony\Component\Mime\Part\AbstractMultipartPart;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class AlternativePart extends AbstractMultipartPart
{
    public function getMediaSubtype(): string
    {
        return 'alternative';
    }
}
