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
 * Casts pgsql resources to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class PgSqlCaster
{
    private const PARAM_CODES = [
        'server_encoding',
        'client_encoding',
        'is_superuser',
        'session_authorization',
        'DateStyle',
        'TimeZone',
        'IntervalStyle',
        'integer_datetimes',
        'application_name',
        'standard_conforming_strings',
    ];

    private const TRANSACTION_STATUS = [
        \PGSQL_TRANSACTION_IDLE => 'PGSQL_TRANSACTION_IDLE',
        \PGSQL_TRANSACTION_ACTIVE => 'PGSQL_TRANSACTION_ACTIVE',
        \PGSQL_TRANSACTION_INTRANS => 'PGSQL_TRANSACTION_INTRANS',
        \PGSQL_TRANSACTION_INERROR => 'PGSQL_TRANSACTION_INERROR',
        \PGSQL_TRANSACTION_UNKNOWN => 'PGSQL_TRANSACTION_UNKNOWN',
    ];

    private const RESULT_STATUS = [
        \PGSQL_EMPTY_QUERY => 'PGSQL_EMPTY_QUERY',
        \PGSQL_COMMAND_OK => 'PGSQL_COMMAND_OK',
        \PGSQL_TUPLES_OK => 'PGSQL_TUPLES_OK',
        \PGSQL_COPY_OUT => 'PGSQL_COPY_OUT',
        \PGSQL_COPY_IN => 'PGSQL_COPY_IN',
        \PGSQL_BAD_RESPONSE => 'PGSQL_BAD_RESPONSE',
        \PGSQL_NONFATAL_ERROR => 'PGSQL_NONFATAL_ERROR',
        \PGSQL_FATAL_ERROR => 'PGSQL_FATAL_ERROR',
    ];

    private const DIAG_CODES = [
        'severity' => \PGSQL_DIAG_SEVERITY,
        'sqlstate' => \PGSQL_DIAG_SQLSTATE,
        'message' => \PGSQL_DIAG_MESSAGE_PRIMARY,
        'detail' => \PGSQL_DIAG_MESSAGE_DETAIL,
        'hint' => \PGSQL_DIAG_MESSAGE_HINT,
        'statement position' => \PGSQL_DIAG_STATEMENT_POSITION,
        'internal position' => \PGSQL_DIAG_INTERNAL_POSITION,
        'internal query' => \PGSQL_DIAG_INTERNAL_QUERY,
        'context' => \PGSQL_DIAG_CONTEXT,
        'file' => \PGSQL_DIAG_SOURCE_FILE,
        'line' => \PGSQL_DIAG_SOURCE_LINE,
        'function' => \PGSQL_DIAG_SOURCE_FUNCTION,
    ];

    public static function castLargeObject($lo, array $a, Stub $stub, bool $isNested): array
    {
        $a['seek position'] = pg_lo_tell($lo);

        return $a;
    }

    public static function castLink($link, array $a, Stub $stub, bool $isNested): array
    {
        $a['status'] = pg_connection_status($link);
        $a['status'] = new ConstStub(\PGSQL_CONNECTION_OK === $a['status'] ? 'PGSQL_CONNECTION_OK' : 'PGSQL_CONNECTION_BAD', $a['status']);
        $a['busy'] = pg_connection_busy($link);

        $a['transaction'] = pg_transaction_status($link);
        if (isset(self::TRANSACTION_STATUS[$a['transaction']])) {
            $a['transaction'] = new ConstStub(self::TRANSACTION_STATUS[$a['transaction']], $a['transaction']);
        }

        $a['pid'] = pg_get_pid($link);
        $a['last error'] = pg_last_error($link);
        $a['last notice'] = pg_last_notice($link);
        $a['host'] = pg_host($link);
        $a['port'] = pg_port($link);
        $a['dbname'] = pg_dbname($link);
        $a['options'] = pg_options($link);
        $a['version'] = pg_version($link);

        foreach (self::PARAM_CODES as $v) {
            if (false !== $s = pg_parameter_status($link, $v)) {
                $a['param'][$v] = $s;
            }
        }

        $a['param']['client_encoding'] = pg_client_encoding($link);
        $a['param'] = new EnumStub($a['param']);

        return $a;
    }

    public static function castResult($result, array $a, Stub $stub, bool $isNested): array
    {
        $a['num rows'] = pg_num_rows($result);
        $a['status'] = pg_result_status($result);
        if (isset(self::RESULT_STATUS[$a['status']])) {
            $a['status'] = new ConstStub(self::RESULT_STATUS[$a['status']], $a['status']);
        }
        $a['command-completion tag'] = pg_result_status($result, \PGSQL_STATUS_STRING);

        if (-1 === $a['num rows']) {
            foreach (self::DIAG_CODES as $k => $v) {
                $a['error'][$k] = pg_result_error_field($result, $v);
            }
        }

        $a['affected rows'] = pg_affected_rows($result);
        $a['last OID'] = pg_last_oid($result);

        $fields = pg_num_fields($result);

        for ($i = 0; $i < $fields; ++$i) {
            $field = [
                'name' => pg_field_name($result, $i),
                'table' => \sprintf('%s (OID: %s)', pg_field_table($result, $i), pg_field_table($result, $i, true)),
                'type' => \sprintf('%s (OID: %s)', pg_field_type($result, $i), pg_field_type_oid($result, $i)),
                'nullable' => (bool) (\PHP_VERSION_ID >= 80300 ? pg_field_is_null($result, null, $i) : pg_field_is_null($result, $i)),
                'storage' => pg_field_size($result, $i).' bytes',
                'display' => (\PHP_VERSION_ID >= 80300 ? pg_field_prtlen($result, null, $i) : pg_field_prtlen($result, $i)).' chars',
            ];
            if (' (OID: )' === $field['table']) {
                $field['table'] = null;
            }
            if ('-1 bytes' === $field['storage']) {
                $field['storage'] = 'variable size';
            } elseif ('1 bytes' === $field['storage']) {
                $field['storage'] = '1 byte';
            }
            if ('1 chars' === $field['display']) {
                $field['display'] = '1 char';
            }
            $a['fields'][] = new EnumStub($field);
        }

        return $a;
    }
}
