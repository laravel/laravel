<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Completion;

/**
 * Represents a single suggested value.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Suggestion implements \Stringable
{
    public function __construct(
        private readonly string $value,
        private readonly string $description = '',
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
