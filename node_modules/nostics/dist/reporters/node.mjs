import { appendFileSync } from "node:fs";
//#region src/reporters/node.ts
const DEFAULT_EXCLUDE_STACK_FRAMES = [/\/node_modules\//i];
function applyExcludeStackFrames(raw, exclude) {
	const [header, ...frames] = raw.split("\n");
	return [header, ...frames.filter((frame) => !exclude.some((re) => re.test(frame)))].join("\n");
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
/* @__NO_SIDE_EFFECTS__ */
function createFileReporter(options) {
	const logFile = options?.logFile ?? ".nostics.log";
	const excludeStackFrames = options?.excludeStackFrames ?? DEFAULT_EXCLUDE_STACK_FRAMES;
	return (diagnostic) => {
		try {
			const d = diagnostic;
			const base = typeof d.toJSON === "function" ? d.toJSON() : { ...d };
			if (d.stack) base.stack = excludeStackFrames?.length ? applyExcludeStackFrames(d.stack, excludeStackFrames) : d.stack;
			appendFileSync(logFile, `${JSON.stringify(base)}\n`);
		} catch (err) {
			console.error(`[nostics]: Failed to write log to "${logFile}":`, err);
		}
	};
}
//#endregion
export { createFileReporter };

//# sourceMappingURL=node.mjs.map