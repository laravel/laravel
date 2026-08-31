<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Node;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Reference\ReferenceInterface;
use League\CommonMark\Reference\ReferenceableInterface;

final class FootnoteRef extends AbstractInline implements ReferenceableInterface
{
    private ReferenceInterface $reference;

    /** @psalm-readonly */
    private ?string $content = null;

    /**
     * @param array<mixed> $data
     */
    public function __construct(ReferenceInterface $reference, ?string $content = null, array $data = [])
    {
        parent::__construct();

        $this->reference = $reference;
        $this->content   = $content;

        if (\count($data) > 0) {
            $this->data->import($data);
        }
    }

    public function getReference(): ReferenceInterface
    {
        return $this->reference;
    }

    public function setReference(ReferenceInterface $reference): void
    {
        $this->reference = $reference;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
