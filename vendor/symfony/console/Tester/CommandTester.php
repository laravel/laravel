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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Eases the testing of console commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class CommandTester
{
    use TesterTrait;

    private Command $command;

    public function __construct(
        callable|Command $command,
    ) {
        $this->command = $command instanceof Command ? $command : new Command(null, $command);
    }

    /**
     * Executes the command.
     *
     * Available execution options:
     *
     *  * interactive:               Sets the input interactive flag
     *  * decorated:                 Sets the output decorated flag
     *  * verbosity:                 Sets the output verbosity flag
     *  * capture_stderr_separately: Make output of stdOut and stdErr separately available
     *
     * @param array $input   An array of command arguments and options
     * @param array $options An array of execution options
     *
     * @return int The command exit code
     */
    public function execute(array $input, array $options = []): int
    {
        // set the command name automatically if the application requires
        // this argument and no command name was passed
        if (!isset($input['command'])
            && (null !== $application = $this->command->getApplication())
            && $application->getDefinition()->hasArgument('command')
        ) {
            $input = array_merge(['command' => $this->command->getName()], $input);
        }

        $this->input = new ArrayInput($input);
        // Use an in-memory input stream even if no inputs are set so that QuestionHelper::ask() does not rely on the blocking STDIN.
        $this->input->setStream(self::createStream($this->inputs));

        if (isset($options['interactive'])) {
            $this->input->setInteractive($options['interactive']);
        }

        if (!isset($options['decorated'])) {
            $options['decorated'] = false;
        }

        $this->initOutput($options);

        return $this->statusCode = $this->command->run($this->input, $this->output);
    }
}
