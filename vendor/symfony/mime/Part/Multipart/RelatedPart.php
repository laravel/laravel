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
use Symfony\Component\Mime\Part\AbstractPart;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class RelatedPart extends AbstractMultipartPart
{
    public function __construct(
        private AbstractPart $mainPart,
        AbstractPart $part,
        AbstractPart ...$parts,
    ) {
        $this->prepareParts($part, ...$parts);

        parent::__construct($part, ...$parts);
    }

    public function getParts(): array
    {
        return array_merge([$this->mainPart], parent::getParts());
    }

    public function getMediaSubtype(): string
    {
        return 'related';
    }

    private function generateContentId(): string
    {
        return bin2hex(random_bytes(16)).'@symfony';
    }

    private function prepareParts(AbstractPart ...$parts): void
    {
        foreach ($parts as $part) {
            if (!$part->getHeaders()->has('Content-ID')) {
                $part->getHeaders()->setHeaderBody('Id', 'Content-ID', $this->generateContentId());
            }
        }
    }
}
