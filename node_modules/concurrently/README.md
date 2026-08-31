# concurrently

[![Latest Release](https://img.shields.io/github/v/release/open-cli-tools/concurrently?label=Release)](https://github.com/open-cli-tools/concurrently/releases)
[![License](https://img.shields.io/github/license/open-cli-tools/concurrently?label=License)](https://github.com/open-cli-tools/concurrently/blob/main/LICENSE)
[![Weekly Downloads on NPM](https://img.shields.io/npm/dw/concurrently?label=Downloads&logo=npm)](https://www.npmjs.com/package/concurrently)
[![CI Status](https://img.shields.io/github/actions/workflow/status/open-cli-tools/concurrently/test.yml?label=CI&logo=github)](https://github.com/open-cli-tools/concurrently/actions/workflows/test.yml)
[![Coverage Status](https://img.shields.io/coveralls/github/open-cli-tools/concurrently/main?label=Coverage&logo=coveralls)](https://coveralls.io/github/open-cli-tools/concurrently?branch=main)

Run multiple commands concurrently.
Like `npm run watch-js & npm run watch-less` but better.

![Demo](docs/demo.gif)

**Table of Contents**

- [concurrently](#concurrently)
  - [Why](#why)
  - [Installation](#installation)
  - [Usage](#usage)
  - [API](#api)
    - [`concurrently(commands[, options])`](#concurrentlycommands-options)
    - [`Command`](#command)
    - [`CloseEvent`](#closeevent)
  - [FAQ](#faq)

## Why

I like [task automation with npm](https://web.archive.org/web/20220531064025/https://github.com/substack/blog/blob/master/npm_run.markdown)
but the usual way to run multiple commands concurrently is
`npm run watch-js & npm run watch-css`. That's fine but it's hard to keep
on track of different outputs. Also if one process fails, others still keep running
and you won't even notice the difference.

Another option would be to just run all commands in separate terminals. I got
tired of opening terminals and made **concurrently**.

**Features:**

- Cross platform (including Windows)
- Output is easy to follow with prefixes
- With `--kill-others` switch, all commands are killed if one dies

## Installation

**concurrently** can be installed in the global scope (if you'd like to have it available and use it on the whole system) or locally for a specific package (for example if you'd like to use it in the `scripts` section of your package):

|             | npm                     | Yarn                           | pnpm                       | Bun                       |
| ----------- | ----------------------- | ------------------------------ | -------------------------- | ------------------------- |
| **Global**  | `npm i -g concurrently` | `yarn global add concurrently` | `pnpm add -g concurrently` | `bun add -g concurrently` |
| **Local**\* | `npm i -D concurrently` | `yarn add -D concurrently`     | `pnpm add -D concurrently` | `bun add -d concurrently` |

<sub>\* It's recommended to add **concurrently** to `devDependencies` as it's usually used for developing purposes. Please adjust the command if this doesn't apply in your case.</sub>

## Usage

> **Note**
> The `concurrently` command is also available under the shorthand alias `conc`.

The tool is written in Node.js, but you can use it to run **any** commands.

Remember to surround separate commands with quotes:

```bash
concurrently 'command1 arg' 'command2 arg'
```

Otherwise **concurrently** would try to run 4 separate commands:
`command1`, `arg`, `command2`, `arg`.

In package.json, escape quotes:

```bash
"start": "concurrently 'command1 arg' 'command2 arg'"
```

You can always check concurrently's flag list by running `concurrently --help`.
For the version, run `concurrently --version`.

Check out documentation and other usage examples in the [`docs` directory](./docs/README.md).

## API

**concurrently** can be used programmatically by using the API documented below:

### `concurrently(commands[, options])`

- `commands`: an array of either strings (containing the commands to run) or objects
  with the shape `{ command, name, prefixColor, env, cwd, ipc }`.

- `options` (optional): an object containing any of the below:
  - `cwd`: the working directory to be used by all commands. Can be overridden per command.
    Default: `process.cwd()`.
  - `defaultInputTarget`: the default input target when reading from `inputStream`.
    Default: `0`.
  - `handleInput`: when `true`, reads input from `process.stdin`.
  - `inputStream`: a [`Readable` stream](https://nodejs.org/dist/latest-v10.x/docs/api/stream.html#stream_readable_streams)
    to read the input from. Should only be used in the rare instance you would like to stream anything other than `process.stdin`. Overrides `handleInput`.
  - `pauseInputStreamOnFinish`: by default, pauses the input stream (`process.stdin` when `handleInput` is enabled, or `inputStream` if provided) when all of the processes have finished. If you need to read from the input stream after `concurrently` has finished, set this to `false`. ([#252](https://github.com/kimmobrunfeldt/concurrently/issues/252)).
  - `killOthersOn`: once the first command exits with one of these statuses, kill other commands.
    Can be an array containing the strings `success` (status code zero) and/or `failure` (non-zero exit status).
  - `maxProcesses`: how many processes should run at once.
  - `outputStream`: a [`Writable` stream](https://nodejs.org/dist/latest-v10.x/docs/api/stream.html#stream_writable_streams)
    to write logs to. Default: `process.stdout`.
  - `prefix`: the prefix type to use when logging processes output.
    Possible values: `index`, `pid`, `time`, `command`, `name`, `none`, or a template (eg `[{time} process: {pid}]`).
    Default: the name of the process, or its index if no name is set.
  - `prefixColors`: a list of colors or a string as supported by [chalk](https://www.npmjs.com/package/chalk) and additional style `auto` for an automatically picked color.
    If concurrently would run more commands than there are colors, the last color is repeated, unless if the last color value is `auto` which means following colors are automatically picked to vary.
    Prefix colors specified per-command take precedence over this list.
  - `prefixLength`: how many characters to show when prefixing with `command`. Default: `10`
  - `raw`: whether raw mode should be used, meaning strictly process output will
    be logged, without any prefixes, coloring or extra stuff. Can be overridden per command.
  - `successCondition`: the condition to consider the run was successful.
    If `first`, only the first process to exit will make up the success of the run; if `last`, the last process that exits will determine whether the run succeeds.
    Anything else means all processes should exit successfully.
  - `restartTries`: how many attempts to restart a process that dies will be made. Default: `0`.
  - `restartDelay`: how many milliseconds to wait between process restarts. Default: `0`.
  - `timestampFormat`: a [Unicode format](https://www.unicode.org/reports/tr35/tr35-dates.html#Date_Field_Symbol_Table)
    to use when prefixing with `time`. Default: `yyyy-MM-dd HH:mm:ss.SSS`
  - `additionalArguments`: list of additional arguments passed that will get replaced in each command. If not defined, no argument replacing will happen.

> **Returns:** an object in the shape `{ result, commands }`.
>
> - `result`: a `Promise` that resolves if the run was successful (according to `successCondition` option),
>   or rejects, containing an array of [`CloseEvent`](#CloseEvent), in the order that the commands terminated.
> - `commands`: an array of all spawned [`Command`s](#Command).

Example:

```js
const concurrently = require('concurrently');
const { result } = concurrently(
  [
    'npm:watch-*',
    { command: 'nodemon', name: 'server' },
    { command: 'deploy', name: 'deploy', env: { PUBLIC_KEY: '...' } },
    {
      command: 'watch',
      name: 'watch',
      cwd: path.resolve(__dirname, 'scripts/watchers'),
    },
  ],
  {
    prefix: 'name',
    killOthers: ['failure', 'success'],
    restartTries: 3,
    cwd: path.resolve(__dirname, 'scripts'),
  },
);
result.then(success, failure);
```

### `Command`

An object that contains all information about a spawned command, and ways to interact with it.<br>
It has the following properties:

- `index`: the index of the command among all commands spawned.
- `command`: the command line of the command.
- `name`: the name of the command; defaults to an empty string.
- `cwd`: the current working directory of the command.
- `env`: an object with all the environment variables that the command will be spawned with.
- `killed`: whether the command has been killed.
- `state`: the command's state. Can be one of
  - `stopped`: if the command was never started
  - `started`: if the command is currently running
  - `errored`: if the command failed spawning
  - `exited`: if the command is not running anymore, e.g. it received a close event
- `pid`: the command's process ID.
- `stdin`: a Writable stream to the command's `stdin`.
- `stdout`: an RxJS observable to the command's `stdout`.
- `stderr`: an RxJS observable to the command's `stderr`.
- `error`: an RxJS observable to the command's error events (e.g. when it fails to spawn).
- `timer`: an RxJS observable to the command's timing events (e.g. starting, stopping).
- `stateChange`: an RxJS observable for changes to the command's `state` property.
- `messages`: an object with the following properties:
  - `incoming`: an RxJS observable for the IPC messages received from the underlying process.
  - `outgoing`: an RxJS observable for the IPC messages sent to the underlying process.

  Both observables emit [`MessageEvent`](#messageevent)s.<br>
  Note that if the command wasn't spawned with IPC support, these won't emit any values.

- `close`: an RxJS observable to the command's close events.
  See [`CloseEvent`](#CloseEvent) for more information.
- `start()`: starts the command and sets up all of the above streams
- `send(message[, handle, options])`: sends a message to the underlying process via IPC channels,
  returning a promise that resolves once the message has been sent.
  See [Node.js docs](https://nodejs.org/docs/latest/api/child_process.html#subprocesssendmessage-sendhandle-options-callback).
- `kill([signal])`: kills the command, optionally specifying a signal (e.g. `SIGTERM`, `SIGKILL`, etc).

### `MessageEvent`

An object that represents a message that was received from/sent to the underlying command process.<br>
It has the following properties:

- `message`: the message itself.
- `handle`: a [`net.Socket`](https://nodejs.org/docs/latest/api/net.html#class-netsocket),
  [`net.Server`](https://nodejs.org/docs/latest/api/net.html#class-netserver) or
  [`dgram.Socket`](https://nodejs.org/docs/latest/api/dgram.html#class-dgramsocket),
  if one was sent, or `undefined`.

### `CloseEvent`

An object with information about a command's closing event.<br>
It contains the following properties:

- `command`: a stripped down version of [`Command`](#command), including only `name`, `command`, `env` and `cwd` properties.
- `index`: the index of the command among all commands spawned.
- `killed`: whether the command exited because it was killed.
- `exitCode`: the exit code of the command's process, or the signal which it was killed with.
- `timings`: an object in the shape `{ startDate, endDate, durationSeconds }`.

## FAQ

- Process exited with code _null_?

  From [Node child_process documentation](http://nodejs.org/api/child_process.html#child_process_event_exit), `exit` event:

  > This event is emitted after the child process ends. If the process
  > terminated normally, code is the final exit code of the process,
  > otherwise null. If the process terminated due to receipt of a signal,
  > signal is the string name of the signal, otherwise null.

  So _null_ means the process didn't terminate normally. This will make **concurrently**
  to return non-zero exit code too.

- Does this work with the npm-replacements [yarn](https://github.com/yarnpkg/yarn), [pnpm](https://pnpm.js.org/), or [Bun](https://bun.sh/)?

  Yes! In all examples above, you may replace "`npm`" with "`yarn`", "`pnpm`", or "`bun`".
