<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Traits;

use Carbon\Exceptions\InvalidCastException;
use DateTimeInterface;

/**
 * Trait Cast.
 *
 * Utils to cast into an other class.
 */
trait Cast
{
    /**
     * Cast the current instance into the given class.
     *
     * @template T
     *
     * @param class-string<T> $className The $className::instance() method will be called to cast the current object.
     *
     * @return T
     */
    public function cast(string $className): mixed
    {
        if (!method_exists($className, 'instance')) {
            if (is_a($className, DateTimeInterface::class, true)) {
                return $className::createFromFormat('U.u', $this->rawFormat('U.u'))
                    ->setTimezone($this->getTimezone());
            }

            throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
        }

        return $className::instance($this);
    }
}
