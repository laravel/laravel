# Configuration

You might want to configure concurrently to always have certain flags on.
Any of concurrently's flags can be set via environment variables that are prefixed with `CONCURRENTLY_`.

```bash
$ export CONCURRENTLY_KILL_OTHERS=true
$ export CONCURRENTLY_HANDLE_INPUT=true
# Equivalent to passing --kill-others and --handle-input
$ concurrently nodemon "echo 'hey nodemon, you won't last long'"
```
