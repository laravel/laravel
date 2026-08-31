//#region src/formatters/ansi.ts
/* @__NO_SIDE_EFFECTS__ */
function ansiFormatter(colors) {
	return (d) => {
		const header = `${colors.bold(colors.red(`[${d.name}]`))} ${d.message}`;
		const details = [];
		if (d.fix) details.push(`${colors.dim("fix:")} ${d.fix}`);
		if (d.sources?.length) details.push(`${colors.dim("sources:")} ${d.sources.join(", ")}`);
		if (d.docs) details.push(`${colors.dim("see:")} ${colors.cyan(d.docs)}`);
		if (details.length === 0) return header;
		return [header, ...details.map((detail, i) => {
			return `${colors.dim(i < details.length - 1 ? "├▶" : "╰▶")} ${detail}`;
		})].join("\n");
	};
}
//#endregion
export { ansiFormatter };

//# sourceMappingURL=ansi.mjs.map