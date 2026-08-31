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
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 *
 * @internal
 */
final class SocketCaster
{
    public static function castSocket(\Socket $socket, array $a, Stub $stub, bool $isNested): array
    {
        socket_getsockname($socket, $addr, $port);
        $info = stream_get_meta_data(socket_export_stream($socket));

        if (\PHP_VERSION_ID >= 80300) {
            $uri = ($info['uri'] ?? '//');
            if (str_starts_with($uri, 'unix://')) {
                $uri .= $addr;
            } else {
                $uri .= \sprintf(str_contains($addr, ':') ? '[%s]:%s' : '%s:%s', $addr, $port);
            }

            $a[Caster::PREFIX_VIRTUAL.'uri'] = $uri;

            if (@socket_atmark($socket)) {
                $a[Caster::PREFIX_VIRTUAL.'atmark'] = true;
            }
        }

        $a += [
            Caster::PREFIX_VIRTUAL.'timed_out' => $info['timed_out'],
            Caster::PREFIX_VIRTUAL.'blocked' => $info['blocked'],
        ];

        if (!$lastError = socket_last_error($socket)) {
            return $a;
        }

        static $errors;

        if (!$errors) {
            $errors = get_defined_constants(true)['sockets'] ?? [];
            $errors = array_flip(array_filter($errors, static fn ($k) => str_starts_with($k, 'SOCKET_E'), \ARRAY_FILTER_USE_KEY));
        }

        $a[Caster::PREFIX_VIRTUAL.'last_error'] = new ConstStub($errors[$lastError], socket_strerror($lastError));

        return $a;
    }
}
