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

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * The Dialog class provides helpers to interact with the user.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DialogHelper extends InputAwareHelper
{
    private $inputStream;
    private static $shell;
    private static $stty;

    /**
     * Asks the user to select a value.
     *
     * @param OutputInterface $output       An Output instance
     * @param string|array    $question     The question to ask
     * @param array           $choices      List of choices to pick from
     * @param bool|string     $default      The default answer if the user enters nothing
     * @param bool|int        $attempts Max number of times to ask before giving up (false by default, which means infinite)
     * @param string          $errorMessage Message which will be shown if invalid value from choice list would be picked
     * @param bool            $multiselect  Select more than one value separated by comma
     *
     * @return int|string|array     The selected value or values (the key of the choices array)
     *
     * @throws \InvalidArgumentException
     */
    public function select(OutputInterface $output, $question, $choices, $default = null, $attempts = false, $errorMessage = 'Value "%s" is invalid', $multiselect = false)
    {
        $width = max(array_map('strlen', array_keys($choices)));

        $messages = (array) $question;
        foreach ($choices as $key => $value) {
            $messages[] = sprintf("  [<info>%-${width}s</info>] %s", $key, $value);
        }

        $output->writeln($messages);

        $result = $this->askAndValidate($output, '> ', function ($picked) use ($choices, $errorMessage, $multiselect) {
            // Collapse all spaces.
            $selectedChoices = str_replace(" ", "", $picked);

            if ($multiselect) {
                // Check for a separated comma values
                if (!preg_match('/^[a-zA-Z0-9_-]+(?:,[a-zA-Z0-9_-]+)*$/', $selectedChoices, $matches)) {
                    throw new \InvalidArgumentException(sprintf($errorMessage, $picked));
                }
                $selectedChoices = explode(",", $selectedChoices);
            } else {
                $selectedChoices = array($picked);
            }

            $multiselectChoices = array();

            foreach ($selectedChoices as $value) {
                if (empty($choices[$value])) {
                    throw new \InvalidArgumentException(sprintf($errorMessage, $value));
                }
                array_push($multiselectChoices, $value);
            }

            if ($multiselect) {
                return $multiselectChoices;
            }

            return $picked;
        }, $attempts, $default);

        return $result;
    }

    /**
     * Asks a question to the user.
     *
     * @param OutputInterface $output       An Output instance
     * @param string|array    $question     The question to ask
     * @param string          $default      The default answer if none is given by the user
     * @param array           $autocomplete List of values to autocomplete
     *
     * @return string The user answer
     *
     * @throws \RuntimeException If there is no data to read in the input stream
     */
    public function ask(OutputInterface $output, $question, $default = null, array $autocomplete = null)
    {
        if ($this->input && !$this->input->isInteractive()) {
            return $default;
        }

        $output->write($question);

        $inputStream = $this->inputStream ?: STDIN;

        if (null === $autocomplete || !$this->hasSttyAvailable()) {
            $ret = fgets($inputStream, 4096);
            if (false === $ret) {
                throw new \RuntimeException('Aborted');
            }
            $ret = trim($ret);
        } else {
            $ret = '';

            $i = 0;
            $ofs = -1;
            $matches = $autocomplete;
            $numMatches = count($matches);

            $sttyMode = shell_exec('stty -g');

            // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
            shell_exec('stty -icanon -echo');

            // Add highlighted text style
            $output->getFormatter()->setStyle('hl', new OutputFormatterStyle('black', 'white'));

            // Read a keypress
            while (!feof($inputStream)) {
                $c = fread($inputStream, 1);

                // Backspace Character
                if ("\177" === $c) {
                    if (0 === $numMatches && 0 !== $i) {
                        $i--;
                        // Move cursor backwards
                        $output->write("\033[1D");
                    }

                    if ($i === 0) {
                        $ofs = -1;
                        $matches = $autocomplete;
                        $numMatches = count($matches);
                    } else {
                        $numMatches = 0;
                    }

                    // Pop the last character off the end of our string
                    $ret = substr($ret, 0, $i);
                } elseif ("\033" === $c) {
                    // Did we read an escape sequence?
                    $c .= fread($inputStream, 2);

                    // A = Up Arrow. B = Down Arrow
                    if (isset($c[2]) && ('A' === $c[2] || 'B' === $c[2])) {
                        if ('A' === $c[2] && -1 === $ofs) {
                            $ofs = 0;
                        }

                        if (0 === $numMatches) {
                            continue;
                        }

                        $ofs += ('A' === $c[2]) ? -1 : 1;
                        $ofs = ($numMatches + $ofs) % $numMatches;
                    }
                } elseif (ord($c) < 32) {
                    if ("\t" === $c || "\n" === $c) {
                        if ($numMatches > 0 && -1 !== $ofs) {
                            $ret = $matches[$ofs];
                            // Echo out remaining chars for current match
                            $output->write(substr($ret, $i));
                            $i = strlen($ret);
                        }

                        if ("\n" === $c) {
                            $output->write($c);
                            break;
                        }

                        $numMatches = 0;
                    }

                    continue;
                } else {
                    $output->write($c);
                    $ret .= $c;
                    $i++;

                    $numMatches = 0;
                    $ofs = 0;

                    foreach ($autocomplete as $value) {
                        // If typed characters match the beginning chunk of value (e.g. [AcmeDe]moBundle)
                        if (0 === strpos($value, $ret) && $i !== strlen($value)) {
                            $matches[$numMatches++] = $value;
                        }
                    }
                }

                // Erase characters from cursor to end of line
                $output->write("\033[K");

                if ($numMatches > 0 && -1 !== $ofs) {
                    // Save cursor position
                    $output->write("\0337");
                    // Write highlighted text
                    $output->write('<hl>'.substr($matches[$ofs], $i).'</hl>');
                    // Restore cursor position
                    $output->write("\0338");
                }
            }

            // Reset stty so it behaves normally again
            shell_exec(sprintf('stty %s', $sttyMode));
        }

        return strlen($ret) > 0 ? $ret : $default;
    }

    /**
     * Asks a confirmation to the user.
     *
     * The question will be asked until the user answers by nothing, yes, or no.
     *
     * @param OutputInterface $output   An Output instance
     * @param string|array    $question The question to ask
     * @param bool            $default  The default answer if the user enters nothing
     *
     * @return bool    true if the user has confirmed, false otherwise
     */
    public function askConfirmation(OutputInterface $output, $question, $default = true)
    {
        $answer = 'z';
        while ($answer && !in_array(strtolower($answer[0]), array('y', 'n'))) {
            $answer = $this->ask($output, $question);
        }

        if (false === $default) {
            return $answer && 'y' == strtolower($answer[0]);
        }

        return !$answer || 'y' == strtolower($answer[0]);
    }

    /**
     * Asks a question to the user, the response is hidden
     *
     * @param OutputInterface $output   An Output instance
     * @param string|array    $question The question
     * @param bool            $fallback In case the response can not be hidden, whether to fallback on non-hidden question or not
     *
     * @return string         The answer
     *
     * @throws \RuntimeException In case the fallback is deactivated and the response can not be hidden
     */
    public function askHiddenResponse(OutputInterface $output, $question, $fallback = true)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $exe = __DIR__.'/../Resources/bin/hiddeninput.exe';

            // handle code running from a phar
            if ('phar:' === substr(__FILE__, 0, 5)) {
                $tmpExe = sys_get_temp_dir().'/hiddeninput.exe';
                copy($exe, $tmpExe);
                $exe = $tmpExe;
            }

            $output->write($question);
            $value = rtrim(shell_exec($exe));
            $output->writeln('');

            if (isset($tmpExe)) {
                unlink($tmpExe);
            }

            return $value;
        }

        if ($this->hasSttyAvailable()) {
            $output->write($question);

            $sttyMode = shell_exec('stty -g');

            shell_exec('stty -echo');
            $value = fgets($this->inputStream ?: STDIN, 4096);
            shell_exec(sprintf('stty %s', $sttyMode));

            if (false === $value) {
                throw new \RuntimeException('Aborted');
            }

            $value = trim($value);
            $output->writeln('');

            return $value;
        }

        if (false !== $shell = $this->getShell()) {
            $output->write($question);
            $readCmd = $shell === 'csh' ? 'set mypassword = $<' : 'read -r mypassword';
            $command = sprintf("/usr/bin/env %s -c 'stty -echo; %s; stty echo; echo \$mypassword'", $shell, $readCmd);
            $value = rtrim(shell_exec($command));
            $output->writeln('');

            return $value;
        }

        if ($fallback) {
            return $this->ask($output, $question);
        }

        throw new \RuntimeException('Unable to hide the response');
    }

    /**
     * Asks for a value and validates the response.
     *
     * The validator receives the data to validate. It must return the
     * validated data when the data is valid and throw an exception
     * otherwise.
     *
     * @param OutputInterface $output       An Output instance
     * @param string|array    $question     The question to ask
     * @param callable        $validator    A PHP callback
     * @param int|false       $attempts     Max number of times to ask before giving up (false by default, which means infinite)
     * @param string          $default      The default answer if none is given by the user
     * @param array           $autocomplete List of values to autocomplete
     *
     * @return mixed
     *
     * @throws \Exception When any of the validators return an error
     */
    public function askAndValidate(OutputInterface $output, $question, $validator, $attempts = false, $default = null, array $autocomplete = null)
    {
        $that = $this;

        $interviewer = function () use ($output, $question, $default, $autocomplete, $that) {
            return $that->ask($output, $question, $default, $autocomplete);
        };

        return $this->validateAttempts($interviewer, $output, $validator, $attempts);
    }

    /**
     * Asks for a value, hide and validates the response.
     *
     * The validator receives the data to validate. It must return the
     * validated data when the data is valid and throw an exception
     * otherwise.
     *
     * @param OutputInterface $output    An Output instance
     * @param string|array    $question  The question to ask
     * @param callable        $validator A PHP callback
     * @param int|false       $attempts  Max number of times to ask before giving up (false by default, which means infinite)
     * @param bool            $fallback  In case the response can not be hidden, whether to fallback on non-hidden question or not
     *
     * @return string         The response
     *
     * @throws \Exception        When any of the validators return an error
     * @throws \RuntimeException In case the fallback is deactivated and the response can not be hidden
     *
     */
    public function askHiddenResponseAndValidate(OutputInterface $output, $question, $validator, $attempts = false, $fallback = true)
    {
        $that = $this;

        $interviewer = function () use ($output, $question, $fallback, $that) {
            return $that->askHiddenResponse($output, $question, $fallback);
        };

        return $this->validateAttempts($interviewer, $output, $validator, $attempts);
    }

    /**
     * Sets the input stream to read from when interacting with the user.
     *
     * This is mainly useful for testing purpose.
     *
     * @param resource $stream The input stream
     */
    public function setInputStream($stream)
    {
        $this->inputStream = $stream;
    }

    /**
     * Returns the helper's input stream
     *
     * @return string
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dialog';
    }

    /**
     * Return a valid Unix shell
     *
     * @return string|bool     The valid shell name, false in case no valid shell is found
     */
    private function getShell()
    {
        if (null !== self::$shell) {
            return self::$shell;
        }

        self::$shell = false;

        if (file_exists('/usr/bin/env')) {
            // handle other OSs with bash/zsh/ksh/csh if available to hide the answer
            $test = "/usr/bin/env %s -c 'echo OK' 2> /dev/null";
            foreach (array('bash', 'zsh', 'ksh', 'csh') as $sh) {
                if ('OK' === rtrim(shell_exec(sprintf($test, $sh)))) {
                    self::$shell = $sh;
                    break;
                }
            }
        }

        return self::$shell;
    }

    private function hasSttyAvailable()
    {
        if (null !== self::$stty) {
            return self::$stty;
        }

        exec('stty 2>&1', $output, $exitcode);

        return self::$stty = $exitcode === 0;
    }

    /**
     * Validate an attempt
     *
     * @param callable         $interviewer  A callable that will ask for a question and return the result
     * @param OutputInterface  $output       An Output instance
     * @param callable         $validator    A PHP callback
     * @param int|false        $attempts     Max number of times to ask before giving up ; false will ask infinitely
     *
     * @return string   The validated response
     *
     * @throws \Exception In case the max number of attempts has been reached and no valid response has been given
     */
    private function validateAttempts($interviewer, OutputInterface $output, $validator, $attempts)
    {
        $error = null;
        while (false === $attempts || $attempts--) {
            if (null !== $error) {
                $output->writeln($this->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
            }

            try {
                return call_user_func($validator, $interviewer());
            } catch (\Exception $error) {
            }
        }

        throw $error;
    }
}
