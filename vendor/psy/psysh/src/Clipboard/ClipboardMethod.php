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

interface ClipboardMethod
{
    public function copy(string $text, OutputInterface $output): bool;
}
