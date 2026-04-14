# Governance — laravel-pr
# Inferred by crag analyze — review and adjust as needed

## Identity
- Project: laravel-pr
- Stack: node, php, laravel

## Gates (run in order, stop on failure)
### Lint
- composer validate --strict

### Test
- composer test

### Build
- npm run build

## Advisories (informational, not enforced)
- actionlint  # [ADVISORY]

## Branch Strategy
- Trunk-based development
- Free-form commits
- Commit trailer: Co-Authored-By: Claude <noreply@anthropic.com>

## Security
- No hardcoded secrets — grep for sk_live, AKIA, password= before commit

## Autonomy
- Auto-commit after gates pass

## Architecture
- Type: monolith

## Key Directories
- `.github/` — CI/CD
- `config/` — configuration
- `public/` — static assets
- `tests/` — tests

## Code Style
- Indent: 4 spaces

## Import Conventions
- Module system: ESM

## Anti-Patterns

Do not:
- Do not leave `console.log` in production code — use a proper logger
- Do not use synchronous filesystem APIs in request handlers
- Do not use `eval()` or `exec()` with user input
- Do not suppress errors with `@` operator

