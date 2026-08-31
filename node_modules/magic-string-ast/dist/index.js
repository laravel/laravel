import MagicString from "magic-string";

export * from "magic-string"

//#region src/index.ts
/**
* MagicString with AST manipulation
*/
var MagicStringAST = class MagicStringAST {
	s;
	constructor(str, options, prototype = typeof str === "string" ? MagicString : str.constructor) {
		this.prototype = prototype;
		this.s = typeof str === "string" ? new prototype(str, options) : str;
		return new Proxy(this.s, { get: (target, p, receiver) => {
			if (Reflect.has(this, p)) return Reflect.get(this, p, receiver);
			let parent = Reflect.get(target, p, receiver);
			if (typeof parent === "function") parent = parent.bind(target);
			return parent;
		} });
	}
	getNodePos(nodes, offset = 0) {
		if (Array.isArray(nodes)) return [offset + nodes[0].start, offset + nodes.at(-1).end];
		else return [offset + nodes.start, offset + nodes.end];
	}
	removeNode(node, { offset } = {}) {
		if (isEmptyNodes(node)) return this;
		this.s.remove(...this.getNodePos(node, offset));
		return this;
	}
	moveNode(node, index, { offset } = {}) {
		if (isEmptyNodes(node)) return this;
		this.s.move(...this.getNodePos(node, offset), index);
		return this;
	}
	sliceNode(node, { offset } = {}) {
		if (isEmptyNodes(node)) return "";
		return this.s.slice(...this.getNodePos(node, offset));
	}
	overwriteNode(node, content, { offset,...options } = {}) {
		if (isEmptyNodes(node)) return this;
		const _content = typeof content === "string" ? content : this.sliceNode(content);
		this.s.overwrite(...this.getNodePos(node, offset), _content, options);
		return this;
	}
	snipNode(node, { offset } = {}) {
		let newS;
		if (isEmptyNodes(node)) newS = this.s.snip(0, 0);
		else newS = this.s.snip(...this.getNodePos(node, offset));
		return new MagicStringAST(newS, void 0, this.prototype);
	}
	clone() {
		return new MagicStringAST(this.s.clone(), void 0, this.prototype);
	}
	toString() {
		return this.s.toString();
	}
	replaceRangeState = {};
	/**
	* Replace a range of text with new nodes.
	* @param start The start index of the range to replace.
	* @param end The end index of the range to replace.
	* @param nodes The nodes or strings to insert into the range.
	*/
	replaceRange(start, end, ...nodes) {
		const state = this.replaceRangeState[this.offset] || (this.replaceRangeState[this.offset] = {
			nodes: [],
			indexes: {}
		});
		if (nodes.length) {
			let index = state.indexes[start] || 0;
			let intro = "";
			let prevNode;
			for (const node of nodes) if (typeof node === "string") node && (intro += node);
			else {
				this.move(node.start, node.end, start);
				index = node.start;
				prevNode = node;
				if (intro) {
					this.appendRight(index, intro);
					intro = "";
				}
				state.nodes.push(node);
			}
			if (intro) this.appendLeft(prevNode?.end || start, intro);
			state.indexes[start] = index;
		}
		if (end > start) {
			let index = start;
			state.nodes.filter((node) => node.start >= start && node.end <= end).sort((a, b) => a.start - b.start).forEach((node) => {
				if (node.start > index) this.remove(index, node.start);
				index = node.end;
			});
			this.remove(index, end);
		}
		return this;
	}
};
function isEmptyNodes(nodes) {
	return Array.isArray(nodes) && nodes.length === 0;
}
/**
* Generate an object of code and source map from MagicString.
*/
function generateTransform(s, id) {
	if (s?.hasChanged()) return {
		code: s.toString(),
		get map() {
			return s.generateMap({
				source: id,
				includeContent: true,
				hires: "boundary"
			});
		}
	};
}

//#endregion
export { MagicString, MagicStringAST, generateTransform };