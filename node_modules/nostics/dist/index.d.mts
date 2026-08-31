import { a as Diagnostic, c as DiagnosticHandle, d as Diagnostics, f as createConsoleReporter, i as DefineDiagnosticsOptions, l as DiagnosticInit, m as ValueOrFn, n as ConsoleMethod, o as DiagnosticCallParams, p as defineDiagnostics, r as ConsoleReporterOptions, s as DiagnosticDefinition, t as AnyDiagnosticReporter, u as DiagnosticReporter } from "./diagnostic-wduO7saY.mjs";

//#region src/formatters/plain.d.ts
/**
* Renders a diagnostic into a multi-line, unicode-decorated string suitable
* for terminal output. The first line is `[<name>] <message>`; optional
* details (`fix`, `sources`, `docs`) follow with `├▶`/`╰▶` connectors.
*/
declare function formatDiagnostic(diagnostic: Diagnostic): string;
//#endregion
//#region src/prod-diagnostics.d.ts
/**
* Options for {@link defineProdDiagnostics}. A lean subset of
* {@link DefineDiagnosticsOptions}: no `codes` map (the proxy serves any code),
* only what is needed to keep behaviour correct in production.
*/
interface DefineProdDiagnosticsOptions<Reporters extends readonly AnyDiagnosticReporter[] = readonly AnyDiagnosticReporter[]> {
  /**
  * Base URL or resolver for documentation links, identical to
  * {@link DefineDiagnosticsOptions.docsBase}. The docs URL is derived from the
  * accessed code at call time, so links survive even without the catalog.
  */
  docsBase?: string | ((code: string) => string | undefined);
  /**
  * Reporters called every time a diagnostic is produced. Omitted by default in
  * production builds; the strip plugin can copy them into the prod branch when
  * prod-time reporting (e.g. telemetry) is desired.
  */
  reporters?: Reporters;
}
/**
* Production counterpart to {@link defineDiagnostics}. Returns a `Proxy` that
* builds a minimal {@link Diagnostic} for any accessed code: the code becomes
* the instance `name`, `docs` is derived from `docsBase`, and `why` points to
* the docs URL when one exists (empty otherwise, so the thrown header is just
* the code). It carries no catalog text, so it stays tiny in a bundle.
*
* The strip plugin (`@nostics/unplugin`) can rewrite a `defineDiagnostics()`
* call into a `process.env.NODE_ENV === 'production'` ternary that selects this
* factory in production, dropping every `why`/`fix` string from the bundle.
*
* @example
* ```ts
* const diagnostics = defineProdDiagnostics({ docsBase: 'https://docs.example.com' })
* throw diagnostics.NUXT_B2011() // NUXT_B2011: https://docs.example.com/nuxt_b2011
* ```
*/
declare function defineProdDiagnostics<const Codes extends Record<string, DiagnosticDefinition> = Record<string, DiagnosticDefinition>, const Reporters extends readonly AnyDiagnosticReporter[] = readonly AnyDiagnosticReporter[]>(options?: DefineProdDiagnosticsOptions<Reporters>): Diagnostics<Codes, Reporters>;
//#endregion
export { type AnyDiagnosticReporter, type ConsoleMethod, type ConsoleReporterOptions, type DefineDiagnosticsOptions, type DefineProdDiagnosticsOptions, Diagnostic, type DiagnosticCallParams, type DiagnosticDefinition, type DiagnosticHandle, type DiagnosticInit, type DiagnosticReporter, type Diagnostics, type ValueOrFn as _ValueOrFn, createConsoleReporter, defineDiagnostics, defineProdDiagnostics, formatDiagnostic };
//# sourceMappingURL=index.d.mts.map