---
name: add-diagnostic
description: 'Add a new diagnostic code following the defineDiagnostics() conventions from nostics'
user-invocable: true
allowed-tools: Read Grep Glob Edit Write
license: MIT
---

# Add a New Diagnostic Code

1. **Find the catalog.** Grep for `defineDiagnostics` to locate the `codes` object the new entry belongs in. With several catalogs, pick by area.
2. **Pick the code.** `PREFIX_XNNNN`: `PREFIX` is the project name uppercased (`NUXT`, `I18N`); `X` is the area letter (`B` build, `R` runtime, `C` config, `D` deprecation); `NNNN` is the next free number. Read existing codes to choose. Never rename or reuse a published code.
3. **Add the entry:**

```ts
LIB_R0001: {
  why: (p: { hook: string }) => `${p.hook}() must run at the top of setup().`, // string or typed fn; becomes Error.message (required)
  fix: 'Move the call into setup() or a composable it calls.', // optional, but add whenever the fix is known
  docs: 'https://example.com/custom', // optional: overrides docsBase, or `false` to opt out
},
```

- `why` is the only required field. Params from `why` and `fix` are intersected and required at the call site.
- Runtime fields (`cause`, `sources`) are passed at the call site, never in the definition.

4. **Call it:** `diagnostics.LIB_R0001({ hook })` to report, `throw diagnostics.LIB_R0001({ hook, cause: err })` to raise. Both run the reporters.

Full API and reporter/formatter/plugin details: the `nostics` skill.
