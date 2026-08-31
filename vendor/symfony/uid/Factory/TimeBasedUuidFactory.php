<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid\Factory;

use Symfony\Component\Uid\TimeBasedUidInterface;
use Symfony\Component\Uid\Uuid;

class TimeBasedUuidFactory
{
    /**
     * @param class-string<Uuid&TimeBasedUidInterface> $class
     */
    public function __construct(
        private string $class,
        private ?Uuid $node = null,
    ) {
    }

    public function create(?\DateTimeInterface $time = null): Uuid&TimeBasedUidInterface
    {
        $class = $this->class;

        if (null === $time && null === $this->node) {
            return new $class();
        }

        return new $class($class::generate($time, $this->node));
    }
}
