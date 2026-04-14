<!-- crag:auto-start -->
# AGENTS.md

> Generated from governance.md by crag. Regenerate: `crag compile --target agents-md`

## Project: laravel-pr


## Quality Gates

All changes must pass these checks before commit:

### Lint
1. `composer validate --strict`

### Test
1. `composer test`

### Build
1. `npm run build`

## Coding Standards

- Stack: node, php, laravel
- Follow project commit conventions

## Architecture

- Type: monolith

## Key Directories

- `.github/` — CI/CD
- `config/` — configuration
- `public/` — static assets
- `tests/` — tests

## Code Style

- Indent: 4 spaces

## Anti-Patterns

Do not:
- Do not leave `console.log` in production code — use a proper logger
- Do not use synchronous filesystem APIs in request handlers
- Do not use `eval()` or `exec()` with user input
- Do not suppress errors with `@` operator

## Security

- No hardcoded secrets — grep for sk_live, AKIA, password= before commit

## Workflow

1. Read `governance.md` at the start of every session — it is the single source of truth.
2. Run all mandatory quality gates before committing.
3. If a gate fails, fix the issue and re-run only the failed gate.
4. Use the project commit conventions for all changes.

<!-- crag:auto-end -->
