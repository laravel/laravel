<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Shell;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class Command
{
    /**
     * @var Command|null
     */
    private $parent;

    /**
     * @var array
     */
    private $bits = array();

    /**
     * @var array
     */
    private $labels = array();

    /**
     * @var \Closure|null
     */
    private $errorHandler;

    /**
     * Constructor.
     *
     * @param Command|null $parent Parent command
     */
    public function __construct(Command $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Returns command as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->join();
    }

    /**
     * Creates a new Command instance.
     *
     * @param Command|null $parent Parent command
     *
     * @return Command New Command instance
     */
    public static function create(Command $parent = null)
    {
        return new self($parent);
    }

    /**
     * Escapes special chars from input.
     *
     * @param string $input A string to escape
     *
     * @return string The escaped string
     */
    public static function escape($input)
    {
        return escapeshellcmd($input);
    }

    /**
     * Quotes input.
     *
     * @param string $input An argument string
     *
     * @return string The quoted string
     */
    public static function quote($input)
    {
        return escapeshellarg($input);
    }

    /**
     * Appends a string or a Command instance.
     *
     * @param string|Command $bit
     *
     * @return Command The current Command instance
     */
    public function add($bit)
    {
        $this->bits[] = $bit;

        return $this;
    }

    /**
     * Prepends a string or a command instance.
     *
     * @param string|Command $bit
     *
     * @return Command The current Command instance
     */
    public function top($bit)
    {
        array_unshift($this->bits, $bit);

        foreach ($this->labels as $label => $index) {
            $this->labels[$label] += 1;
        }

        return $this;
    }

    /**
     * Appends an argument, will be quoted.
     *
     * @param string $arg
     *
     * @return Command The current Command instance
     */
    public function arg($arg)
    {
        $this->bits[] = self::quote($arg);

        return $this;
    }

    /**
     * Appends escaped special command chars.
     *
     * @param string $esc
     *
     * @return Command The current Command instance
     */
    public function cmd($esc)
    {
        $this->bits[] = self::escape($esc);

        return $this;
    }

    /**
     * Inserts a labeled command to feed later.
     *
     * @param string $label The unique label
     *
     * @return Command The current Command instance
     *
     * @throws \RuntimeException If label already exists
     */
    public function ins($label)
    {
        if (isset($this->labels[$label])) {
            throw new \RuntimeException(sprintf('Label "%s" already exists.', $label));
        }

        $this->bits[] = self::create($this);
        $this->labels[$label] = count($this->bits)-1;

        return $this->bits[$this->labels[$label]];
    }

    /**
     * Retrieves a previously labeled command.
     *
     * @param string $label
     *
     * @return Command The labeled command
     *
     * @throws \RuntimeException
     */
    public function get($label)
    {
        if (!isset($this->labels[$label])) {
            throw new \RuntimeException(sprintf('Label "%s" does not exist.', $label));
        }

        return $this->bits[$this->labels[$label]];
    }

    /**
     * Returns parent command (if any).
     *
     * @return Command Parent command
     *
     * @throws \RuntimeException If command has no parent
     */
    public function end()
    {
        if (null === $this->parent) {
            throw new \RuntimeException('Calling end on root command doesn\'t make sense.');
        }

        return $this->parent;
    }

    /**
     * Counts bits stored in command.
     *
     * @return int The bits count
     */
    public function length()
    {
        return count($this->bits);
    }

    /**
     * @param \Closure $errorHandler
     *
     * @return Command
     */
    public function setErrorHandler(\Closure $errorHandler)
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /**
     * @return \Closure|null
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

    /**
     * Executes current command.
     *
     * @return array The command result
     *
     * @throws \RuntimeException
     */
    public function execute()
    {
        if (null === $this->errorHandler) {
            exec($this->join(), $output);
        } else {
            $process = proc_open($this->join(), array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
            $output = preg_split('~(\r\n|\r|\n)~', stream_get_contents($pipes[1]), -1, PREG_SPLIT_NO_EMPTY);

            if ($error = stream_get_contents($pipes[2])) {
                call_user_func($this->errorHandler, $error);
            }

            proc_close($process);
        }

        return $output ?: array();
    }

    /**
     * Joins bits.
     *
     * @return string
     */
    public function join()
    {
        return implode(' ', array_filter(
            array_map(function ($bit) {
                return $bit instanceof Command ? $bit->join() : ($bit ?: null);
            }, $this->bits),
            function ($bit) { return null !== $bit; }
        ));
    }

    /**
     * Insert a string or a Command instance before the bit at given position $index (index starts from 0).
     *
     * @param string|Command $bit
     * @param int            $index
     *
     * @return Command The current Command instance
     */
    public function addAtIndex($bit, $index)
    {
        array_splice($this->bits, $index, 0, $bit);

        return $this;
    }
}
