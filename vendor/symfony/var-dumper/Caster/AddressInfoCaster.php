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
 * @internal since Symfony 7.3
 */
final class AddressInfoCaster
{
    private const MAPS = [
        'ai_flags' => [
            1 => 'AI_PASSIVE',
            2 => 'AI_CANONNAME',
            4 => 'AI_NUMERICHOST',
            8 => 'AI_V4MAPPED',
            16 => 'AI_ALL',
            32 => 'AI_ADDRCONFIG',
            64 => 'AI_IDN',
            128 => 'AI_CANONIDN',
            1024 => 'AI_NUMERICSERV',
        ],
        'ai_family' => [
            1 => 'AF_UNIX',
            2 => 'AF_INET',
            10 => 'AF_INET6',
            44 => 'AF_DIVERT',
        ],
        'ai_socktype' => [
            1 => 'SOCK_STREAM',
            2 => 'SOCK_DGRAM',
            3 => 'SOCK_RAW',
            4 => 'SOCK_RDM',
            5 => 'SOCK_SEQPACKET',
        ],
        'ai_protocol' => [
            1 => 'SOL_SOCKET',
            6 => 'SOL_TCP',
            17 => 'SOL_UDP',
            136 => 'SOL_UDPLITE',
        ],
    ];

    public static function castAddressInfo(\AddressInfo $h, array $a, Stub $stub, bool $isNested): array
    {
        static $resolvedMaps;

        if (!$resolvedMaps) {
            foreach (self::MAPS as $k => $map) {
                foreach ($map as $v => $name) {
                    if (\defined($name)) {
                        $resolvedMaps[$k][\constant($name)] = $name;
                    } elseif (!isset($resolvedMaps[$k][$v])) {
                        $resolvedMaps[$k][$v] = $name;
                    }
                }
            }
        }

        foreach (socket_addrinfo_explain($h) as $k => $v) {
            $a[Caster::PREFIX_VIRTUAL.$k] = match (true) {
                'ai_flags' === $k => ConstStub::fromBitfield($v, $resolvedMaps[$k]),
                isset($resolvedMaps[$k][$v]) => new ConstStub($resolvedMaps[$k][$v], $v),
                default => $v,
            };
        }

        return $a;
    }
}
