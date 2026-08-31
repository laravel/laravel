# Documentation Site and Error Code Registry

Every published code needs a stable, public documentation page forever. It serves three audiences: **developers** clicking the `see:` URL from their terminal, **AI agents** fetching the page to help when a user pastes an error, and **search engines** indexing `NUXT_B2011` so it's findable. Plan the URL structure to match `docsBase` (string form → `${docsBase}/${code.toLowerCase()}`; function form → full control).

## Page template

Each code page (`https://nuxt.com/e/b2011`) follows this structure. Keep it human-readable and agent-parseable: consistent `##` headings, actionable content early, no critical info hidden in tabs/collapsed sections/JS-rendered content.

```markdown
# {CODE}: {Short title}

Code: `{CODE}`
Level: {error|warn|suggestion|deprecation}

## What this error means

{Plain-language explanation, no assumed context: what the system expected vs received. 1-3 sentences. Agents rely on this to explain the error.}

## Why this happens

{Bulleted list of the concrete scenarios that trigger this diagnostic.}

## How to fix it

{The most important section: copy-pasteable code showing the wrong pattern and the corrected version.}

## Additional context

{Optional: links to related docs, changelog, or related codes.}

## Example output

{Optional: the formatted terminal output, so users confirm they're on the right page.}
```

## Deployment

- Host on a public URL matching `docsBase`: a static site generator (VitePress, Nuxt Content) with a catch-all `/e/[code]` route, or a dedicated `/errors` route in existing docs.
- Return 200 for valid codes, 404 for unknown ones, so agents and crawlers distinguish them.
- Add frontmatter/`<meta>` structured data (code, level, title). Keep pages lightweight; avoid SPAs that block fetch-based agents.

## Keep docs in sync with code

- Store the markdown alongside the diagnostic definitions or in `docs/errors/`, and add the page in the same PR as a new code.
- Generate an index page listing all codes with messages and levels.
- In CI, validate that every code in `defineDiagnostics()` has a corresponding page; fail the build if one is missing.
