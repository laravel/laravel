<?php

declare(strict_types=1);

namespace Dotenv\Store;

use Dotenv\Store\File\Paths;

final class StoreBuilder
{
    /**
     * The of default name.
     */
    private const DEFAULT_NAME = '.env';

    /**
     * The paths to search within.
     *
     * @var string[]
     */
    private $paths;

    /**
     * The file names to search for.
     *
     * @var string[]
     */
    private $names;

    /**
     * Should file loading short circuit?
     *
     * @var bool
     */
    private $shortCircuit;

    /**
     * The file encoding.
     *
     * @var string|null
     */
    private $fileEncoding;

    /**
     * Create a new store builder instance.
     *
     * @param string[]    $paths
     * @param string[]    $names
     * @param bool        $shortCircuit
     * @param string|null $fileEncoding
     *
     * @return void
     */
    private function __construct(array $paths = [], array $names = [], bool $shortCircuit = false, ?string $fileEncoding = null)
    {
        $this->paths = $paths;
        $this->names = $names;
        $this->shortCircuit = $shortCircuit;
        $this->fileEncoding = $fileEncoding;
    }

    /**
     * Create a new store builder instance with no names.
     *
     * @return \Dotenv\Store\StoreBuilder
     */
    public static function createWithNoNames()
    {
        return new self();
    }

    /**
     * Create a new store builder instance with the default name.
     *
     * @return \Dotenv\Store\StoreBuilder
     */
    public static function createWithDefaultName()
    {
        return new self([], [self::DEFAULT_NAME]);
    }

    /**
     * Creates a store builder with the given path added.
     *
     * @param string $path
     *
     * @return \Dotenv\Store\StoreBuilder
     */
    public function addPath(string $path)
    {
        return new self(\array_merge($this->paths, [$path]), $this->names, $this->shortCircuit, $this->fileEncoding);
    }

    /**
     * Creates a store builder with the given name added.
     *
     * @param string $name
     *
     * @return \Dotenv\Store\StoreBuilder
     */
    public function addName(string $name)
    {
        return new self($this->paths, \array_merge($this->names, [$name]), $this->shortCircuit, $this->fileEncoding);
    }

    /**
     * Creates a store builder with short circuit mode enabled.
     *
     * @return \Dotenv\Store\StoreBuilder
     */
    public function shortCircuit()
    {
        return new self($this->paths, $this->names, true, $this->fileEncoding);
    }

    /**
     * Creates a store builder with the specified file encoding.
     *
     * @param string|null $fileEncoding
     *
     * @return \Dotenv\Store\StoreBuilder
     */
    public function fileEncoding(?string $fileEncoding = null)
    {
        return new self($this->paths, $this->names, $this->shortCircuit, $fileEncoding);
    }

    /**
     * Creates a new store instance.
     *
     * @return \Dotenv\Store\StoreInterface
     */
    public function make()
    {
        return new FileStore(
            Paths::filePaths($this->paths, $this->names),
            $this->shortCircuit,
            $this->fileEncoding
        );
    }
}
