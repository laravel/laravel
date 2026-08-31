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
final class CurlCaster
{
    public static function castCurl(\CurlHandle $h, array $a, Stub $stub, bool $isNested): array
    {
        foreach (curl_getinfo($h) as $key => $val) {
            $a[Caster::PREFIX_VIRTUAL.$key] = $val;
        }

        return $a;
    }
}
