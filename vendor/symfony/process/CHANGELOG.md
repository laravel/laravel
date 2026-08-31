CHANGELOG
=========


7.3
---

 * Add `RunProcessMessage::fromShellCommandline()` to instantiate a Process via the fromShellCommandline method

7.1
---

 * Add `Process::setIgnoredSignals()` to disable signal propagation to the child process

6.4
---

 * Add `PhpSubprocess` to handle PHP subprocesses that take over the
   configuration from their parent
 * Add `RunProcessMessage` and `RunProcessMessageHandler`

5.2.0
-----

 * added `Process::setOptions()` to set `Process` specific options
 * added option `create_new_console` to allow a subprocess to continue
   to run after the main script exited, both on Linux and on Windows

5.1.0
-----

 * added `Process::getStartTime()` to retrieve the start time of the process as float

5.0.0
-----

 * removed `Process::inheritEnvironmentVariables()`
 * removed `PhpProcess::setPhpBinary()`
 * `Process` must be instantiated with a command array, use `Process::fromShellCommandline()` when the command should be parsed by the shell
 * removed `Process::setCommandLine()`

4.4.0
-----

 * deprecated `Process::inheritEnvironmentVariables()`: env variables are always inherited.
 * added `Process::getLastOutputTime()` method

4.2.0
-----

 * added the `Process::fromShellCommandline()` to run commands in a shell wrapper
 * deprecated passing a command as string when creating a `Process` instance
 * deprecated the `Process::setCommandline()` and the `PhpProcess::setPhpBinary()` methods
 * added the `Process::waitUntil()` method to wait for the process only for a
   specific output, then continue the normal execution of your application

4.1.0
-----

 * added the `Process::isTtySupported()` method that allows to check for TTY support
 * made `PhpExecutableFinder` look for the `PHP_BINARY` env var when searching the php binary
 * added the `ProcessSignaledException` class to properly catch signaled process errors

4.0.0
-----

 * environment variables will always be inherited
 * added a second `array $env = []` argument to the `start()`, `run()`,
   `mustRun()`, and `restart()` methods of the `Process` class
 * added a second `array $env = []` argument to the `start()` method of the
   `PhpProcess` class
 * the `ProcessUtils::escapeArgument()` method has been removed
 * the `areEnvironmentVariablesInherited()`, `getOptions()`, and `setOptions()`
   methods of the `Process` class have been removed
 * support for passing `proc_open()` options has been removed
 * removed the `ProcessBuilder` class, use the `Process` class instead
 * removed the `getEnhanceWindowsCompatibility()` and `setEnhanceWindowsCompatibility()` methods of the `Process` class
 * passing a not existing working directory to the constructor of the `Symfony\Component\Process\Process` class is not
   supported anymore

3.4.0
-----

 * deprecated the ProcessBuilder class
 * deprecated calling `Process::start()` without setting a valid working directory beforehand (via `setWorkingDirectory()` or constructor)

3.3.0
-----

 * added command line arrays in the `Process` class
 * added `$env` argument to `Process::start()`, `run()`, `mustRun()` and `restart()` methods
 * deprecated the `ProcessUtils::escapeArgument()` method
 * deprecated not inheriting environment variables
 * deprecated configuring `proc_open()` options
 * deprecated configuring enhanced Windows compatibility
 * deprecated configuring enhanced sigchild compatibility

2.5.0
-----

 * added support for PTY mode
 * added the convenience method "mustRun"
 * deprecation: Process::setStdin() is deprecated in favor of Process::setInput()
 * deprecation: Process::getStdin() is deprecated in favor of Process::getInput()
 * deprecation: Process::setInput() and ProcessBuilder::setInput() do not accept non-scalar types

2.4.0
-----

 * added the ability to define an idle timeout

2.3.0
-----

 * added ProcessUtils::escapeArgument() to fix the bug in escapeshellarg() function on Windows
 * added Process::signal()
 * added Process::getPid()
 * added support for a TTY mode

2.2.0
-----

 * added ProcessBuilder::setArguments() to reset the arguments on a builder
 * added a way to retrieve the standard and error output incrementally
 * added Process:restart()

2.1.0
-----

 * added support for non-blocking processes (start(), wait(), isRunning(), stop())
 * enhanced Windows compatibility
 * added Process::getExitCodeText() that returns a string representation for
   the exit code returned by the process
 * added ProcessBuilder
