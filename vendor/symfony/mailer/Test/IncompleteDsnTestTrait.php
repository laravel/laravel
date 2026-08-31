<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\IncompleteDsnException;
use Symfony\Component\Mailer\Transport\Dsn;

trait IncompleteDsnTestTrait
{
    /**
     * @psalm-return iterable<array{0: Dsn}>
     */
    abstract public static function incompleteDsnProvider(): iterable;

    /**
     * @dataProvider incompleteDsnProvider
     */
    #[DataProvider('incompleteDsnProvider')]
    public function testIncompleteDsnException(Dsn $dsn)
    {
        $factory = $this->getFactory();

        $this->expectException(IncompleteDsnException::class);
        $factory->create($dsn);
    }
}
