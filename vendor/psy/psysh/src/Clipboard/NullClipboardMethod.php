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

class NullClipboardMethod implements ClipboardMethod
{
    public const REASON_NONE = 'none';
    public const REASON_NO_COMMAND = 'no_command';
    public const REASON_NO_COMMAND_SUPPORT = 'no_command_support';

    private bool $warned = false;
    private bool $isSsh;
    private string $reason;

    public function __construct(bool $isSsh, string $reason = self::REASON_NONE)
    {
        $this->isSsh = $isSsh;
        $this->reason = $reason;
    }

    public function copy(string $text, OutputInterface $output): bool
    {
        if ($this->warned) {
            return false;
        }
        $this->warned = true;

        if ($this->isSsh) {
            $output->writeln('<error>Clipboard copy is unavailable over SSH.</error>');
            $output->writeln('Set <comment>useOsc52Clipboard: true</comment> to enable OSC 52.');
            $output->writeln('Only enable this on trusted systems.');

            return false;
        }

        if ($this->reason === self::REASON_NO_COMMAND_SUPPORT) {
            $output->writeln('<error>Clipboard commands are unavailable in this PHP environment.</error>');
            $output->writeln('Configured <comment>clipboardCommand</comment> requires <comment>proc_open</comment>.');
        } elseif ($this->reason === self::REASON_NO_COMMAND) {
            $output->writeln('<error>No clipboard command was found.</error>');
            $output->writeln('Set <comment>clipboardCommand</comment> to configure one.');
        }

        return false;
    }
}
