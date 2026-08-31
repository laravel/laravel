<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command\ListCommand;

use Psy\Reflection\ReflectionNamespace;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Constant Enumerator class.
 */
class ConstantEnumerator extends Enumerator
{
    // Because `Json` is ugly.
    private const CATEGORY_LABELS = [
        'libxml'   => 'libxml',
        'openssl'  => 'OpenSSL',
        'pcre'     => 'PCRE',
        'sqlite3'  => 'SQLite3',
        'curl'     => 'cURL',
        'dom'      => 'DOM',
        'ftp'      => 'FTP',
        'gd'       => 'GD',
        'gmp'      => 'GMP',
        'iconv'    => 'iconv',
        'json'     => 'JSON',
        'ldap'     => 'LDAP',
        'mbstring' => 'mbstring',
        'odbc'     => 'ODBC',
        'pcntl'    => 'PCNTL',
        'pgsql'    => 'pgsql',
        'posix'    => 'POSIX',
        'mysqli'   => 'mysqli',
        'soap'     => 'SOAP',
        'exif'     => 'EXIF',
        'sysvmsg'  => 'sysvmsg',
        'xml'      => 'XML',
        'xsl'      => 'XSL',
    ];

    /**
     * {@inheritdoc}
     */
    protected function listItems(InputInterface $input, ?\Reflector $reflector = null, $target = null): array
    {
        // if we have a reflector, ensure that it's a namespace reflector
        if (($target !== null || $reflector !== null) && !$reflector instanceof ReflectionNamespace) {
            return [];
        }

        // only list constants if we are specifically asked
        if (!$input->getOption('constants')) {
            return [];
        }

        $user = $input->getOption('user');
        $internal = $input->getOption('internal');
        $category = $input->getOption('category');

        if ($category) {
            $category = \strtolower($category);

            if ($category === 'internal') {
                $internal = true;
                $category = null;
            } elseif ($category === 'user') {
                $user = true;
                $category = null;
            }
        }

        $ret = [];

        if ($user) {
            $ret['User Constants'] = $this->getConstants('user');
        }

        if ($internal) {
            $ret['Internal Constants'] = $this->getConstants('internal');
        }

        if ($category) {
            $caseCategory = \array_key_exists($category, self::CATEGORY_LABELS) ? self::CATEGORY_LABELS[$category] : \ucfirst($category);
            $label = $caseCategory.' Constants';
            $ret[$label] = $this->getConstants($category);
        }

        if (!$user && !$internal && !$category) {
            $ret['Constants'] = $this->getConstants();
        }

        if ($reflector !== null) {
            $prefix = \strtolower($reflector->getName()).'\\';

            foreach ($ret as $key => $names) {
                foreach (\array_keys($names) as $name) {
                    if (\strpos(\strtolower($name), $prefix) !== 0) {
                        unset($ret[$key][$name]);
                    }
                }
            }
        }

        return \array_map([$this, 'prepareConstants'], \array_filter($ret));
    }

    /**
     * Get defined constants.
     *
     * Optionally restrict constants to a given category, e.g. "date". If the
     * category is "internal", include all non-user-defined constants.
     *
     * @param string|null $category
     *
     * @return array
     */
    protected function getConstants(?string $category = null): array
    {
        if (!$category) {
            return \get_defined_constants();
        }

        $consts = \get_defined_constants(true);

        if ($category === 'internal') {
            unset($consts['user']);
            $values = \array_values($consts);

            return $values ? \array_merge(...$values) : [];
        }

        foreach ($consts as $key => $value) {
            if (\strtolower($key) === $category) {
                return $value;
            }
        }

        return [];
    }

    /**
     * Prepare formatted constant array.
     *
     * @param array $constants
     *
     * @return array
     */
    protected function prepareConstants(array $constants): array
    {
        // My kingdom for a generator.
        $ret = [];

        $names = \array_keys($constants);
        \natcasesort($names);

        foreach ($names as $name) {
            if ($this->showItem($name)) {
                $ret[$name] = [
                    'name'  => $name,
                    'style' => self::IS_CONSTANT,
                    'value' => $this->presentRef($constants[$name]),
                ];
            }
        }

        return $ret;
    }
}
