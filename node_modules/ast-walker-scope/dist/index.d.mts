import { ParseResult, ParserPlugin } from "@babel/parser";
import { File, Function, Identifier, Node, VariableDeclaration } from "@babel/types";

//#region src/types.d.ts
interface ParseOptions {
  filename?: string;
  parserPlugins?: ParserPlugin[];
}
type Scope = Record<string, Node>;
interface WalkerContext {
  skip: () => void;
  remove: () => void;
  replace: (node: Node) => void;
}
interface ScopeContext {
  parent: Node | undefined | null;
  key: string | undefined | null;
  index: number | undefined | null;
  scope: Scope;
  scopes: Scope[];
  level: number;
}
interface WalkerHooks {
  enter?: (this: WalkerContext & ScopeContext, node: Node) => void;
  enterAfter?: (this: ScopeContext, node: Node) => void;
  leave?: (this: WalkerContext & ScopeContext, node: Node) => void;
  leaveAfter?: (this: ScopeContext, node: Node) => void;
}
//#endregion
//#region src/utils/babel.d.ts
declare const isNewScope: (node: Node | undefined | null) => boolean;
declare function walkFunctionParams(node: Function, onIdent: (id: Identifier) => void): void;
declare function extractIdentifiers(param: Node, nodes?: Identifier[]): Identifier[];
declare function babelParse(code: string, filename?: string, parserPlugins?: ParserPlugin[]): ParseResult<File>;
declare function walkVariableDeclaration(stmt: VariableDeclaration, register: (id: Identifier) => void): void;
declare function walkNewIdentifier(node: Node, register: (id: Identifier) => void): void;
//#endregion
//#region src/index.d.ts
declare function walk(code: string, walkHooks: WalkerHooks, {
  filename,
  parserPlugins
}?: ParseOptions): ParseResult<File>;
declare function walkAST(node: Node | Node[], {
  enter,
  leave,
  enterAfter,
  leaveAfter
}: WalkerHooks): void;
declare function getRootScope(nodes: Node[]): Scope;
//#endregion
export { ParseOptions, Scope, ScopeContext, WalkerContext, WalkerHooks, babelParse, extractIdentifiers, getRootScope, isNewScope, walk, walkAST, walkFunctionParams, walkNewIdentifier, walkVariableDeclaration };