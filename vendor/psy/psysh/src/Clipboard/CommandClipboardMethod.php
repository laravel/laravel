<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Clipboard;

use Symfony\Component\Console\Output\OutputInterface;

class CommandClipboardMethod implements ClipboardMethod
{
    private string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function copy(string $text, OutputInterface $output): bool
    {
        $process = \proc_open($this->command, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);
        if ($process === false) {
            return false;
        }

        $success = $this->writeAll($pipes[0], $text);
        \fclose($pipes[0]);

        // Drain stdout and stderr to prevent the child process from blocking.
        \stream_get_contents($pipes[1]);
        \fclose($pipes[1]);
        \stream_get_contents($pipes[2]);
        \fclose($pipes[2]);

        return $success && \proc_close($process) === 0;
    }

    /**
     * Write the full string to the pipe, returning false on failure.
     *
     * @param resource $pipe
     */
    private function writeAll($pipe, string $text): bool
    {
        $remaining = $text;

        while ($remaining !== '') {
            $written = \fwrite($pipe, $remaining);
            if ($written === false || $written === 0) {
                return false;
            }

            $remaining = (string) \substr($remaining, $written);
        }

        return true;
    }
}
