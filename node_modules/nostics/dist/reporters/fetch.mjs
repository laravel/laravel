//#region src/reporters/fetch.ts
/**
* Creates a reporter that POSTs each diagnostic as JSON to the given URL.
* Errors are swallowed so reporting never throws into user code.
*/
/* @__NO_SIDE_EFFECTS__ */
function createFetchReporter(url) {
	return (diagnostic) => {
		fetch(url, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify(diagnostic)
		}).catch(() => {});
	};
}
//#endregion
export { createFetchReporter };

//# sourceMappingURL=fetch.mjs.map