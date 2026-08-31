import { u as DiagnosticReporter } from "../diagnostic-wduO7saY.mjs";

//#region src/reporters/dev.d.ts
/**
* Creates a reporter for browser code under Vite dev: it forwards each
* diagnostic over `import.meta.hot.send('nostics:report', ...)` so the
* dev-server collector can file it. Outside Vite (`import.meta.hot` absent) it
* warns once and does nothing.
*/
declare function createDevReporter(): DiagnosticReporter;
//#endregion
export { createDevReporter };
//# sourceMappingURL=dev.d.mts.map