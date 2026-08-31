# Success Conditions

When you're using concurrently in shell scripts or CI pipelines, the exit code matters.  
It determines whether the next step runs, or if the script stops with a failure.

You can control concurrently's exit code using the `--success` flag.  
This tells it **which command(s)** must succeed (exit with code `0`) for concurrently to return success overall.

There are several possible values:

## `all`

All commands must exit with code `0`.
This is the default value.

## `first`

The first command to exit must do so with code `0`.

```bash
# ✅ Exits with code 0 — second command exits first and succeeds
$ concurrently --success first 'sleep 1 && exit 1' 'exit 0'

# ❌ Exits with a non-zero code — second command exits first, but with code 1
$ concurrently --success first 'sleep 1 && exit 0' 'exit 1'
```

## `last`

The last command to exit must do so with code `0`.

```bash
# ✅ Exits with code 0 - first command exits last and succeeds
$ concurrently --success last 'sleep 1 && exit 0' 'exit 1'

# ❌ Exits with a non-zero code — first command exits last, but with code 1
$ concurrently --success last 'sleep 1 && exit 1' 'exit 0'
```

## `command-{name}` or `command-{index}`

A specific command, by name or index, must exit with code `0`.

```bash
# Exits with code 0 only if 'npm test' (index 1) passes.
$ concurrently --success command-1 --kill-others 'npm run server' 'npm test'

# Exits with code 0 only if 'test' command passes.
$ concurrently --success command-test --names server,test --kill-others \
    'npm start' \
    'npm test'
```

> [!TIP]
> Use `--kill-others` to kill a long-running process, such as a server, once tests pass.

## `!command-{name}` or `!command-{index}`

All but a specific command, by name or index, must exit with code `0`.

```bash
# Ignores 'npm start'; all others must succeed
$ concurrently --success '!command-2' --kill-others \
    'npm test' \
    'npm build' \
    'npm start'

# Ignores 'server'; all others must succeed
$ concurrently --success '!command-server' --names test,build,server --kill-others \
    'npm test' \
    'npm build' \
    'npm start'
```
