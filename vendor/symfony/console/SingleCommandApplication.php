<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SingleCommandApplication extends Command
{
    private string $version = 'UNKNOWN';
    private bool $autoExit = true;
    private bool $running = false;

    /**
     * @return $this
     */
    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @final
     *
     * @return $this
     */
    public function setAutoExit(bool $autoExit): static
    {
        $this->autoExit = $autoExit;

        return $this;
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        if ($this->running) {
            return parent::run($input, $output);
        }

        // We use the command name as the application name
        $application = new Application($this->getName() ?: 'UNKNOWN', $this->version);
        $application->setAutoExit($this->autoExit);
        // Fix the usage of the command displayed with "--help"
        $this->setName($_SERVER['argv'][0]);
        $application->addCommand($this);
        $application->setDefaultCommand($this->getName(), true);

        $this->running = true;
        try {
            $ret = $application->run($input, $output);
        } finally {
            $this->running = false;
        }

        return $ret;
    }
}
