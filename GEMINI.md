<!-- crag:auto-start -->
# GEMINI.md

> Generated from governance.md by crag. Regenerate: `crag compile --target gemini`

## Project Context

- **Name:** laravel-pr
- **Stack:** node, php, laravel
- **Runtimes:** node

## Rules

### Quality Gates

Run these checks in order before committing any changes:

1. [lint] `composer validate --strict`
2. [test] `composer test`
3. [build] `npm run build`

### Security

- No hardcoded secrets — grep for sk_live, AKIA, password= before commit

### Workflow

- Follow project commit conventions
- Run quality gates before committing
- Review security implications of all changes

<!-- crag:auto-end -->
