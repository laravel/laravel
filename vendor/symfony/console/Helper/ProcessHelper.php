<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * The ProcessHelper class provides helpers to run external processes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ProcessHelper extends Helper
{
    /**
     * Runs an external process.
     *
     * @param array|Process $cmd      An instance of Process or an array of the command and arguments
     * @param callable|null $callback A PHP callback to run whenever there is some
     *                                output available on STDOUT or STDERR
     */
    public function run(OutputInterface $output, array|Process $cmd, ?string $error = null, ?callable $callback = null, int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE): Process
    {
        if (!class_exists(Process::class)) {
            throw new \LogicException('The ProcessHelper cannot be run as the Process component is not installed. Try running "compose require symfony/process".');
        }

        if ($output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        $formatter = $this->getHelperSet()->get('debug_formatter');

        if ($cmd instanceof Process) {
            $cmd = [$cmd];
        }

        if (\is_string($cmd[0] ?? null)) {
            $process = new Process($cmd);
            $cmd = [];
        } elseif (($cmd[0] ?? null) instanceof Process) {
            $process = $cmd[0];
            unset($cmd[0]);
        } else {
            throw new \InvalidArgumentException(\sprintf('Invalid command provided to "%s()": the command should be an array whose first element is either the path to the binary to run or a "Process" object.', __METHOD__));
        }

        if ($verbosity <= $output->getVerbosity()) {
            $output->write($formatter->start(spl_object_hash($process), $this->escapeString($process->getCommandLine())));
        }

        if ($output->isDebug()) {
            $callback = $this->wrapCallback($output, $process, $callback);
        }

        $process->run($callback, $cmd);

        if ($verbosity <= $output->getVerbosity()) {
            $message = $process->isSuccessful() ? 'Command ran successfully' : \sprintf('%s Command did not run successfully', $process->getExitCode());
            $output->write($formatter->stop(spl_object_hash($process), $message, $process->isSuccessful()));
        }

        if (!$process->isSuccessful() && null !== $error) {
            $output->writeln(\sprintf('<error>%s</error>', $this->escapeString($error)));
        }

        return $process;
    }

    /**
     * Runs the process.
     *
     * This is identical to run() except that an exception is thrown if the process
     * exits with a non-zero exit code.
     *
     * @param array|Process $cmd      An instance of Process or a command to run
     * @param callable|null $callback A PHP callback to run whenever there is some
     *                                output available on STDOUT or STDERR
     *
     * @throws ProcessFailedException
     *
     * @see run()
     */
    public function mustRun(OutputInterface $output, array|Process $cmd, ?string $error = null, ?callable $callback = null, int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE): Process
    {
        $process = $this->run($output, $cmd, $error, $callback, $verbosity);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }

    /**
     * Wraps a Process callback to add debugging output.
     */
    public function wrapCallback(OutputInterface $output, Process $process, ?callable $callback = null): callable
    {
        if ($output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        $formatter = $this->getHelperSet()->get('debug_formatter');

        return function ($type, $buffer) use ($output, $process, $callback, $formatter) {
            $output->write($formatter->progress(spl_object_hash($process), $this->escapeString($buffer), Process::ERR === $type));

            if (null !== $callback) {
                $callback($type, $buffer);
            }
        };
    }

    private function escapeString(string $str): string
    {
        return str_replace('<', '\\<', $str);
    }

    public function getName(): string
    {
        return 'process';
    }
}
