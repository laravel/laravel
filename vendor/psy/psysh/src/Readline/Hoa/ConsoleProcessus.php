<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Psy\Readline\Hoa;

/**
 * Manipulate a processus as a stream.
 */
class ConsoleProcessus extends Stream implements StreamIn, StreamOut, StreamPathable
{
    /**
     * Signal: terminal line hangup (terminate process).
     */
    const SIGHUP = 1;

    /**
     * Signal: interrupt program (terminate process).
     */
    const SIGINT = 2;

    /**
     * Signal: quit program (create core image).
     */
    const SIGQUIT = 3;

    /**
     * Signal: illegal instruction (create core image).
     */
    const SIGILL = 4;

    /**
     * Signal: trace trap (create core image).
     */
    const SIGTRAP = 5;

    /**
     * Signal: abort program, formerly SIGIOT (create core image).
     */
    const SIGABRT = 6;

    /**
     * Signal: emulate instruction executed (create core image).
     */
    const SIGEMT = 7;

    /**
     * Signal: floating-point exception (create core image).
     */
    const SIGFPE = 8;

    /**
     * Signal: kill program (terminate process).
     */
    const SIGKILL = 9;

    /**
     * Signal: bus error.
     */
    const SIGBUS = 10;

    /**
     * Signal: segmentation violation (create core image).
     */
    const SIGSEGV = 11;

    /**
     * Signal: non-existent system call invoked (create core image).
     */
    const SIGSYS = 12;

    /**
     * Signal: write on a pipe with no reader (terminate process).
     */
    const SIGPIPE = 13;

    /**
     * Signal: real-time timer expired (terminate process).
     */
    const SIGALRM = 14;

    /**
     * Signal: software termination signal (terminate process).
     */
    const SIGTERM = 15;

    /**
     * Signal: urgent condition present on socket (discard signal).
     */
    const SIGURG = 16;

    /**
     * Signal: stop, cannot be caught or ignored  (stop proces).
     */
    const SIGSTOP = 17;

    /**
     * Signal: stop signal generated from keyboard (stop process).
     */
    const SIGTSTP = 18;

    /**
     * Signal: continue after stop (discard signal).
     */
    const SIGCONT = 19;

    /**
     * Signal: child status has changed (discard signal).
     */
    const SIGCHLD = 20;

    /**
     * Signal: background read attempted from control terminal (stop process).
     */
    const SIGTTIN = 21;

    /**
     * Signal: background write attempted to control terminal (stop process).
     */
    const SIGTTOU = 22;

    /**
     * Signal: I/O is possible on a descriptor, see fcntl(2) (discard signal).
     */
    const SIGIO = 23;

    /**
     * Signal: cpu time limit exceeded, see setrlimit(2) (terminate process).
     */
    const SIGXCPU = 24;

    /**
     * Signal: file size limit exceeded, see setrlimit(2) (terminate process).
     */
    const SIGXFSZ = 25;

    /**
     * Signal: virtual time alarm, see setitimer(2) (terminate process).
     */
    const SIGVTALRM = 26;

    /**
     * Signal: profiling timer alarm, see setitimer(2) (terminate process).
     */
    const SIGPROF = 27;

    /**
     * Signal: Window size change (discard signal).
     */
    const SIGWINCH = 28;

    /**
     * Signal: status request from keyboard (discard signal).
     */
    const SIGINFO = 29;

    /**
     * Signal: User defined signal 1 (terminate process).
     */
    const SIGUSR1 = 30;

    /**
     * Signal: User defined signal 2 (terminate process).
     */
    const SIGUSR2 = 31;

    /**
     * Command name.
     */
    protected $_command = null;

    /**
     * Command options (options => value, or input).
     */
    protected $_options = [];

    /**
     * Current working directory.
     */
    protected $_cwd = null;

    /**
     * Environment.
     */
    protected $_environment = null;

    /**
     * Timeout.
     */
    protected $_timeout = 30;

    /**
     * Descriptor.
     */
    protected $_descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    /**
     * Pipe descriptors of the processus.
     */
    protected $_pipes = null;

    /**
     * Seekability of pipes.
     */
    protected $_seekable = [];

