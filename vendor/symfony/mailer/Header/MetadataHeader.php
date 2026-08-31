<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Header;

use Symfony\Component\Mime\Header\UnstructuredHeader;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MetadataHeader extends UnstructuredHeader
{
    public function __construct(
        private string $key,
        string $value,
    ) {
        parent::__construct('X-Metadata-'.$key, $value);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
