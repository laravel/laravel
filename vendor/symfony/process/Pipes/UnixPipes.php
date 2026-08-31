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

use Symfony\Component\Process\Process;

/**
 * UnixPipes implementation uses unix pipes as handles.
 *
 * @author Romain Neutron <imprec@gmail.com>
 *
 * @internal
 */
class UnixPipes extends AbstractPipes
{
    public function __construct(
        private ?bool $ttyMode,
        private bool $ptyMode,
        mixed $input,
        private bool $haveReadSupport,
    ) {
        parent::__construct($input);
    }

    public function __serialize(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __unserialize(array $data): void
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getDescriptors(): array
    {
        if (!$this->haveReadSupport) {
            $nullstream = fopen('/dev/null', 'c');

            return [
                ['pipe', 'r'],
                $nullstream,
                $nullstream,
            ];
        }

        if ($this->ttyMode) {
            return [
                ['file', '/dev/tty', 'r'],
                ['file', '/dev/tty', 'w'],
                ['file', '/dev/tty', 'w'],
            ];
        }

        if ($this->ptyMode && Process::isPtySupported()) {
            return [
                ['pty'],
                ['pty'],
                ['pipe', 'w'], // stderr needs to be in a pipe to correctly split error and output, since PHP will use the same stream for both
            ];
        }

        return [
            ['pipe', 'r'],
            ['pipe', 'w'], // stdout
            ['pipe', 'w'], // stderr
        ];
    }

    public function getFiles(): array
    {
        return [];
    }

    public function readAndWrite(bool $blocking, bool $close = false): array
    {
        $this->unblock();
        $w = $this->write();

        $read = $e = [];
        $r = $this->pipes;
        unset($r[0]);

        // let's have a look if something changed in streams
        set_error_handler($this->handleError(...));
        if (($r || $w) && false === stream_select($r, $w, $e, 0, $blocking ? Process::TIMEOUT_PRECISION * 1E6 : 0)) {
            restore_error_handler();
            // if a system call has been interrupted, forget about it, let's try again
            // otherwise, an error occurred, let's reset pipes
            if (!$this->hasSystemCallBeenInterrupted()) {
                $this->pipes = [];
            }

            return $read;
        }
        restore_error_handler();

        foreach ($r as $pipe) {
            // prior PHP 5.4 the array passed to stream_select is modified and
            // lose key association, we have to find back the key
            $read[$type = array_search($pipe, $this->pipes, true)] = '';

            do {
                $data = @fread($pipe, self::CHUNK_SIZE);
                $read[$type] .= $data;
            } while (isset($data[0]) && ($close || isset($data[self::CHUNK_SIZE - 1])));

            if (!isset($read[$type][0])) {
                unset($read[$type]);
            }

            if ($close && feof($pipe)) {
                fclose($pipe);
                unset($this->pipes[$type]);
            }
        }

        return $read;
    }

    public function haveReadSupport(): bool
    {
        return $this->haveReadSupport;
    }

    public function areOpen(): bool
    {
        return (bool) $this->pipes;
    }
}
