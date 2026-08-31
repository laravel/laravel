/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
let muggle_string = require("muggle-string");
//#region src/volar/entries/sfc-route-blocks.ts
const allCodeFeatures = {
	verification: true,
	completion: true,
	semantic: true,
	navigation: true,
	structure: true,
	format: true
};
const plugin = () => {
	const routeBlockIdPrefix = "route_";
	const routeBlockIdRe = new RegExp(`^${routeBlockIdPrefix}(\\d+)$`);
	return {
		version: 2.1,
		getEmbeddedCodes(_fileName, sfc) {
			const embeddedCodes = [];
			for (let i = 0; i < sfc.customBlocks.length; i++) {
				const block = sfc.customBlocks[i];
				if (block.type === "route") {
					const lang = block.lang === "txt" ? "json" : block.lang;
					embeddedCodes.push({
						id: `${routeBlockIdPrefix}${i}`,
						lang
					});
				}
			}
			return embeddedCodes;
		},
		resolveEmbeddedCode(_fileName, sfc, embeddedCode) {
			const match = embeddedCode.id.match(routeBlockIdRe);
			if (match) {
				const i = parseInt(match[1]);
				const block = sfc.customBlocks[i];
				if (!block) return;
				embeddedCode.content.push([
					block.content,
					block.name,
					0,
					allCodeFeatures
				]);
				if (embeddedCode.lang === "json") {
					const contentStr = (0, muggle_string.toString)(embeddedCode.content);
					if (contentStr.trim().startsWith("{") && !contentStr.includes("$schema")) (0, muggle_string.replace)(embeddedCode.content, "{", "{\n  \"$schema\": \"https://router.vuejs.org/schemas/route.schema.json\",");
				}
			}
		}
	};
};
//#endregion
module.exports = plugin;
