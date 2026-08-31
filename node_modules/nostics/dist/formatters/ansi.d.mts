import { a as Diagnostic } from "../diagnostic-wduO7saY.mjs";

//#region src/formatters/ansi.d.ts
interface Colors {
  red: (s: string) => string;
  yellow: (s: string) => string;
  cyan: (s: string) => string;
  gray: (s: string) => string;
  bold: (s: string) => string;
  dim: (s: string) => string;
}
declare function ansiFormatter(colors: Colors): (d: Diagnostic) => string;
//#endregion
export { Colors, ansiFormatter };
//# sourceMappingURL=ansi.d.mts.map