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

class Osc52ClipboardMethod implements ClipboardMethod
{
    public function copy(string $text, OutputInterface $output): bool
    {
        $base64 = \base64_encode($text);
        $osc52 = "\x1b]52;c;{$base64}\x07";

        if (\getenv('TMUX')) {
            $osc52 = "\x1bPtmux;\x1b".\str_replace("\x1b", "\x1b\x1b", $osc52)."\x1b\\";
        }

        $output->write($osc52, false, OutputInterface::OUTPUT_RAW);

        return true;
    }
}
