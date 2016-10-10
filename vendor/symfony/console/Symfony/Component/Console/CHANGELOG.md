CHANGELOG
=========

2.4.0
-----

 * added a way to force terminal dimensions
 * added a convenient method to detect verbosity level
 * [BC BREAK] made descriptors use output instead of returning a string

2.3.0
-----

 * added multiselect support to the select dialog helper
 * added Table Helper for tabular data rendering
 * added support for events in `Application`
 * added a way to normalize EOLs in `ApplicationTester::getDisplay()` and `CommandTester::getDisplay()`
 * added a way to set the progress bar progress via the `setCurrent` method
 * added support for multiple InputOption shortcuts, written as `'-a|-b|-c'`
 * added two additional verbosity levels, VERBOSITY_VERY_VERBOSE and VERBOSITY_DEBUG

2.2.0
-----

 * added support for colorization on Windows via ConEmu
 * add a method to Dialog Helper to ask for a question and hide the response
 * added support for interactive selections in console (DialogHelper::select())
 * added support for autocompletion as you type in Dialog Helper

2.1.0
-----

 * added ConsoleOutputInterface
 * added the possibility to disable a command (Command::isEnabled())
 * added suggestions when a command does not exist
 * added a --raw option to the list command
 * added support for STDERR in the console output class (errors are now sent
   to STDERR)
 * made the defaults (helper set, commands, input definition) in Application
   more easily customizable
 * added support for the shell even if readline is not available
 * added support for process isolation in Symfony shell via
   `--process-isolation` switch
 * added support for `--`, which disables options parsing after that point
   (tokens will be parsed as arguments)
