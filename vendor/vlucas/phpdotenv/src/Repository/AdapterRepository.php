<?php

declare(strict_types=1);

namespace Dotenv\Repository;

use Dotenv\Repository\Adapter\ReaderInterface;
use Dotenv\Repository\Adapter\WriterInterface;
use InvalidArgumentException;

final class AdapterRepository implements RepositoryInterface
{
    /**
     * The reader to use.
     *
     * @var \Dotenv\Repository\Adapter\ReaderInterface
     */
    private $reader;

    /**
     * The writer to use.
     *
     * @var \Dotenv\Repository\Adapter\WriterInterface
     */
    private $writer;

    /**
     * Create a new adapter repository instance.
     *
     * @param \Dotenv\Repository\Adapter\ReaderInterface $reader
     * @param \Dotenv\Repository\Adapter\WriterInterface $writer
     *
     * @return void
     */
    public function __construct(ReaderInterface $reader, WriterInterface $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }

    /**
     * Determine if the given environment variable is defined.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name)
    {
        return '' !== $name && $this->reader->read($name)->isDefined();
    }

    /**
     * Get an environment variable.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return string|null
     */
    public function get(string $name)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Expected name to be a non-empty string.');
        }

        return $this->reader->read($name)->getOrElse(null);
    }

    /**
     * Set an environment variable.
     *
     * @param string $name
     * @param string $value
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function set(string $name, string $value)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Expected name to be a non-empty string.');
        }

        return $this->writer->write($name, $value);
    }

    /**
     * Clear an environment variable.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function clear(string $name)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Expected name to be a non-empty string.');
        }

        return $this->writer->delete($name);
    }
}
