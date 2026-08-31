<?php

declare(strict_types=1);

/*
 * This file is part of the league/config package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Config;

use Dflydev\DotAccessData\Data;
use Dflydev\DotAccessData\DataInterface;
use Dflydev\DotAccessData\Exception\DataException;
use Dflydev\DotAccessData\Exception\InvalidPathException;
use Dflydev\DotAccessData\Exception\MissingPathException;
use League\Config\Exception\UnknownOptionException;
use League\Config\Exception\ValidationException;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException as NetteValidationException;

final class Configuration implements ConfigurationBuilderInterface, ConfigurationInterface
{
    /** @psalm-readonly */
    private Data $userConfig;

    /**
     * @var array<string, Schema>
     *
     * @psalm-allow-private-mutation
     */
    private array $configSchemas = [];

    /** @psalm-allow-private-mutation */
    private Data $finalConfig;

    /**
     * @var array<string, mixed>
     *
     * @psalm-allow-private-mutation
     */
    private array $cache = [];

    /** @psalm-readonly */
    private ConfigurationInterface $reader;

    /**
     * @param array<string, Schema> $baseSchemas
     */
    public function __construct(array $baseSchemas = [])
    {
        $this->configSchemas = $baseSchemas;
        $this->userConfig    = new Data();
        $this->finalConfig   = new Data();

        $this->reader = new ReadOnlyConfiguration($this);
    }

    /**
     * Registers a new configuration schema at the given top-level key
     *
     * @psalm-allow-private-mutation
     */
    public function addSchema(string $key, Schema $schema): void
    {
        $this->invalidate();

        $this->configSchemas[$key] = $schema;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-allow-private-mutation
     */
    public function merge(array $config = []): void
    {
        $this->invalidate();

        $this->userConfig->import($config, DataInterface::REPLACE);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-allow-private-mutation
     */
    public function set(string $key, $value): void
    {
        $this->invalidate();

        try {
            $this->userConfig->set($key, $value);
        } catch (DataException $ex) {
            throw new UnknownOptionException($ex->getMessage(), $key, (int) $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function get(string $key)
    {
        if (\array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        try {
            $this->build(self::getTopLevelKey($key));

            return $this->cache[$key] = $this->finalConfig->get($key);
        } catch (InvalidPathException | MissingPathException $ex) {
            throw new UnknownOptionException($ex->getMessage(), $key, (int) $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function exists(string $key): bool
    {
        if (\array_key_exists($key, $this->cache)) {
            return true;
        }

        try {
            $this->build(self::getTopLevelKey($key));

            return $this->finalConfig->has($key);
        } catch (InvalidPathException | UnknownOptionException $ex) {
            return false;
        }
    }

    /**
     * @psalm-mutation-free
     */
    public function reader(): ConfigurationInterface
    {
        return $this->reader;
    }

    /**
     * @psalm-external-mutation-free
     */
    private function invalidate(): void
    {
        $this->cache       = [];
        $this->finalConfig = new Data();
    }

    /**
     * Applies the schema against the configuration to return the final configuration
     *
     * @throws ValidationException|UnknownOptionException|InvalidPathException
     *
     * @psalm-allow-private-mutation
     */
    private function build(string $topLevelKey): void
    {
        if ($this->finalConfig->has($topLevelKey)) {
            return;
        }

        if (! isset($this->configSchemas[$topLevelKey])) {
            throw new UnknownOptionException(\sprintf('Missing config schema for "%s"', $topLevelKey), $topLevelKey);
        }

        try {
            $userData = [$topLevelKey => $this->userConfig->get($topLevelKey)];
        } catch (DataException $ex) {
            $userData = [];
        }

        try {
            $schema    = $this->configSchemas[$topLevelKey];
            $processor = new Processor();

            $processed = $processor->process(Expect::structure([$topLevelKey => $schema]), $userData);

            $this->raiseAnyDeprecationNotices($processor->getWarnings());

            $this->finalConfig->import((array) self::convertStdClassesToArrays($processed));
        } catch (NetteValidationException $ex) {
            throw new ValidationException($ex);
        }
    }

    /**
     * Recursively converts stdClass instances to arrays
     *
     * @phpstan-template T
     *
     * @param T $data
     *
     * @return mixed
     *
     * @phpstan-return ($data is \stdClass ? array<string, mixed> : T)
     *
     * @psalm-pure
     */
    private static function convertStdClassesToArrays($data)
    {
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }

        if (\is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::convertStdClassesToArrays($v);
            }
        }

        return $data;
    }

    /**
     * @param string[] $warnings
     */
    private function raiseAnyDeprecationNotices(array $warnings): void
    {
        foreach ($warnings as $warning) {
            @\trigger_error($warning, \E_USER_DEPRECATED);
        }
    }

    /**
     * @throws InvalidPathException
     */
    private static function getTopLevelKey(string $path): string
    {
        if (\strlen($path) === 0) {
            throw new InvalidPathException('Path cannot be an empty string');
        }

        $path = \str_replace(['.', '/'], '.', $path);

        $firstDelimiter = \strpos($path, '.');
        if ($firstDelimiter === false) {
            return $path;
        }

        return \substr($path, 0, $firstDelimiter);
    }
}
