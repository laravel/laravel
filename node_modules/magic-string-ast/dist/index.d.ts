import MagicString, { MagicStringOptions, OverwriteOptions } from "magic-string";
export * from "magic-string";

//#region src/index.d.ts
interface Node {
  start?: number | null;
  end?: number | null;
}
interface MagicStringAST extends MagicString {}
/**
* MagicString with AST manipulation
*/
declare class MagicStringAST implements MagicString {
  private prototype;
  s: MagicString;
  constructor(str: string | MagicString, options?: MagicStringOptions, prototype?: typeof MagicString);
  private getNodePos;
  removeNode(node: Node | Node[], {
    offset
  }?: {
    offset?: number;
  }): this;
  moveNode(node: Node | Node[], index: number, {
    offset
  }?: {
    offset?: number;
  }): this;
  sliceNode(node: Node | Node[], {
    offset
  }?: {
    offset?: number;
  }): string;
  overwriteNode(node: Node | Node[], content: string | Node | Node[], {
    offset,
    ...options
  }?: OverwriteOptions & {
    offset?: number;
  }): this;
  snipNode(node: Node | Node[], {
    offset
  }?: {
    offset?: number;
  }): MagicStringAST;
  clone(): this;
  toString(): string;
  private replaceRangeState;
  /**
  * Replace a range of text with new nodes.
  * @param start The start index of the range to replace.
  * @param end The end index of the range to replace.
  * @param nodes The nodes or strings to insert into the range.
  */
  replaceRange(start: number, end: number, ...nodes: (string | Node)[]): this;
}
/**
* The result of code transformation.
*/
interface CodeTransform {
  code: string;
  map: any;
}
/**
* Generate an object of code and source map from MagicString.
*/
declare function generateTransform(s: MagicString | undefined, id: string): CodeTransform | undefined;
//#endregion
export { CodeTransform, MagicString, MagicStringAST, generateTransform };