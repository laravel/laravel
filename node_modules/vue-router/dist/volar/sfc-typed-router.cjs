/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
let muggle_string = require("muggle-string");
let pathe = require("pathe");
//#region src/volar/utils/augment-vls-ctx.ts
/**
* Augments the VLS context (volar) with additianal type information.
*
* @param content - content retrieved from the volar pluign
* @param codes - codes to add to the VLS context
*/
function augmentVlsCtx(content, codes) {
	let from = -1;
	for (let i = 0; i < content.length; i++) {
		const code = content[i];
		if (typeof code !== "string") continue;
		if (from === -1 && code.startsWith(`const __VLS_ctx`)) from = i;
		else if (from !== -1) {
			if (code === `}`) {
				content.splice(i, 0, ...codes.map((code) => `...${code},\n`));
				break;
			} else if (code === `;\n`) {
				content.splice(from + 1, i - from, `{\n`, `...`, ...content.slice(from + 1, i), `,\n`, ...codes.map((code) => `...${code},\n`), `}`, `;\n`);
				break;
			}
		}
	}
}
//#endregion
//#region src/volar/entries/sfc-typed-router.ts
const plugin = ({ compilerOptions, modules: { typescript: ts }, config: { options } }) => {
	const rootDir = options?.rootDir ?? compilerOptions.rootDir;
	if (!rootDir) console.warn("[vue-router] No rootDir specified. Set it in the Volar plugin options or tsconfig compilerOptions.rootDir for proper typed routes.");
	const RE = { DOLLAR_ROUTE: { 
	/**
	* When using `$route` in a template, it is referred
	* to as `__VLS_ctx.$route` in the virtual file.
	*/
VLS_CTX: /\b__VLS_ctx.\$route\b/g } };
	return {
		version: 2.1,
		resolveEmbeddedCode(fileName, sfc, embeddedCode) {
			if (!embeddedCode.id.startsWith("script_")) return;
			const escapedFilePath = (rootDir ? (0, pathe.relative)(rootDir, fileName) : fileName).replace(/\\/g, "\\\\").replace(/'/g, "\\'");
			const useRouteNameTypeParam = `<${`import('vue-router/auto-routes')._RouteNamesForFilePath<'${escapedFilePath}'>`}>`;
			const definePageFilePathTypeParam = `<'${escapedFilePath}'>`;
			if (sfc.scriptSetup) visit(sfc.scriptSetup.ast);
			function visit(node) {
				if (ts.isCallExpression(node) && ts.isIdentifier(node.expression) && ts.idText(node.expression) === "useRoute" && !node.typeArguments && !node.arguments.length) if (!sfc.scriptSetup.lang.startsWith("js")) (0, muggle_string.replaceSourceRange)(embeddedCode.content, sfc.scriptSetup.name, node.expression.end, node.expression.end, useRouteNameTypeParam);
				else {
					const { start, end } = getStartEnd(node, sfc.scriptSetup.ast);
					(0, muggle_string.replaceSourceRange)(embeddedCode.content, sfc.scriptSetup.name, start, start, `(`);
					(0, muggle_string.replaceSourceRange)(embeddedCode.content, sfc.scriptSetup.name, end, end, ` as ReturnType<typeof useRoute${useRouteNameTypeParam}>)`);
				}
				else if (ts.isCallExpression(node) && ts.isIdentifier(node.expression) && ts.idText(node.expression) === "definePage" && !node.typeArguments && node.arguments.length === 1 && !sfc.scriptSetup.lang.startsWith("js")) (0, muggle_string.replaceSourceRange)(embeddedCode.content, sfc.scriptSetup.name, node.expression.end, node.expression.end, definePageFilePathTypeParam);
				else ts.forEachChild(node, visit);
			}
			const contentStr = (0, muggle_string.toString)(embeddedCode.content);
			const vlsCtxAugmentations = [];
			if (contentStr.match(RE.DOLLAR_ROUTE.VLS_CTX)) vlsCtxAugmentations.push(`{} as { $route: ReturnType<typeof import('vue-router').useRoute${useRouteNameTypeParam}> }`);
			if (vlsCtxAugmentations.length) augmentVlsCtx(embeddedCode.content, vlsCtxAugmentations);
		}
	};
	function getStartEnd(node, ast) {
		return {
			start: ts.getTokenPosOfNode(node, ast),
			end: node.end
		};
	}
};
//#endregion
module.exports = plugin;
