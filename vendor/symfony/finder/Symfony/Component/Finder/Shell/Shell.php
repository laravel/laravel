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
class Shell
{
    const TYPE_UNIX    = 1;
    const TYPE_DARWIN  = 2;
    const TYPE_CYGWIN  = 3;
    const TYPE_WINDOWS = 4;
    const TYPE_BSD     = 5;

    /**
     * @var string|null
     */
    private $type;

    /**
     * Returns guessed OS type.
     *
     * @return int
     */
    public function getType()
    {
        if (null === $this->type) {
            $this->type = $this->guessType();
        }

        return $this->type;
    }

    /**
     * Tests if a command is available.
     *
     * @param string $command
     *
     * @return bool
     */
    public function testCommand($command)
    {
        if (!function_exists('exec')) {
            return false;
        }

        // todo: find a better way (command could not be available)
        $testCommand = 'which ';
        if (self::TYPE_WINDOWS === $this->type) {
            $testCommand = 'where ';
        }

        $command = escapeshellcmd($command);

        exec($testCommand.$command, $output, $code);

        return 0 === $code && count($output) > 0;
    }

    /**
     * Guesses OS type.
     *
     * @return int
     */
    private function guessType()
    {
        $os = strtolower(PHP_OS);

        if (false !== strpos($os, 'cygwin')) {
            return self::TYPE_CYGWIN;
        }

        if (false !== strpos($os, 'darwin')) {
            return self::TYPE_DARWIN;
        }

        if (false !== strpos($os, 'bsd')) {
            return self::TYPE_BSD;
        }

        if (0 === strpos($os, 'win')) {
            return self::TYPE_WINDOWS;
        }

        return self::TYPE_UNIX;
    }
}
