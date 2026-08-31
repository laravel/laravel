import { u as DiagnosticReporter } from "../diagnostic-wduO7saY.mjs";

//#region src/reporters/fetch.d.ts
/**
* Creates a reporter that POSTs each diagnostic as JSON to the given URL.
* Errors are swallowed so reporting never throws into user code.
*/
declare function createFetchReporter(url: string): DiagnosticReporter;
//#endregion
export { createFetchReporter };
//# sourceMappingURL=fetch.d.mts.map