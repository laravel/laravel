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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarExporter\Internal\LazyObjectState;

/**
 * @final
 *
 * @internal since Symfony 7.3
 */
class SymfonyCaster
{
    private const REQUEST_GETTERS = [
        'pathInfo' => 'getPathInfo',
        'requestUri' => 'getRequestUri',
        'baseUrl' => 'getBaseUrl',
        'basePath' => 'getBasePath',
        'method' => 'getMethod',
        'format' => 'getRequestFormat',
    ];

    public static function castRequest(Request $request, array $a, Stub $stub, bool $isNested): array
    {
        $clone = null;

        foreach (self::REQUEST_GETTERS as $prop => $getter) {
            $key = Caster::PREFIX_PROTECTED.$prop;
            if (\array_key_exists($key, $a) && null === $a[$key]) {
                $clone ??= clone $request;
                $a[Caster::PREFIX_VIRTUAL.$prop] = $clone->{$getter}();
            }
        }

        return $a;
    }

    public static function castHttpClient($client, array $a, Stub $stub, bool $isNested): array
    {
        $multiKey = \sprintf("\0%s\0multi", $client::class);
        if (isset($a[$multiKey]) && !$a[$multiKey] instanceof Stub) {
            $a[$multiKey] = new CutStub($a[$multiKey]);
        }

        return $a;
    }

    public static function castHttpClientResponse($response, array $a, Stub $stub, bool $isNested): array
    {
        $stub->cut += \count($a);
        $a = [];

        foreach ($response->getInfo() as $k => $v) {
            $a[Caster::PREFIX_VIRTUAL.$k] = $v;
        }

        return $a;
    }

    public static function castLazyObjectState($state, array $a, Stub $stub, bool $isNested): array
    {
        if (!$isNested) {
            return $a;
        }

        $stub->cut += \count($a) - 1;

        $instance = $a['realInstance'] ?? null;

        if (isset($a['status'])) { // forward-compat with Symfony 8
            $a = ['status' => new ConstStub(match ($a['status']) {
                LazyObjectState::STATUS_INITIALIZED_FULL => 'INITIALIZED_FULL',
                LazyObjectState::STATUS_INITIALIZED_PARTIAL => 'INITIALIZED_PARTIAL',
                LazyObjectState::STATUS_UNINITIALIZED_FULL => 'UNINITIALIZED_FULL',
                LazyObjectState::STATUS_UNINITIALIZED_PARTIAL => 'UNINITIALIZED_PARTIAL',
            }, $a['status'])];
        }

        if ($instance) {
            $a['realInstance'] = $instance;
            --$stub->cut;
        }

        return $a;
    }

    public static function castUuid(Uuid $uuid, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'toBase58'] = $uuid->toBase58();
        $a[Caster::PREFIX_VIRTUAL.'toBase32'] = $uuid->toBase32();

        // symfony/uid >= 5.3
        if (method_exists($uuid, 'getDateTime')) {
            $a[Caster::PREFIX_VIRTUAL.'time'] = $uuid->getDateTime()->format('Y-m-d H:i:s.u \U\T\C');
        }

        return $a;
    }

    public static function castUlid(Ulid $ulid, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'toBase58'] = $ulid->toBase58();
        $a[Caster::PREFIX_VIRTUAL.'toRfc4122'] = $ulid->toRfc4122();

        // symfony/uid >= 5.3
        if (method_exists($ulid, 'getDateTime')) {
            $a[Caster::PREFIX_VIRTUAL.'time'] = $ulid->getDateTime()->format('Y-m-d H:i:s.v \U\T\C');
        }

        return $a;
    }
}
