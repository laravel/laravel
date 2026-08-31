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
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class GdCaster
{
    public static function castGd(\GdImage $gd, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'size'] = imagesx($gd).'x'.imagesy($gd);
        $a[Caster::PREFIX_VIRTUAL.'trueColor'] = imageistruecolor($gd);

        return $a;
    }
}
