<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\Option;
use PhpOption\Some;

final class ServerConstAdapter implements AdapterInterface
{
    /**
     * Create a new server const adapter instance.
     *
     * @return void
     */
    private function __construct()
    {
        //
    }

    /**
     * Create a new instance of the adapter, if it is available.
     *
     * @return \PhpOption\Option<\Dotenv\Repository\Adapter\AdapterInterface>
     */
    public static function create()
    {
        /** @var \PhpOption\Option<AdapterInterface> */
        return Some::create(new self());
    }

    /**
     * Read an environment variable, if it exists.
     *
     * @param non-empty-string $name
     *
     * @return \PhpOption\Option<string>
     */
    public function read(string $name)
    {
        /** @var \PhpOption\Option<string> */
        return Option::fromArraysValue($_SERVER, $name)
            ->filter(static function ($value) {
                return \is_scalar($value);
            })
            ->map(static function ($value) {
                if ($value === false) {
                    return 'false';
                }

                if ($value === true) {
                    return 'true';
                }

                return (string) $value;
            });
    }

    /**
     * Write to an environment variable, if possible.
     *
     * @param non-empty-string $name
     * @param string           $value
     *
     * @return bool
     */
    public function write(string $name, string $value)
    {
        $_SERVER[$name] = $value;

        return true;
    }

    /**
     * Delete an environment variable, if possible.
     *
     * @param non-empty-string $name
     *
     * @return bool
     */
    public function delete(string $name)
    {
        unset($_SERVER[$name]);

        return true;
    }
}
