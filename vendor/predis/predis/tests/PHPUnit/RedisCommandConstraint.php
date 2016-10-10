<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Predis\Command\CommandInterface;

/**
 * Constraint that verifies a redis command.
 */
class RedisCommandConstraint extends PHPUnit_Framework_Constraint
{
    protected $commandID;
    protected $arguments;

    /**
     * @param string|CommandInterface $command   Expected command ID or instance.
     * @param array                   $arguments Expected command arguments.
     */
    public function __construct($command = null, array $arguments = null)
    {
        if ($command instanceof CommandInterface) {
            $this->commandID = strtoupper($command->getId());
            $this->arguments = $arguments ?: $command->getArguments();
        } else {
            $this->commandID = strtoupper($command);
            $this->arguments = $arguments;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function matches($other)
    {
        if (!$other instanceof CommandInterface) {
            return false;
        }

        if ($this->commandID && strtoupper($other->getId()) !== $this->commandID) {
            return false;
        }

        if ($this->arguments !== null) {
            $otherArguments = $other->getArguments();

            if (count($this->arguments) !== count($otherArguments)) {
                return false;
            }

            for ($i = 0; $i < count($this->arguments); $i++) {
                if (((string) $this->arguments[$i]) !== ((string) $otherArguments[$i])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Improve output using diff when expected and actual arguments of a
     *       command do not match.
     */
    public function toString()
    {
        $string = 'is a Redis command';

        if ($this->commandID) {
            $string .= " with ID '{$this->commandID}'";
        }

        if ($this->arguments) {
            $string .= " and the following arguments:\n\n";
            $string .= PHPUnit_Util_Type::export($this->arguments);
        }

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    protected function failureDescription($other)
    {
        $string = is_object($other) ? get_class($other) : $other;

        return "$string {$this->toString()}";
    }
}
