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

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * A Shell wraps an Application to add shell capabilities to it.
 *
 * Support for history and completion only works with a PHP compiled
 * with readline support (either --with-readline or --with-libedit)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class Shell
{
    private $application;
    private $history;
    private $output;
    private $hasReadline;
    private $processIsolation = false;

    /**
     * Constructor.
     *
     * If there is no readline support for the current PHP executable
     * a \RuntimeException exception is thrown.
     *
     * @param Application $application An application instance
     */
    public function __construct(Application $application)
    {
        $this->hasReadline = function_exists('readline');
        $this->application = $application;
        $this->history = getenv('HOME').'/.history_'.$application->getName();
        $this->output = new ConsoleOutput();
    }

    /**
     * Runs the shell.
     */
    public function run()
    {
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(true);

        if ($this->hasReadline) {
            readline_read_history($this->history);
            readline_completion_function(array($this, 'autocompleter'));
        }

        $this->output->writeln($this->getHeader());
        $php = null;
        if ($this->processIsolation) {
            $finder = new PhpExecutableFinder();
            $php = $finder->find();
            $this->output->writeln(<<<EOF
<info>Running with process isolation, you should consider this:</info>
  * each command is executed as separate process,
  * commands don't support interactivity, all params must be passed explicitly,
  * commands output is not colorized.

EOF
            );
        }

        while (true) {
            $command = $this->readline();

            if (false === $command) {
                $this->output->writeln("\n");

                break;
            }

            if ($this->hasReadline) {
                readline_add_history($command);
                readline_write_history($this->history);
            }

            if ($this->processIsolation) {
                $pb = new ProcessBuilder();

                $process = $pb
                    ->add($php)
                    ->add($_SERVER['argv'][0])
                    ->add($command)
                    ->inheritEnvironmentVariables(true)
                    ->getProcess()
                ;

                $output = $this->output;
                $process->run(function ($type, $data) use ($output) {
                    $output->writeln($data);
                });

                $ret = $process->getExitCode();
            } else {
                $ret = $this->application->run(new StringInput($command), $this->output);
            }

            if (0 !== $ret) {
                $this->output->writeln(sprintf('<error>The command terminated with an error status (%s)</error>', $ret));
            }
        }
    }

    /**
     * Returns the shell header.
     *
     * @return string The header string
     */
    protected function getHeader()
    {
        return <<<EOF

Welcome to the <info>{$this->application->getName()}</info> shell (<comment>{$this->application->getVersion()}</comment>).

At the prompt, type <comment>help</comment> for some help,
or <comment>list</comment> to get a list of available commands.

To exit the shell, type <comment>^D</comment>.

EOF;
    }

    /**
     * Renders a prompt.
     *
     * @return string The prompt
     */
    protected function getPrompt()
    {
        // using the formatter here is required when using readline
        return $this->output->getFormatter()->format($this->application->getName().' > ');
    }

    protected function getOutput()
    {
        return $this->output;
    }

    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Tries to return autocompletion for the current entered text.
     *
     * @param string $text The last segment of the entered text
     *
     * @return bool|array    A list of guessed strings or true
     */
    private function autocompleter($text)
    {
        $info = readline_info();
        $text = substr($info['line_buffer'], 0, $info['end']);

        if ($info['point'] !== $info['end']) {
            return true;
        }

        // task name?
        if (false === strpos($text, ' ') || !$text) {
            return array_keys($this->application->all());
        }

        // options and arguments?
        try {
            $command = $this->application->find(substr($text, 0, strpos($text, ' ')));
        } catch (\Exception $e) {
            return true;
        }

        $list = array('--help');
        foreach ($command->getDefinition()->getOptions() as $option) {
            $list[] = '--'.$option->getName();
        }

        return $list;
    }

    /**
     * Reads a single line from standard input.
     *
     * @return string The single line from standard input
     */
    private function readline()
    {
        if ($this->hasReadline) {
            $line = readline($this->getPrompt());
        } else {
            $this->output->write($this->getPrompt());
            $line = fgets(STDIN, 1024);
            $line = (!$line && strlen($line) == 0) ? false : rtrim($line);
        }

        return $line;
    }

    public function getProcessIsolation()
    {
        return $this->processIsolation;
    }

    public function setProcessIsolation($processIsolation)
    {
        $this->processIsolation = (bool) $processIsolation;

        if ($this->processIsolation && !class_exists('Symfony\\Component\\Process\\Process')) {
            throw new \RuntimeException('Unable to isolate processes as the Symfony Process Component is not installed.');
        }
    }
}
