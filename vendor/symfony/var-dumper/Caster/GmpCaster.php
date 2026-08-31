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

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Casts GMP objects to array representation.
 *
 * @author Hamza Amrouche <hamza.simperfit@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class GmpCaster
{
    public static function castGmp(\GMP $gmp, array $a, Stub $stub, bool $isNested, int $filter): array
    {
        $a[Caster::PREFIX_VIRTUAL.'value'] = new ConstStub(gmp_strval($gmp), gmp_strval($gmp));

        return $a;
    }
}