    /**
     * Start a processus.
     */
    public function __construct(
        string $command,
        ?array $options = null,
        ?array $descriptors = null,
        ?string $cwd = null,
        ?array $environment = null,
        int $timeout = 30
    ) {
        $this->setCommand($command);

        if (null !== $options) {
            $this->setOptions($options);
        }

        if (null !== $descriptors) {
            $this->_descriptors = [];

            foreach ($descriptors as $descriptor => $nature) {
                if (isset($this->_descriptors[$descriptor])) {
                    throw new ConsoleException('Pipe descriptor %d already exists, cannot '.'redefine it.', 0, $descriptor);
                }

                $this->_descriptors[$descriptor] = $nature;
            }
        }

        $this->setCwd($cwd ?: \getcwd());

        if (null !== $environment) {
            $this->setEnvironment($environment);
        }

        $this->setTimeout($timeout);
        parent::__construct($this->getCommandLine(), null, true);
        $this->getListener()->addIds(['input', 'output', 'timeout', 'start', 'stop']);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     */
    protected function &_open(string $streamName, ?StreamContext $context = null)
    {
        $out = @\proc_open(
            $streamName,
            $this->_descriptors,
            $this->_pipes,
            $this->getCwd(),
            $this->getEnvironment()
        );

        if (false === $out) {
            throw new ConsoleException('Something wrong happen when running %s.', 1, $streamName);
        }

        return $out;
    }

    /**
     * Close the current stream.
     */
    protected function _close(): bool
    {
        foreach ($this->_pipes as $pipe) {
            @\fclose($pipe);
        }

        return (bool) @\proc_close($this->getStream());
    }

    /**
     * Run the process and fire events (amongst start, stop, input, output and
     * timeout).
     * If an event returns false, it will close the current pipe.
     * For a simple run without firing events, use the $this->open() method.
     */
    public function run()
    {
        if (false === $this->isOpened()) {
            $this->open();
        } else {
            $this->_close();
            $this->_setStream($this->_open(
                $this->getStreamName(),
                $this->getStreamContext()
            ));
        }

        $this->getListener()->fire('start', new EventBucket());

        $_read = [];
        $_write = [];
        $_except = [];

        foreach ($this->_pipes as $p => $pipe) {
            switch ($this->_descriptors[$p][1]) {
                case 'r':
                    \stream_set_blocking($pipe, false);
                    $_write[] = $pipe;

                    break;

                case 'w':
                case 'a':
                    \stream_set_blocking($pipe, true);
                    $_read[] = $pipe;

                    break;
            }
        }

        while (true) {
            foreach ($_read as $i => $r) {
                if (false === \is_resource($r)) {
                    unset($_read[$i]);
                }
            }

            foreach ($_write as $i => $w) {
                if (false === \is_resource($w)) {
                    unset($_write[$i]);
                }
            }

            foreach ($_except as $i => $e) {
                if (false === \is_resource($e)) {
                    unset($_except[$i]);
                }
            }

            if (empty($_read) && empty($_write) && empty($_except)) {
                break;
            }

            $read = $_read;
            $write = $_write;
            $except = $_except;
            $select = \stream_select($read, $write, $except, $this->getTimeout());

            if (0 === $select) {
                $this->getListener()->fire('timeout', new EventBucket());

                break;
            }

            foreach ($read as $i => $_r) {
                $pipe = \array_search($_r, $this->_pipes);
                $line = $this->readLine($pipe);

                if (false === $line) {
                    $result = [false];
                } else {
                    $result = $this->getListener()->fire(
                        'output',
                        new EventBucket([
                            'pipe' => $pipe,
                            'line' => $line,
                        ])
                    );
                }

                if (true === \feof($_r) || \in_array(false, $result, true)) {
                    \fclose($_r);
                    unset($_read[$i]);

                    break;
                }
            }

            foreach ($write as $j => $_w) {
                $result = $this->getListener()->fire(
                    'input',
                    new EventBucket([
                        'pipe' => \array_search($_w, $this->_pipes),
                    ])
                );

                if (true === \feof($_w) || \in_array(false, $result, true)) {
                    \fclose($_w);
                    unset($_write[$j]);
                }
            }

            if (empty($_read)) {
                break;
            }
        }

        $this->getListener()->fire('stop', new EventBucket());

        return;
    }

    /**
     * Get pipe resource.
     */
    protected function getPipe(int $pipe)
    {
        if (!isset($this->_pipes[$pipe])) {
            throw new ConsoleException('Pipe descriptor %d does not exist, cannot read from it.', 2, $pipe);
        }

        return $this->_pipes[$pipe];
    }

    /**
     * Check if a pipe is seekable or not.
     */
    protected function isPipeSeekable(int $pipe): bool
    {
        if (!isset($this->_seekable[$pipe])) {
            $_pipe = $this->getPipe($pipe);
            $data = \stream_get_meta_data($_pipe);
            $this->_seekable[$pipe] = $data['seekable'];
        }

        return $this->_seekable[$pipe];
    }

    /**
     * Test for end-of-file.
     */
    public function eof(int $pipe = 1): bool
    {
        return \feof($this->getPipe($pipe));
    }

    /**
     * Read n characters.
     */
    public function read(int $length, int $pipe = 1)
    {
        if (0 > $length) {
            throw new ConsoleException('Length must be greater than 0, given %d.', 3, $length);
        }

        return \fread($this->getPipe($pipe), $length);
    }

    /**
     * Alias of $this->read().
     */
    public function readString(int $length, int $pipe = 1)
    {
        return $this->read($length, $pipe);
    }

    /**
     * Read a character.
     */
    public function readCharacter(int $pipe = 1)
    {
        return \fgetc($this->getPipe($pipe));
    }

    /**
     * Read a boolean.
     */
    public function readBoolean(int $pipe = 1)
    {
        return (bool) $this->read(1, $pipe);
    }

    /**
     * Read an integer.
     */
    public function readInteger(int $length = 1, int $pipe = 1)
    {
        return (int) $this->read($length, $pipe);
    }

    /**
     * Read a float.
     */
    public function readFloat(int $length = 1, int $pipe = 1)
    {
        return (float) $this->read($length, $pipe);
    }

    /**
     * Read an array.
     * Alias of the $this->scanf() method.
     */
    public function readArray(?string $format = null, int $pipe = 1)
    {
        return $this->scanf($format, $pipe);
    }

    /**
     * Read a line.
     */
    public function readLine(int $pipe = 1)
    {
        return \stream_get_line($this->getPipe($pipe), 1 << 15, "\n");
    }

    /**
     * Read all, i.e. read as much as possible.
     */
    public function readAll(int $offset = -1, int $pipe = 1)
    {
        $_pipe = $this->getPipe($pipe);

        if (true === $this->isPipeSeekable($pipe)) {
            $offset += \ftell($_pipe);
        } else {
            $offset = -1;
        }

        return \stream_get_contents($_pipe, -1, $offset);
    }

    /**
     * Parse input from a stream according to a format.
     */
    public function scanf(string $format, int $pipe = 1): array
    {
        return \fscanf($this->getPipe($pipe), $format);
    }

    /**
     * Write n characters.
     */
    public function write(string $string, int $length, int $pipe = 0)
    {
        if (0 > $length) {
            throw new ConsoleException('Length must be greater than 0, given %d.', 4, $length);
        }

        return \fwrite($this->getPipe($pipe), $string, $length);
    }

    /**
     * Write a string.
     */
    public function writeString(string $string, int $pipe = 0)
    {
        $string = (string) $string;

        return $this->write($string, \strlen($string), $pipe);
    }

    /**
     * Write a character.
     */
    public function writeCharacter(string $char, int $pipe = 0)
    {
        return $this->write((string) $char[0], 1, $pipe);
    }

    /**
     * Write a boolean.
     */
    public function writeBoolean(bool $boolean, int $pipe = 0)
    {
        return $this->write((string) (bool) $boolean, 1, $pipe);
    }

    /**
     * Write an integer.
     */
    public function writeInteger(int $integer, int $pipe = 0)
    {
        $integer = (string) (int) $integer;

        return $this->write($integer, \strlen($integer), $pipe);
    }

    /**
     * Write a float.
     */
    public function writeFloat(float $float, int $pipe = 0)
    {
        $float = (string) (float) $float;

        return $this->write($float, \strlen($float), $pipe);
    }

    /**
     * Write an array.
     */
    public function writeArray(array $array, int $pipe = 0)
    {
        $array = \var_export($array, true);

        return $this->write($array, \strlen($array), $pipe);
    }

    /**
     * Write a line.
     */
    public function writeLine(string $line, int $pipe = 0)
    {
        if (false === $n = \strpos($line, "\n")) {
            return $this->write($line."\n", \strlen($line) + 1, $pipe);
        }

        ++$n;

        return $this->write(\substr($line, 0, $n), $n, $pipe);
    }

    /**
     * Write all, i.e. as much as possible.
     */
    public function writeAll(string $string, int $pipe = 0)
    {
        return $this->write($string, \strlen($string), $pipe);
    }

    /**
     * Truncate a file to a given length.
     */
    public function truncate(int $size, int $pipe = 0): bool
    {
        return \ftruncate($this->getPipe($pipe), $size);
    }

    /**
     * Get filename component of path.
     */
    public function getBasename(): string
    {
        return \basename($this->getCommand());
    }

    /**
     * Get directory name component of path.
     */
    public function getDirname(): string
    {
        return \dirname($this->getCommand());
    }

    /**
     * Get status.
     */
    public function getStatus(): array
    {
        return \proc_get_status($this->getStream());
    }

    /**
     * Get exit code (alias of $this->getStatus()['exitcode']);.
     */
    public function getExitCode(): int
    {
        $handle = $this->getStatus();

        return $handle['exitcode'];
    }

    /**
     * Whether the processus have ended successfully.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return 0 === $this->getExitCode();
    }

    /**
     * Terminate the process.
     *
     * Valid signals are self::SIGHUP, SIGINT, SIGQUIT, SIGABRT, SIGKILL,
     * SIGALRM and SIGTERM.
     */
    public function terminate(int $signal = self::SIGTERM): bool
    {
        return \proc_terminate($this->getStream(), $signal);
    }

    /**
     * Set command name.
     */
    protected function setCommand(string $command)
    {
        $old = $this->_command;
        $this->_command = \escapeshellcmd($command);

        return $old;
    }

    /**
     * Get command name.
     */
    public function getCommand()
    {
        return $this->_command;
    }

    /**
     * Set command options.
     */
    protected function setOptions(array $options): array
    {
        foreach ($options as &$option) {
            $option = \escapeshellarg($option);
        }

        $old = $this->_options;
        $this->_options = $options;

        return $old;
    }

    /**
     * Get options.
     */
    public function getOptions(): array
    {
        return $this->_options;
    }

    /**
     * Get command-line.
     */
    public function getCommandLine(): string
    {
        $out = $this->getCommand();

        foreach ($this->getOptions() as $key => $value) {
            if (!\is_int($key)) {
                $out .= ' '.$key.'='.$value;
            } else {
                $out .= ' '.$value;
            }
        }

        return $out;
    }

    /**
     * Set current working directory of the process.
     */
    protected function setCwd(string $cwd)
    {
        $old = $this->_cwd;
        $this->_cwd = $cwd;

        return $old;
    }

    /**
     * Get current working directory of the process.
     */
    public function getCwd(): string
    {
        return $this->_cwd;
    }

    /**
     * Set environment of the process.
     */
    protected function setEnvironment(array $environment)
    {
        $old = $this->_environment;
        $this->_environment = $environment;

        return $old;
    }

    /**
     * Get environment of the process.
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Set timeout of the process.
     */
    public function setTimeout(int $timeout)
    {
        $old = $this->_timeout;
        $this->_timeout = $timeout;

        return $old;
    }

    /**
     * Get timeout of the process.
     */
    public function getTimeout(): int
    {
        return $this->_timeout;
    }

    /**
     * Set process title.
     */
    public static function setTitle(string $title)
    {
        \cli_set_process_title($title);
    }

    /**
     * Get process title.
     */
    public static function getTitle()
    {
        return \cli_get_process_title();
    }

    /**
     * Found the place of a binary.
     */
    public static function locate(string $binary)
    {
        if (isset($_ENV['PATH'])) {
            $separator = ':';
            $path = &$_ENV['PATH'];
        } elseif (isset($_SERVER['PATH'])) {
            $separator = ':';
            $path = &$_SERVER['PATH'];
        } elseif (isset($_SERVER['Path'])) {
            $separator = ';';
            $path = &$_SERVER['Path'];
        } else {
            return null;
        }

        foreach (\explode($separator, $path) as $directory) {
            if (true === \file_exists($out = $directory.\DIRECTORY_SEPARATOR.$binary)) {
                return $out;
            }
        }

        return null;
    }

    /**
     * Quick process execution.
     * Returns only the STDOUT.
     */
    public static function execute(string $commandLine, bool $escape = true): string
    {
        if (true === $escape) {
            $commandLine = \escapeshellcmd($commandLine);
        }

        return \rtrim(\shell_exec($commandLine) ?? '');
    }
}
