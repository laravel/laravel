<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tester;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Eases the testing of console applications.
 *
 * When testing an application, don't forget to disable the auto exit flag:
 *
 *     $application = new Application();
 *     $application->setAutoExit(false);
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ApplicationTester
{
    use TesterTrait;

    public function __construct(
        private Application $application,
    ) {
    }

    /**
     * Executes the application.
     *
     * Available options:
     *
     *  * interactive:               Sets the input interactive flag
     *  * decorated:                 Sets the output decorated flag
     *  * verbosity:                 Sets the output verbosity flag
     *  * capture_stderr_separately: Make output of stdOut and stdErr separately available
     *
     * @return int The command exit code
     */
    public function run(array $input, array $options = []): int
    {
        $this->input = new ArrayInput($input);
        if (isset($options['interactive'])) {
            $this->input->setInteractive($options['interactive']);
        }

        if ($this->inputs) {
            $this->input->setStream(self::createStream($this->inputs));
        }

        $this->initOutput($options);

        // Temporarily clear SHELL_VERBOSITY to prevent Application::configureIO
        // from overriding the interactive and verbosity settings set above
        $prevShellVerbosity = [getenv('SHELL_VERBOSITY'), $_ENV['SHELL_VERBOSITY'] ?? false, $_SERVER['SHELL_VERBOSITY'] ?? false];
        if (\function_exists('putenv')) {
            @putenv('SHELL_VERBOSITY');
        }
        unset($_ENV['SHELL_VERBOSITY'], $_SERVER['SHELL_VERBOSITY']);

        try {
            return $this->statusCode = $this->application->run($this->input, $this->output);
        } finally {
            if (false !== $prevShellVerbosity[0]) {
                if (\function_exists('putenv')) {
                    @putenv('SHELL_VERBOSITY='.$prevShellVerbosity[0]);
                }
            }
            if (false !== $prevShellVerbosity[1]) {
                $_ENV['SHELL_VERBOSITY'] = $prevShellVerbosity[1];
            }
            if (false !== $prevShellVerbosity[2]) {
                $_SERVER['SHELL_VERBOSITY'] = $prevShellVerbosity[2];
            }
        }
    }
}
