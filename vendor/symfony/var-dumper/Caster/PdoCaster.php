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
 * Casts PDO related classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class PdoCaster
{
    private const PDO_ATTRIBUTES = [
        'CASE' => [
            \PDO::CASE_LOWER => 'LOWER',
            \PDO::CASE_NATURAL => 'NATURAL',
            \PDO::CASE_UPPER => 'UPPER',
        ],
        'ERRMODE' => [
            \PDO::ERRMODE_SILENT => 'SILENT',
            \PDO::ERRMODE_WARNING => 'WARNING',
            \PDO::ERRMODE_EXCEPTION => 'EXCEPTION',
        ],
        'TIMEOUT',
        'PREFETCH',
        'AUTOCOMMIT',
        'PERSISTENT',
        'DRIVER_NAME',
        'SERVER_INFO',
        'ORACLE_NULLS' => [
            \PDO::NULL_NATURAL => 'NATURAL',
            \PDO::NULL_EMPTY_STRING => 'EMPTY_STRING',
            \PDO::NULL_TO_STRING => 'TO_STRING',
        ],
        'CLIENT_VERSION',
        'SERVER_VERSION',
        'STATEMENT_CLASS',
        'EMULATE_PREPARES',
        'CONNECTION_STATUS',
        'STRINGIFY_FETCHES',
        'DEFAULT_FETCH_MODE' => [
            \PDO::FETCH_ASSOC => 'ASSOC',
            \PDO::FETCH_BOTH => 'BOTH',
            \PDO::FETCH_LAZY => 'LAZY',
            \PDO::FETCH_NUM => 'NUM',
            \PDO::FETCH_OBJ => 'OBJ',
        ],
    ];

    public static function castPdo(\PDO $c, array $a, Stub $stub, bool $isNested): array
    {
        $attr = [];
        $errmode = $c->getAttribute(\PDO::ATTR_ERRMODE);
        $c->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        foreach (self::PDO_ATTRIBUTES as $k => $v) {
            if (!isset($k[0])) {
                $k = $v;
                $v = [];
            }

            try {
                $attr[$k] = 'ERRMODE' === $k ? $errmode : $c->getAttribute(\constant('PDO::ATTR_'.$k));
                if ($v && isset($v[$attr[$k]])) {
                    $attr[$k] = new ConstStub($v[$attr[$k]], $attr[$k]);
                }
            } catch (\Exception) {
            }
        }
        if (isset($attr[$k = 'STATEMENT_CLASS'][1])) {
            if ($attr[$k][1]) {
                $attr[$k][1] = new ArgsStub($attr[$k][1], '__construct', $attr[$k][0]);
            }
            $attr[$k][0] = new ClassStub($attr[$k][0]);
        }

        $prefix = Caster::PREFIX_VIRTUAL;
        $a += [
            $prefix.'inTransaction' => method_exists($c, 'inTransaction'),
            $prefix.'errorInfo' => $c->errorInfo(),
            $prefix.'attributes' => new EnumStub($attr),
        ];

        if ($a[$prefix.'inTransaction']) {
            $a[$prefix.'inTransaction'] = $c->inTransaction();
        } else {
            unset($a[$prefix.'inTransaction']);
        }

        if (!isset($a[$prefix.'errorInfo'][1], $a[$prefix.'errorInfo'][2])) {
            unset($a[$prefix.'errorInfo']);
        }

        $c->setAttribute(\PDO::ATTR_ERRMODE, $errmode);

        return $a;
    }

    public static function castPdoStatement(\PDOStatement $c, array $a, Stub $stub, bool $isNested): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;
        $a[$prefix.'errorInfo'] = $c->errorInfo();

        if (!isset($a[$prefix.'errorInfo'][1], $a[$prefix.'errorInfo'][2])) {
            unset($a[$prefix.'errorInfo']);
        }

        return $a;
    }
}
