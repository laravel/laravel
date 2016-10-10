<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Exception;

use Symfony\Component\Finder\Adapter\AdapterInterface;
use Symfony\Component\Finder\Shell\Command;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class ShellCommandFailureException extends AdapterFailureException
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @param AdapterInterface $adapter
     * @param Command          $command
     * @param \Exception|null  $previous
     */
    public function __construct(AdapterInterface $adapter, Command $command, \Exception $previous = null)
    {
        $this->command = $command;
        parent::__construct($adapter, 'Shell command failed: "'.$command->join().'".', $previous);
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }
}
