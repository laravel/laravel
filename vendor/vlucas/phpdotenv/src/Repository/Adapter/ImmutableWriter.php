<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

final class ImmutableWriter implements WriterInterface
{
    /**
     * The inner writer to use.
     *
     * @var \Dotenv\Repository\Adapter\WriterInterface
     */
    private $writer;

    /**
     * The inner reader to use.
     *
     * @var \Dotenv\Repository\Adapter\ReaderInterface
     */
    private $reader;

    /**
     * The record of loaded variables.
     *
     * @var array<string, string>
     */
    private $loaded;

    /**
     * Create a new immutable writer instance.
     *
     * @param \Dotenv\Repository\Adapter\WriterInterface $writer
     * @param \Dotenv\Repository\Adapter\ReaderInterface $reader
     *
     * @return void
     */
    public function __construct(WriterInterface $writer, ReaderInterface $reader)
    {
        $this->writer = $writer;
        $this->reader = $reader;
        $this->loaded = [];
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
        // Don't overwrite existing environment variables
        // Ruby's dotenv does this with `ENV[key] ||= value`
        if ($this->isExternallyDefined($name)) {
            return false;
        }

        // Set the value on the inner writer
        if (!$this->writer->write($name, $value)) {
            return false;
        }

        // Record that we have loaded the variable
        $this->loaded[$name] = '';

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
        // Don't clear existing environment variables
        if ($this->isExternallyDefined($name)) {
            return false;
        }

        // Clear the value on the inner writer
        if (!$this->writer->delete($name)) {
            return false;
        }

        // Leave the variable as fair game
        unset($this->loaded[$name]);

        return true;
    }

    /**
     * Determine if the given variable is externally defined.
     *
     * That is, is it an "existing" variable.
     *
     * @param non-empty-string $name
     *
     * @return bool
     */
    private function isExternallyDefined(string $name)
    {
        return $this->reader->read($name)->isDefined() && !isset($this->loaded[$name]);
    }
}
