<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * OutputAware interface.
 *
 * Used to pass the Shell's OutputInterface into components that need to write
 * messages directly to the console.
 */
interface OutputAware
{
    /**
     * Set the OutputInterface instance.
     */
    public function setOutput(OutputInterface $output): void;
}
