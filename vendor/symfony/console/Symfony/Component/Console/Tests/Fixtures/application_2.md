My Symfony application
======================

* alias1
* alias2
* help
* list

**descriptor:**

* descriptor:command1
* descriptor:command2

help
----

* Description: Displays help for a command
* Usage: `help [--xml] [--format="..."] [--raw] [command_name]`
* Aliases: <none>

The <info>help</info> command displays help for a given command:

  <info>php app/console help list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php app/console help --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.

### Arguments:

**command_name:**

* Name: command_name
* Is required: no
* Is array: no
* Description: The command name
* Default: `'help'`

### Options:

**xml:**

* Name: `--xml`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: To output help as XML
* Default: `false`

**format:**

* Name: `--format`
* Shortcut: <none>
* Accept value: yes
* Is value required: yes
* Is multiple: no
* Description: To output help in other formats
* Default: `'txt'`

**raw:**

* Name: `--raw`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: To output raw command help
* Default: `false`

**help:**

* Name: `--help`
* Shortcut: `-h`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Display this help message.
* Default: `false`

**quiet:**

* Name: `--quiet`
* Shortcut: `-q`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Do not output any message.
* Default: `false`

**verbose:**

* Name: `--verbose`
* Shortcut: `-v|-vv|-vvv`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
* Default: `false`

**version:**

* Name: `--version`
* Shortcut: `-V`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Display this application version.
* Default: `false`

**ansi:**

* Name: `--ansi`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Force ANSI output.
* Default: `false`

**no-ansi:**

* Name: `--no-ansi`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Disable ANSI output.
* Default: `false`

**no-interaction:**

* Name: `--no-interaction`
* Shortcut: `-n`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Do not ask any interactive question.
* Default: `false`

list
----

* Description: Lists commands
* Usage: `list [--xml] [--raw] [--format="..."] [namespace]`
* Aliases: <none>

The <info>list</info> command lists all commands:

  <info>php app/console list</info>

You can also display the commands for a specific namespace:

  <info>php app/console list test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>php app/console list --format=xml</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php app/console list --raw</info>

### Arguments:

**namespace:**

* Name: namespace
* Is required: no
* Is array: no
* Description: The namespace name
* Default: `NULL`

### Options:

**xml:**

* Name: `--xml`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: To output list as XML
* Default: `false`

**raw:**

* Name: `--raw`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: To output raw command list
* Default: `false`

**format:**

* Name: `--format`
* Shortcut: <none>
* Accept value: yes
* Is value required: yes
* Is multiple: no
* Description: To output list in other formats
* Default: `'txt'`

descriptor:command1
-------------------

* Description: command 1 description
* Usage: `descriptor:command1`
* Aliases: `alias1`, `alias2`

command 1 help

### Options:

**help:**

* Name: `--help`
* Shortcut: `-h`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Display this help message.
* Default: `false`

**quiet:**

* Name: `--quiet`
* Shortcut: `-q`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Do not output any message.
* Default: `false`

**verbose:**

* Name: `--verbose`
* Shortcut: `-v|-vv|-vvv`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
* Default: `false`

**version:**

* Name: `--version`
* Shortcut: `-V`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Display this application version.
* Default: `false`

**ansi:**

* Name: `--ansi`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Force ANSI output.
* Default: `false`

**no-ansi:**

* Name: `--no-ansi`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Disable ANSI output.
* Default: `false`

**no-interaction:**

* Name: `--no-interaction`
* Shortcut: `-n`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Do not ask any interactive question.
* Default: `false`

descriptor:command2
-------------------

* Description: command 2 description
* Usage: `descriptor:command2 [-o|--option_name] argument_name`
* Aliases: <none>

command 2 help

### Arguments:

**argument_name:**

* Name: argument_name
* Is required: yes
* Is array: no
* Description: <none>
* Default: `NULL`

### Options:

**option_name:**

* Name: `--option_name`
* Shortcut: `-o`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: <none>
* Default: `false`

**help:**

* Name: `--help`
* Shortcut: `-h`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Display this help message.
* Default: `false`

**quiet:**

* Name: `--quiet`
* Shortcut: `-q`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Do not output any message.
* Default: `false`

**verbose:**

* Name: `--verbose`
* Shortcut: `-v|-vv|-vvv`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
* Default: `false`

**version:**

* Name: `--version`
* Shortcut: `-V`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Display this application version.
* Default: `false`

**ansi:**

* Name: `--ansi`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Force ANSI output.
* Default: `false`

**no-ansi:**

* Name: `--no-ansi`
* Shortcut: <none>
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Disable ANSI output.
* Default: `false`

**no-interaction:**

* Name: `--no-interaction`
* Shortcut: `-n`
* Accept value: no
* Is value required: no
* Is multiple: no
* Description: Do not ask any interactive question.
* Default: `false`
