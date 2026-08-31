import { isFunctionType, walkAST as walkAST$1 } from "ast-kit";
import { parse } from "@babel/parser";
//#region src/utils/babel.ts
const NEW_SCOPE = new Set([
	"CatchClause",
	"ForInStatement",
	"ForOfStatement"
]);
const isNewScope = (node) => node && NEW_SCOPE.has(node.type) || isFunctionType(node);
function walkFunctionParams(node, onIdent) {
	for (const p of node.params) for (const id of extractIdentifiers(p)) onIdent(id);
}
function extractIdentifiers(param, nodes = []) {
	switch (param.type) {
		case "Identifier":
			nodes.push(param);
			break;
		case "MemberExpression": {
			let object = param;
			while (object.type === "MemberExpression") object = object.object;
			nodes.push(object);
			break;
		}
		case "ObjectPattern":
			for (const prop of param.properties) if (prop.type === "RestElement") extractIdentifiers(prop.argument, nodes);
			else extractIdentifiers(prop.value, nodes);
			break;
		case "ArrayPattern":
			param.elements.forEach((element) => {
				if (element) extractIdentifiers(element, nodes);
			});
			break;
		case "RestElement":
			extractIdentifiers(param.argument, nodes);
			break;
		case "AssignmentPattern":
			extractIdentifiers(param.left, nodes);
			break;
	}
	return nodes;
}
function babelParse(code, filename, parserPlugins = []) {
	const plugins = parserPlugins || [];
	if (filename) {
		if (/\.tsx?$/.test(filename)) plugins.push("typescript");
		if (filename.endsWith("x")) plugins.push("jsx");
	}
	return parse(code, {
		sourceType: "module",
		plugins
	});
}
function walkVariableDeclaration(stmt, register) {
	if (stmt.declare) return;
	for (const decl of stmt.declarations) for (const id of extractIdentifiers(decl.id)) register(id);
}
function walkNewIdentifier(node, register) {
	if (node.type === "ExportNamedDeclaration" && node.declaration) node = node.declaration;
	if (node.type === "VariableDeclaration") walkVariableDeclaration(node, register);
	else if (node.type === "FunctionDeclaration" || node.type === "ClassDeclaration") {
		if (node.declare || !node.id) return;
		register(node.id);
	} else if (node.type === "ExportNamedDeclaration" && node.declaration && node.declaration.type === "VariableDeclaration") walkVariableDeclaration(node.declaration, register);
}
//#endregion
//#region src/index.ts
function walk(code, walkHooks, { filename, parserPlugins } = {}) {
	const ast = babelParse(code, filename, parserPlugins);
	walkAST(ast.program, walkHooks);
	return ast;
}
function walkAST(node, { enter, leave, enterAfter, leaveAfter }) {
	let currentScope = Object.create(null);
	const scopeStack = [currentScope];
	walkAST$1(Array.isArray(node) ? {
		type: "Program",
		body: node
	} : node, {
		enter(node, parent, ...args) {
			const { scopeCtx, walkerCtx, isSkip, isRemoved, getNode } = getHookContext(this, node, [parent, ...args]);
			enter?.call({
				...scopeCtx(),
				...walkerCtx
			}, node);
			node = getNode();
			if (!isSkip() && !isRemoved()) {
				enterNode(node, parent);
				enterAfter?.call(scopeCtx(), node);
			}
		},
		leave(node, parent, ...args) {
			const { scopeCtx, walkerCtx, isSkip, isRemoved, getNode } = getHookContext(this, node, [parent, ...args]);
			leave?.call({
				...scopeCtx(),
				...walkerCtx
			}, node);
			node = getNode();
			if (!isSkip() && !isRemoved()) {
				leaveNode(node, parent);
				leaveAfter?.call(scopeCtx(), node);
			}
		}
	});
	function getHookContext(ctx, node, [parent, key, index]) {
		const scopeCtx = () => ({
			parent,
			key,
			index,
			scope: scopeStack.reduce((prev, curr) => Object.assign(prev, curr), Object.create(null)),
			scopes: scopeStack,
			level: scopeStack.length
		});
		let isSkip = false;
		let isRemoved = false;
		let newNode = node;
		return {
			scopeCtx,
			walkerCtx: {
				skip() {
					isSkip = true;
					ctx.skip();
				},
				replace(node) {
					newNode = node;
				},
				remove() {
					isRemoved = true;
				}
			},
			isSkip: () => isSkip,
			isRemoved: () => isRemoved,
			getNode: () => newNode
		};
	}
	function enterNode(node, parent) {
		if (isNewScope(node) || node.type === "BlockStatement" && !isNewScope(parent)) scopeStack.push(currentScope = Object.create(null));
		if (isFunctionType(node)) walkFunctionParams(node, registerBinding);
		else if (node.type === "CatchClause" && node.param && node.param.type === "Identifier") registerBinding(node.param);
		if (node.type === "BlockStatement" || node.type === "Program") {
			for (const stmt of node.body) if (stmt.type === "VariableDeclaration" && stmt.kind === "var") walkVariableDeclaration(stmt, registerBinding);
			else if (stmt.type === "FunctionDeclaration" && stmt.id) registerBinding(stmt.id);
		}
	}
	function leaveNode(node, parent) {
		if (isNewScope(node) || node.type === "BlockStatement" && !isNewScope(parent)) {
			scopeStack.pop();
			currentScope = scopeStack.at(-1);
		}
		walkNewIdentifier(node, registerBinding);
	}
	function registerBinding(id) {
		if (currentScope) currentScope[id.name] = id;
		else error("registerBinding called without active scope, something is wrong.", id);
	}
	function error(msg, node) {
		const e = new Error(msg);
		e.node = node;
		throw e;
	}
}
function getRootScope(nodes) {
	const scope = Object.create(null);
	for (const node of nodes) walkNewIdentifier(node, (id) => {
		scope[id.name] = id;
	});
	return scope;
}
//#endregion
export { babelParse, extractIdentifiers, getRootScope, isNewScope, walk, walkAST, walkFunctionParams, walkNewIdentifier, walkVariableDeclaration };
