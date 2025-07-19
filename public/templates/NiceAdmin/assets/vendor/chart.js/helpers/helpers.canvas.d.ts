/**
 * Note: typedefs are auto-exported, so use a made-up `canvas` namespace where
 * necessary to avoid duplicates with `export * from './helpers`; see
 * https://github.com/microsoft/TypeScript/issues/46011
 * @typedef { import('../core/core.controller.js').default } canvas.Chart
 * @typedef { import('../types/index.js').Point } Point
 */
/**
 * @namespace Chart.helpers.canvas
 */
/**
 * Converts the given font object into a CSS font string.
 * @param {object} font - A font object.
 * @return {string|null} The CSS font string. See https://developer.mozilla.org/en-US/docs/Web/CSS/font
 * @private
 */
export function toFontString(font: object): string | null;
/**
 * @private
 */
export function _measureText(ctx: any, data: any, gc: any, longest: any, string: any): any;
/**
 * @private
 */
export function _longestText(ctx: any, font: any, arrayOfThings: any, cache: any): number;
/**
 * Returns the aligned pixel value to avoid anti-aliasing blur
 * @param {canvas.Chart} chart - The chart instance.
 * @param {number} pixel - A pixel value.
 * @param {number} width - The width of the element.
 * @returns {number} The aligned pixel value.
 * @private
 */
export function _alignPixel(chart: canvas.Chart, pixel: number, width: number): number;
/**
 * Clears the entire canvas.
 * @param {HTMLCanvasElement} canvas
 * @param {CanvasRenderingContext2D} [ctx]
 */
export function clearCanvas(canvas: HTMLCanvasElement, ctx?: CanvasRenderingContext2D): void;
export function drawPoint(ctx: any, options: any, x: any, y: any): void;
export function drawPointLegend(ctx: any, options: any, x: any, y: any, w: any): void;
/**
 * Returns true if the point is inside the rectangle
 * @param {Point} point - The point to test
 * @param {object} area - The rectangle
 * @param {number} [margin] - allowed margin
 * @returns {boolean}
 * @private
 */
export function _isPointInArea(point: Point, area: object, margin?: number): boolean;
export function clipArea(ctx: any, area: any): void;
export function unclipArea(ctx: any): void;
/**
 * @private
 */
export function _steppedLineTo(ctx: any, previous: any, target: any, flip: any, mode: any): any;
/**
 * @private
 */
export function _bezierCurveTo(ctx: any, previous: any, target: any, flip: any): any;
/**
 * Render text onto the canvas
 */
export function renderText(ctx: any, text: any, x: any, y: any, font: any, opts?: {}): void;
/**
 * Add a path of a rectangle with rounded corners to the current sub-path
 * @param {CanvasRenderingContext2D} ctx Context
 * @param {*} rect Bounding rect
 */
export function addRoundedRectPath(ctx: CanvasRenderingContext2D, rect: any): void;
export namespace canvas {
    /**
     * Note: typedefs are auto-exported, so use a made-up `canvas` namespace where
     * necessary to avoid duplicates with `export * from './helpers`; see
     * https://github.com/microsoft/TypeScript/issues/46011
     */
    type Chart = import('../core/core.controller.js').default;
}
/**
 * Note: typedefs are auto-exported, so use a made-up `canvas` namespace where
 * necessary to avoid duplicates with `export * from './helpers`; see
 * https://github.com/microsoft/TypeScript/issues/46011
 */
export type Point = import('../types/index.js').Point;
