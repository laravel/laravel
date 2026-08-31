<?php

declare(strict_types=1);

namespace Dotenv\Store;

use Dotenv\Exception\InvalidPathException;
use Dotenv\Store\File\Reader;

final class FileStore implements StoreInterface
{
    /**
     * The file paths.
     *
     * @var string[]
     */
    private $filePaths;

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
     * Create a new file store instance.
     *
     * @param string[]    $filePaths
     * @param bool        $shortCircuit
     * @param string|null $fileEncoding
     *
     * @return void
     */
    public function __construct(array $filePaths, bool $shortCircuit, ?string $fileEncoding = null)
    {
        $this->filePaths = $filePaths;
        $this->shortCircuit = $shortCircuit;
        $this->fileEncoding = $fileEncoding;
    }

    /**
     * Read the content of the environment file(s).
     *
     * @throws \Dotenv\Exception\InvalidEncodingException|\Dotenv\Exception\InvalidPathException
     *
     * @return string
     */
    public function read()
    {
        if ($this->filePaths === []) {
            throw new InvalidPathException('At least one environment file path must be provided.');
        }

        $contents = Reader::read($this->filePaths, $this->shortCircuit, $this->fileEncoding);

        if (\count($contents) > 0) {
            return \implode("\n", $contents);
        }

        throw new InvalidPathException(
            \sprintf('Unable to read any of the environment file(s) at [%s].', \implode(', ', $this->filePaths))
        );
    }
}
