<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process\Pipes;

/**
 * PipesInterface manages descriptors and pipes for the use of proc_open.
 *
 * @author Romain Neutron <imprec@gmail.com>
 *
 * @internal
 */
interface PipesInterface
{
    public const CHUNK_SIZE = 16384;

    /**
     * Returns an array of descriptors for the use of proc_open.
     */
    public function getDescriptors(): array;

    /**
     * Returns an array of filenames indexed by their related stream in case these pipes use temporary files.
     *
     * @return string[]
     */
    public function getFiles(): array;

    /**
     * Reads data in file handles and pipes.
     *
     * @param bool $blocking Whether to use blocking calls or not
     * @param bool $close    Whether to close pipes if they've reached EOF
     *
     * @return string[] An array of read data indexed by their fd
     */
    public function readAndWrite(bool $blocking, bool $close = false): array;

    /**
     * Returns if the current state has open file handles or pipes.
     */
    public function areOpen(): bool;

    /**
     * Returns if pipes are able to read output.
     */
    public function haveReadSupport(): bool;

    /**
     * Closes file handles and pipes.
     */
    public function close(): void;
}
