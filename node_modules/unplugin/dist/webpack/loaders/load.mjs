import { r as normalizeObjectHook } from "../../parse-Da_tZNBK.mjs";
import { t as normalizeAbsolutePath } from "../../webpack-like-BSQQnmcP.mjs";
import { n as createBuildContext, r as createContext } from "../../context-CUiL2Gcl.mjs";
//#region src/webpack/loaders/load.ts
async function load(source, map) {
	const callback = this.async();
	const { plugin } = this.query;
	let id = this.resource;
	if (!plugin?.load || !id) return callback(null, source, map);
	if (id.startsWith(plugin.__virtualModulePrefix)) id = decodeURIComponent(id.slice(plugin.__virtualModulePrefix.length));
	const context = createContext(this);
	const { handler } = normalizeObjectHook("load", plugin.load);
	const res = await handler.call(Object.assign({}, createBuildContext({
		addWatchFile: (file) => {
			this.addDependency(file);
		},
		getWatchFiles: () => {
			return this.getDependencies();
		}
	}, this._compiler, this._compilation, this), context), normalizeAbsolutePath(id));
	if (res == null) callback(null, source, map);
	else if (typeof res !== "string") callback(null, res.code, res.map ?? map);
	else callback(null, res, map);
}
//#endregion
export { load as default };
