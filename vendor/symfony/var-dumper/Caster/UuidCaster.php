<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 *
 * @internal since Symfony 7.3
 */
final class UuidCaster
{
    public static function castRamseyUuid(UuidInterface $c, array $a, Stub $stub, bool $isNested): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'uuid' => (string) $c,
        ];

        return $a;
    }
}
