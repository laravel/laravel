import { u as DiagnosticReporter } from "../diagnostic-wduO7saY.mjs";

//#region src/reporters/node.d.ts
interface FileReporterOptions {
  /**
  * Path to the log file.
  * @default '.nostics.log'
  */
  logFile?: string;
  /**
  * Stack frames matching ANY of these patterns are removed from
  * `diagnostic.stack` before it is written to the log file. Useful to strip
  * `node_modules` and Node internals. The `Error: ...` header line is
  * always preserved.
  *
  * Pass an empty array to keep every frame.
  * @default [/\/node_modules\//i]
  */
  excludeStackFrames?: readonly RegExp[];
}
/**
* Creates a reporter that appends diagnostics as NDJSON to a local file.
* Each diagnostic is written as a single JSON line. The diagnostic's `stack`
* (if present) is included in the payload; noisy frames can be removed via
* {@link FileReporterOptions.excludeStackFrames}.
*
* @example
* ```ts
* import { defineDiagnostics } from 'nostics'
* import { createFileReporter } from 'nostics/reporters/node'
*
* const diagnostics = defineDiagnostics({
*   codes: { ... },
*   reporters: [createFileReporter({
*     excludeStackFrames: [/\/node_modules\//, /\(node:/],
*   })],
* })
* ```
*/
declare function createFileReporter(options?: FileReporterOptions): DiagnosticReporter;
//#endregion
export { FileReporterOptions, createFileReporter };
//# sourceMappingURL=node.d.mts.map