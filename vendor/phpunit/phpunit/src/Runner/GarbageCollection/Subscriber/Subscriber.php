<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\GarbageCollection;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class Subscriber
{
    private GarbageCollectionHandler $handler;

    public function __construct(GarbageCollectionHandler $handler)
    {
        $this->handler = $handler;
    }

    protected function handler(): GarbageCollectionHandler
    {
        return $this->handler;
    }
}
