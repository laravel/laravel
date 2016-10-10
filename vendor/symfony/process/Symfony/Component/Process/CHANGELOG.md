CHANGELOG
=========

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
