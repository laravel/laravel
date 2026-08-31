/*!
 * vue-router v5.2.0
 * (c) 2026 Eduardo San Martin Morote
 * @license MIT
 */
import { AllowedComponentProps, AnchorHTMLAttributes, App, Component as Component$1, ComponentCustomProps, ComponentPublicInstance, ComputedRef, DefineComponent, EffectScope, InjectionKey, MaybeRef, Ref, ShallowRef, UnwrapRef, VNode, VNodeProps } from "vue";
//#region src/experimental/data-loaders/symbols.d.ts
/**
 * Retrieves the internal version of loaders.
 * @internal
 */
declare const LOADER_SET_KEY: unique symbol;
/**
 * Retrieves the internal version of loader entries.
 * @internal
 */
declare const LOADER_ENTRIES_KEY: unique symbol;
/**
 * Added to the loaders returned by `defineLoader()` to identify them.
 * Allows to extract exported useData() from a component.
 * @internal
 */
declare const IS_USE_DATA_LOADER_KEY: unique symbol;
/**
 * Symbol used to save the pending location on the router.
 * @internal
 */
declare const PENDING_LOCATION_KEY: unique symbol;
/**
 * Symbol used to know there is no value staged for the loader and that commit should be skipped.
 * @internal
 */
declare const STAGED_NO_VALUE: unique symbol;
/**
 * Gives access to the current app and it's `runWithContext` method.
 * @internal
 */
declare const APP_KEY: unique symbol;
/**
 * Gives access to an AbortController that aborts when the navigation is canceled.
 * @internal
 */
declare const ABORT_CONTROLLER_KEY: unique symbol;
/**
 * Symbol used to save the initial data on the router.
 * @internal
 */
declare const IS_SSR_KEY: unique symbol;
/**
 * Symbol used to get the effect scope used for data loaders.
 * @internal
 */
declare const DATA_LOADERS_EFFECT_SCOPE_KEY: unique symbol;
//#endregion
//#region src/config.d.ts
/**
 * Allows customizing existing types of the router that are used globally like `$router`, `<RouterLink>`, etc. **ONLY FOR INTERNAL USAGE**.
 *
 * - `Router` - swaps the public {@link Router} type (e.g. to `EXPERIMENTAL_Router`)
 * - `$router` - the router instance
 * - `$route` - the current route location
 * - `beforeRouteEnter` - Page component option
 * - `beforeRouteUpdate` - Page component option
 * - `beforeRouteLeave` - Page component option
 * - `RouterLink` - RouterLink Component
 * - `RouterView` - RouterView Component
 *
 * @internal
 */
interface TypesConfig {}
//#endregion
//#region src/query.d.ts
/**
 * Possible values in normalized {@link LocationQuery}. `null` renders the query
 * param but without an `=`.
 *
 * @example
 * ```
 * ?isNull&isEmpty=&other=other
 * gives
 * `{ isNull: null, isEmpty: '', other: 'other' }`.
 * ```
 *
 * @internal
 */
type LocationQueryValue$1 = string | null;
/**
 * Possible values when defining a query. `undefined` allows to remove a value.
 *
 * @internal
 */
type LocationQueryValueRaw$1 = LocationQueryValue$1 | number | undefined;
/**
 * Normalized query object that appears in {@link RouteLocationNormalized}
 *
 * @public
 */
type LocationQuery$1 = Record<string, LocationQueryValue$1 | LocationQueryValue$1[]>;
/**
 * Loose {@link LocationQuery} object that can be passed to functions like
 * {@link Router.push} and {@link Router.replace} or anywhere when creating a
 * {@link RouteLocationRaw}
 *
 * @public
 */
type LocationQueryRaw$1 = Record<string | number, LocationQueryValueRaw$1 | LocationQueryValueRaw$1[]>;
/**
 * Transforms a queryString into a {@link LocationQuery} object. Accept both, a
 * version with the leading `?` and without Should work as URLSearchParams
 * @internal
 *
 * @param search - search string to parse
 * @returns a query object
 */
declare function parseQuery(search: string): LocationQuery$1;
/**
 * Stringifies a {@link LocationQueryRaw} object. Like `URLSearchParams`, it
 * doesn't prepend a `?`
 *
 * @internal
 *
 * @param query - query object to stringify
 * @returns string version of the query without the leading `?`
 */
declare function stringifyQuery(query: LocationQueryRaw$1 | undefined): string;
//#endregion
//#region src/matcher/types.d.ts
/**
 * Normalized version of a {@link RouteRecord | route record}.
 */
interface RouteRecordNormalized {
  /**
   * {@inheritDoc _RouteRecordBase.path}
   */
  path: _RouteRecordBase['path'];
  /**
   * {@inheritDoc _RouteRecordBase.redirect}
   */
  redirect: _RouteRecordBase['redirect'] | undefined;
  /**
   * {@inheritDoc _RouteRecordBase.name}
   */
  name: _RouteRecordBase['name'];
  /**
   * {@inheritDoc RouteRecordMultipleViews.components}
   */
  components: RouteRecordMultipleViews['components'] | null | undefined;
  /**
   * Contains the original modules for lazy loaded components.
   * @internal
   */
  mods: Record<string, unknown>;
  /**
   * Nested route records.
   */
  children: RouteRecordRaw[];
  /**
   * {@inheritDoc _RouteRecordBase.meta}
   */
  meta: Exclude<_RouteRecordBase['meta'], void>;
  /**
   * {@inheritDoc RouteRecordMultipleViews.props}
   */
  props: Record<string, _RouteRecordProps>;
  /**
   * Registered beforeEnter guards
   */
  beforeEnter: _RouteRecordBase['beforeEnter'];
  /**
   * Registered leave guards
   *
   * @internal
   */
  leaveGuards: Set<NavigationGuard>;
  /**
   * Registered update guards
   *
   * @internal
   */
  updateGuards: Set<NavigationGuard>;
  /**
   * Registered beforeRouteEnter callbacks passed to `next` or returned in guards
   *
   * @internal
   */
  enterCallbacks: Record<string, NavigationGuardNextCallback[]>;
  /**
   * Mounted route component instances
   * Having the instances on the record mean beforeRouteUpdate and
   * beforeRouteLeave guards can only be invoked with the latest mounted app
   * instance if there are multiple application instances rendering the same
   * view, basically duplicating the content on the page, which shouldn't happen
   * in practice. It will work if multiple apps are rendering different named
   * views.
   */
  instances: Record<string, ComponentPublicInstance | undefined | null>;
  /**
   * Defines if this record is the alias of another one. This property is
   * `undefined` if the record is the original one.
   */
  aliasOf: RouteRecordNormalized | undefined;
}
/**
 * {@inheritDoc RouteRecordNormalized}
 */
type RouteRecord = RouteRecordNormalized;
//#endregion
//#region src/matcher/pathParserRanker.d.ts
type PathParams = Record<string, string | string[]>;
/**
 * A param in a url like `/users/:id`
 */
interface PathParserParamKey {
  name: string;
  repeatable: boolean;
  optional: boolean;
}
interface PathParser {
  /**
   * The regexp used to match a url
   */
  re: RegExp;
  /**
   * The score of the parser
   */
  score: Array<number[]>;
  /**
   * Keys that appeared in the path
   */
  keys: PathParserParamKey[];
  /**
   * Parses a url and returns the matched params or null if it doesn't match. An
   * optional param that isn't preset will be an empty string. A repeatable
   * param will be an array if there is at least one value.
   *
   * @param path - url to parse
   * @returns a Params object, empty if there are no params. `null` if there is
   * no match
   */
  parse(path: string): PathParams | null;
  /**
   * Creates a string version of the url
   *
   * @param params - object of params
   * @returns a url
   */
  stringify(params: PathParams): string;
}
/**
 * @internal
 */
interface _PathParserOptions {
  /**
   * Makes the RegExp case-sensitive.
   *
   * @defaultValue `false`
   */
  sensitive?: boolean;
  /**
   * Whether to disallow a trailing slash or not.
   *
   * @defaultValue `false`
   */
  strict?: boolean;
  /**
   * Should the RegExp match from the beginning by prepending a `^` to it.
   * @internal
   *
   * @defaultValue `true`
   */
  start?: boolean;
  /**
   * Should the RegExp match until the end by appending a `$` to it.
   *
   * @deprecated this option will alsways be `true` in the future. Open a discussion in vuejs/router if you need this to be `false`
   *
   * @defaultValue `true`
   */
  end?: boolean;
}
type PathParserOptions = Pick<_PathParserOptions, 'end' | 'sensitive' | 'strict'>;
//#endregion
//#region src/matcher/pathMatcher.d.ts
interface RouteRecordMatcher extends PathParser {
  record: RouteRecord;
  parent: RouteRecordMatcher | undefined;
  children: RouteRecordMatcher[];
  alias: RouteRecordMatcher[];
}
//#endregion
//#region src/matcher/index.d.ts
/**
 * Internal RouterMatcher
 *
 * @internal
 */
interface RouterMatcher {
  addRoute: (record: RouteRecordRaw, parent?: RouteRecordMatcher) => () => void;
  removeRoute(matcher: RouteRecordMatcher): void;
  removeRoute(name: NonNullable<RouteRecordNameGeneric>): void;
  clearRoutes: () => void;
  getRoutes: () => RouteRecordMatcher[];
  getRecordMatcher: (name: NonNullable<RouteRecordNameGeneric>) => RouteRecordMatcher | undefined;
  /**
   * Resolves a location. Gives access to the route record that corresponds to the actual path as well as filling the corresponding params objects
   *
   * @param location - MatcherLocationRaw to resolve to a url
   * @param currentLocation - MatcherLocation of the current location
   */
  resolve: (location: MatcherLocationRaw, currentLocation: MatcherLocation) => MatcherLocation;
}
/**
 * Creates a Router Matcher.
 *
 * @internal
 * @param routes - array of initial routes
 * @param globalOptions - global route options
 */
declare function createRouterMatcher(routes: Readonly<RouteRecordRaw[]>, globalOptions: PathParserOptions): RouterMatcher;
//#endregion
//#region src/history/common.d.ts
type HistoryLocation = string;
/**
 * Allowed variables in HTML5 history state. Note that pushState clones the state
 * passed and does not accept everything: e.g.: it doesn't accept symbols, nor
 * functions as values. It also ignores Symbols as keys.
 *
 * @internal
 */
type HistoryStateValue = string | number | boolean | null | undefined | HistoryState | HistoryStateArray;
/**
 * Allowed HTML history.state
 */
interface HistoryState {
  [x: number]: HistoryStateValue;
  [x: string]: HistoryStateValue;
}
/**
 * Allowed arrays for history.state.
 *
 * @internal
 */
interface HistoryStateArray extends Array<HistoryStateValue> {}
declare enum NavigationType {
  pop = "pop",
  push = "push"
}
declare enum NavigationDirection {
  back = "back",
  forward = "forward",
  unknown = ""
}
interface NavigationInformation {
  type: NavigationType;
  direction: NavigationDirection;
  delta: number;
}
interface NavigationCallback {
  (to: HistoryLocation, from: HistoryLocation, information: NavigationInformation): void;
}
/**
 * Interface implemented by History implementations that can be passed to the
 * router as {@link Router.history}
 *
 * @alpha
 */
interface RouterHistory {
  /**
   * Base path that is prepended to every url. This allows hosting an SPA at a
   * sub-folder of a domain like `example.com/sub-folder` by having a `base` of
   * `/sub-folder`
   */
  readonly base: string;
  /**
   * Current History location
   */
  readonly location: HistoryLocation;
  /**
   * Current History state
   */
  readonly state: HistoryState;
  /**
   * Navigates to a location. In the case of an HTML5 History implementation,
   * this will call `history.pushState` to effectively change the URL.
   *
   * @param to - location to push
   * @param data - optional {@link HistoryState} to be associated with the
   * navigation entry
   */
  push(to: HistoryLocation, data?: HistoryState): void;
  /**
   * Same as {@link RouterHistory.push} but performs a `history.replaceState`
   * instead of `history.pushState`
   *
   * @param to - location to set
   * @param data - optional {@link HistoryState} to be associated with the
   * navigation entry
   */
  replace(to: HistoryLocation, data?: HistoryState): void;
  /**
   * Traverses history in a given direction.
   *
   * @example
   * ```js
   * myHistory.go(-1) // equivalent to window.history.back()
   * myHistory.go(1) // equivalent to window.history.forward()
   * ```
   *
   * @param delta - distance to travel. If delta is \< 0, it will go back,
   * if it's \> 0, it will go forward by that amount of entries.
   * @param triggerListeners - whether this should trigger listeners attached to
   * the history
   */
  go(delta: number, triggerListeners?: boolean): void;
  /**
   * Attach a listener to the History implementation that is triggered when the
   * navigation is triggered from outside (like the Browser back and forward
   * buttons) or when passing `true` to {@link RouterHistory.back} and
   * {@link RouterHistory.forward}
   *
   * @param callback - listener to attach
   * @returns a callback to remove the listener
   */
  listen(callback: NavigationCallback): () => void;
  /**
   * Generates the corresponding href to be used in an anchor tag.
   *
   * @param location - history location that should create an href
   */
  createHref(location: HistoryLocation): string;
  /**
   * Clears any event listener attached by the history implementation.
   */
  destroy(): void;
}
//#endregion
//#region src/errors.d.ts
/**
 * Flags so we can combine them when checking for multiple errors. This is the internal version of
 * {@link NavigationFailureType}.
 *
 * @internal
 */
declare const enum ErrorTypes {
  MATCHER_NOT_FOUND = 1,
  NAVIGATION_GUARD_REDIRECT = 2,
  NAVIGATION_ABORTED = 4,
  NAVIGATION_CANCELLED = 8,
  NAVIGATION_DUPLICATED = 16
}
/**
 * Enumeration with all possible types for navigation failures. Can be passed to
 * {@link isNavigationFailure} to check for specific failures.
 */
declare enum NavigationFailureType {
  /**
   * An aborted navigation is a navigation that failed because a navigation
   * guard returned `false` or called `next(false)`
   */
  aborted = 4,
  /**
   * A cancelled navigation is a navigation that failed because a more recent
   * navigation finished started (not necessarily finished).
   */
  cancelled = 8,
  /**
   * A duplicated navigation is a navigation that failed because it was
   * initiated while already being at the exact same location.
   */
  duplicated = 16
}
/**
 * Extended Error that contains extra information regarding a failed navigation.
 */
interface NavigationFailure extends Error {
  /**
   * Type of the navigation. One of {@link NavigationFailureType}
   */
  type: ErrorTypes.NAVIGATION_CANCELLED | ErrorTypes.NAVIGATION_ABORTED | ErrorTypes.NAVIGATION_DUPLICATED;
  /**
   * Route location we were navigating from
   */
  from: RouteLocationNormalized;
  /**
   * Route location we were navigating to
   */
  to: RouteLocationNormalized;
}
/**
 * Internal error used to detect a redirection.
 *
 * @internal
 */
interface NavigationRedirectError extends Omit<NavigationFailure, 'to' | 'type'> {
  type: ErrorTypes.NAVIGATION_GUARD_REDIRECT;
  to: RouteLocationRaw;
}
/**
 * Check if an object is a {@link NavigationFailure}.
 *
 * @param error - possible {@link NavigationFailure}
 * @param type - optional types to check for
 *
 * @example
 * ```js
 * import { isNavigationFailure, NavigationFailureType } from 'vue-router'
 *
 * router.afterEach((to, from, failure) => {
 *   // Any kind of navigation failure
 *   if (isNavigationFailure(failure)) {
 *     // ...
 *   }
 *   // Only duplicated navigations
 *   if (isNavigationFailure(failure, NavigationFailureType.duplicated)) {
 *     // ...
 *   }
 *   // Aborted or canceled navigations
 *   if (isNavigationFailure(failure, NavigationFailureType.aborted | NavigationFailureType.cancelled )) {
 *     // ...
 *   }
 * })
 * ```
 */
declare function isNavigationFailure(error: any, type?: ErrorTypes.NAVIGATION_GUARD_REDIRECT): error is NavigationRedirectError;
declare function isNavigationFailure(error: any, type?: ErrorTypes | NavigationFailureType): error is NavigationFailure;
/**
 * Internal type to define an ErrorHandler
 *
 * @param error - error thrown
 * @param to - location we were navigating to when the error happened
 * @param from - location we were navigating from when the error happened
 * @internal
 */
interface _ErrorListener {
  (error: any, to: RouteLocationNormalized, from: RouteLocationNormalizedLoaded): any;
}
//#endregion
//#region src/experimental/query.d.ts
/**
 * NOTE: some types here are duplicated from ../query.ts
 * because they will change to always have (string | null)[] values
 */
/**
 * Possible values in normalized {@link LocationQuery}. `null` renders the query
 * param but without an `=`.
 *
 * @internal
 */
type LocationQueryValue = string | null;
/**
 * Possible values when defining a query. `undefined` allows to remove a value.
 *
 * @internal
 */
type LocationQueryValueRaw = LocationQueryValue | number | undefined;
/**
 * Normalized query object that appears in {@link RouteLocationNormalized}
 *
 * @public
 */
type LocationQuery = Record<string, LocationQueryValue | LocationQueryValue[]>;
/**
 * Loose {@link LocationQuery} object that can be passed to functions like
 * {@link Router.push} and {@link Router.replace} or anywhere when creating a
 * {@link RouteLocationRaw}
 *
 * @public
 */
type LocationQueryRaw = Record<string | number, LocationQueryValueRaw | LocationQueryValueRaw[]>;
/**
 * Transforms a queryString into a {@link LocationQuery} object. Accept both, a
 * version with the leading `?` and without. Uses `Object.create(null)` so the
 * returned object cannot be exploited via prototype pollution.
 *
 * @internal
 *
 * @param search - search string to parse
 * @returns a query object
 */
declare function experimental_parseQuery(search: string): LocationQuery;
//#endregion
//#region src/scrollBehavior.d.ts
/**
 * Scroll position similar to
 * {@link https://developer.mozilla.org/en-US/docs/Web/API/ScrollToOptions | `ScrollToOptions`}.
 * Note that not all browsers support `behavior`.
 */
type ScrollPositionCoordinates = {
  behavior?: ScrollOptions['behavior'];
  left?: number;
  top?: number;
};
/**
 * Internal normalized version of {@link ScrollPositionCoordinates} that always
 * has `left` and `top` coordinates. Must be a type to be assignable to HistoryStateValue.
 *
 * @internal
 */
type _ScrollPositionNormalized = {
  behavior?: ScrollOptions['behavior'];
  left: number;
  top: number;
};
/**
 * Type of the `scrollBehavior` option that can be passed to `createRouter`.
 */
interface RouterScrollBehavior {
  /**
   * @param to - Route location where we are navigating to
   * @param from - Route location where we are navigating from
   * @param savedPosition - saved position if it exists, `null` otherwise
   */
  (to: RouteLocationNormalized, from: RouteLocationNormalizedLoaded, savedPosition: _ScrollPositionNormalized | null): Awaitable<ScrollPosition | false | void>;
}
interface ScrollPositionElement extends ScrollToOptions {
  /**
   * A valid CSS selector. Note some characters must be escaped in id selectors (https://mathiasbynens.be/notes/css-escapes).
   * @example
   * Here are a few examples:
   *
   * - `.title`
   * - `.content:first-child`
   * - `#marker`
   * - `#marker\~with\~symbols`
   * - `#marker.with.dot`: selects `class="with dot" id="marker"`, not `id="marker.with.dot"`
   *
   */
  el: string | Element;
}
type ScrollPosition = ScrollPositionCoordinates | ScrollPositionElement;
type Awaitable<T> = T | PromiseLike<T>;
//#endregion
//#region src/experimental/route-resolver/matchers/param-parsers/types.d.ts
/**
 * Defines a parser that can read a param from the url (string-based) and
 * transform it into a more complex type, or vice versa.
 *
 * @param TParam - the type of the param after parsing as exposed in
 * `route.params`
 *
 * @param TUrlParam - this is the most permissive type that can be passed to
 * get and returned by set. By default it's the type of query path params
 * (stricter as they do not allow `null` within an array or `undefined`). By
 * default it allows undefined because that represents a value that can be
 * omitted and is different from null in query params
 *
 * @param TParamRaw - the type that can be passed as a location when
 * navigating: `router.push({ params: {}})` it's sometimes more permissive than
 * TParam, for example allowing nullish values
 *
 * @see {MatcherPattern}
 */
interface ParamParser<TParam = MatcherQueryParamsValue, TUrlParam = MatcherQueryParamsValue, TParamRaw = TParam> {
  get?: (value: NoInfer<TUrlParam>) => TParam;
  set?: (value: TParamRaw) => TUrlParam;
}
//#endregion
//#region src/types/utils.d.ts
/**
 * Creates a union type that still allows autocompletion for strings.
 * @internal
 */
type _LiteralUnion<LiteralType, BaseType extends string = string> = LiteralType | (BaseType & Record<never, never>);
/**
 * Maybe a promise maybe not
 * @internal
 */
type _Awaitable<T> = T | PromiseLike<T>;
/**
 * @internal
 */
type Simplify<T> = { [K in keyof T]: T[K]; } & {};
//#endregion
//#region src/experimental/route-resolver/matchers/matcher-pattern.d.ts
/**
 * Base interface for matcher patterns that extract params from a URL.
 *
 * @template TIn - type of the input value to match against the pattern
 * @template TParams - type of the output value after matching
 * @template TParamsRaw - type of the input value to build the input from
 *
 * In the case of the `path`, the `TIn` is a `string`, but in the case of the
 * query, it's the object of query params. `TParamsRaw` allows for a more permissive
 * type when building the value, for example allowing numbers and strings like
 * the old params.
 *
 * @internal this is the base interface for all matcher patterns, it shouldn't
 * be used directly
 */
interface MatcherPattern<TIn = string, TParams extends MatcherParamsFormatted = MatcherParamsFormatted, TParamsRaw extends MatcherParamsFormatted = TParams> {
  /**
   * Matches a serialized params value against the pattern.
   *
   * @param value - params value to parse
   * @throws {MatchMiss} if the value doesn't match
   * @returns parsed params object
   */
  match(value: TIn): TParams;
  /**
   * Build a serializable value from parsed params. Should apply encoding if the
   * returned value is a string (e.g path and hash should be encoded but query
   * shouldn't).
   *
   * @param value - params value to parse
   * @returns serialized params value
   */
  build(params: TParamsRaw): TIn;
}
/**
 * Handles the `path` part of a URL. It can transform a path string into an
 * object of params and vice versa.
 */
interface MatcherPatternPath<TParams extends MatcherParamsFormatted = MatcherParamsFormatted // | null // | undefined // | void // so it might be a bit more convenient
, TParamsRaw extends MatcherParamsFormatted = TParams> extends MatcherPattern<string, TParams, TParamsRaw> {}
/**
 * Allows matching a static path.
 *
 * @example
 * ```ts
 * const matcher = new MatcherPatternPathStatic('/team')
 * matcher.match('/team') // {}
 * matcher.match('/team/123') // throws MatchMiss
 * matcher.build() // '/team'
 * ```
 */
declare class MatcherPatternPathStatic implements MatcherPatternPath<EmptyParams> {
  readonly path: string;
  /**
   * lowercase version of the path to match against.
   * This is used to make the matching case insensitive.
   */
  private pathi;
  constructor(path: string);
  match(path: string): EmptyParams;
  build(): string;
}
/**
 * Options for param parsers in {@link MatcherPatternPathDynamic}.
 */
type MatcherPatternPathDynamic_ParamOptions<TUrlParam extends string | string[] | null = string | string[] | null, TParam = string | string[] | null, TParamRaw = TParam> = readonly [
/**
 * Param parser to use for this param.
 */
parser?: ParamParser<TParam, TUrlParam, TParamRaw>,
/**
 * Is tha param a repeatable param and should be converted to an array
 */
repeatable?: boolean,
/**
 * Can this parameter be omitted or empty (for repeatable params, an empty array).
 */
optional?: boolean];
/**
 * Helper type to extract the params from the options object.
 *
 * @internal
 */
type ExtractParamTypeFromOptions<TParamsOptions> = { [K in keyof TParamsOptions]: TParamsOptions[K] extends MatcherPatternPathDynamic_ParamOptions<any, infer TParam, any> ? TParam : never; };
/**
 * Helper type to extract the raw params from the options object.
 *
 * @internal
 */
type ExtractLocationParamTypeFromOptions<TParamsOptions> = { [K in keyof TParamsOptions]: TParamsOptions[K] extends MatcherPatternPathDynamic_ParamOptions<any, any, infer TParamRaw> ? TParamRaw : never; };
/**
 * Handles the `path` part of a URL with dynamic parameters.
 */
declare class MatcherPatternPathDynamic<TParamsOptions> implements MatcherPatternPath<ExtractParamTypeFromOptions<TParamsOptions>, ExtractLocationParamTypeFromOptions<TParamsOptions>> {
  readonly re: RegExp;
  readonly params: TParamsOptions & Record<string, MatcherPatternPathDynamic_ParamOptions<any, any>>;
  readonly pathParts: Array<string | number | Array<string | number>>;
  readonly trailingSlash: boolean | null;
  /**
   * Cached keys of the {@link params} object.
   */
  private paramsKeys;
  /**
   * Creates a new dynamic path matcher.
   *
   * @param re - regex to match the path against
   * @param params - object of param parsers as {@link MatcherPatternPathDynamic_ParamOptions}
   * @param pathParts - array of path parts, where strings are static parts, 1 are regular params, and 0 are splat params (not encode slash)
   * @param trailingSlash - whether the path should end with a trailing slash, null means "do not care" (for trailing splat params)
   */
  constructor(re: RegExp, params: TParamsOptions & Record<string, MatcherPatternPathDynamic_ParamOptions<any, any>>, pathParts: Array<string | number | Array<string | number>>, trailingSlash?: boolean | null);
  match(path: string): Simplify<ExtractParamTypeFromOptions<TParamsOptions>>;
  build(params: Simplify<ExtractLocationParamTypeFromOptions<TParamsOptions>>): string;
}
/**
 * Handles the `hash` part of a URL. It can transform a hash string into an
 * object of params and vice versa.
 */
interface MatcherPatternHash<TParams extends MatcherParamsFormatted = MatcherParamsFormatted> extends MatcherPattern<string, TParams> {}
/**
 * Generic object of params that can be passed to a matcher.
 */
type MatcherParamsFormatted = Record<string, unknown>;
/**
 * Empty object in TS.
 */
type EmptyParams = Record<PropertyKey, never>;
/**
 * Possible values for query params in a matcher.
 */
type MatcherQueryParamsValue = string | null | undefined | Array<string | null>;
type MatcherQueryParams = Record<string, MatcherQueryParamsValue>;
//#endregion
//#region src/location.d.ts
/**
 * Location object returned by {@link `parseURL`}.
 * @internal
 */
interface LocationNormalized {
  path: string;
  fullPath: string;
  hash: string;
  query: LocationQuery$1;
}
/**
 * Initial route location where the router is. Can be used in navigation guards
 * to differentiate the initial navigation.
 *
 * @example
 * ```js
 * import { START_LOCATION } from 'vue-router'
 *
 * router.beforeEach((to, from) => {
 *   if (from === START_LOCATION) {
 *     // initial navigation
 *   }
 * })
 * ```
 */
declare const START_LOCATION_NORMALIZED: RouteLocationNormalizedLoaded;
//#endregion
//#region src/experimental/route-resolver/resolver-abstract.d.ts
/**
 * Allowed types for a matcher name.
 */
type RecordName = string | symbol;
/**
 * Manage and resolve routes. Also handles the encoding, decoding, parsing and
 * serialization of params, query, and hash.
 *
 * - `TMatcherRecordRaw` represents the raw record type passed to {@link addMatcher}.
 * - `TMatcherRecord` represents the normalized record type returned by {@link getRoutes}.
 */
interface EXPERIMENTAL_Resolver_Base<TRecord> {
  /**
   * Resolves an absolute location (like `/path/to/somewhere`).
   *
   * @param absoluteLocation - The absolute location to resolve.
   * @param currentLocation - This value is ignored and should not be passed if the location is absolute.
   */
  resolve(absoluteLocation: `/${string}`, currentLocation?: undefined): ResolverLocationResolved<TRecord>;
  /**
   * Resolves a string location relative to another location. A relative
   * location can be `./same-folder`, `../parent-folder`, `same-folder`, or
   * even `?page=2`.
   */
  resolve(relativeLocation: string, currentLocation: ResolverLocationResolved<TRecord>): ResolverLocationResolved<TRecord>;
  /**
   * Resolves a location by its name. Any required params or query must be
   * passed in the `options` argument.
   */
  resolve(location: ResolverLocationAsNamed, currentLocation?: undefined): ResolverLocationResolved<TRecord>;
  /**
   * Resolves a location by its absolute path (starts with `/`). Any required query must be passed.
   *
   * @param location - The location to resolve.
   */
  resolve(location: ResolverLocationAsPathAbsolute, currentLocation?: undefined): ResolverLocationResolved<TRecord>;
  resolve(location: ResolverLocationAsPathRelative, currentLocation: ResolverLocationResolved<TRecord>): ResolverLocationResolved<TRecord>;
  /**
   * Resolves a location relative to another location. It reuses existing
   * properties in the `currentLocation` like `params`, `query`, and `hash`.
   */
  resolve(relativeLocation: ResolverLocationAsRelative, currentLocation: ResolverLocationResolved<TRecord>): ResolverLocationResolved<TRecord>;
  /**
   * Get a list of all resolver route records.
   */
  getRoutes(): TRecord[];
  /**
   * Get a resolver record by its name.
   * Previously named `getRecordMatcher()`
   */
  getRoute(name: RecordName): TRecord | undefined;
}
/**
 * Returned location object by {@link EXPERIMENTAL_Resolver_Base['resolve']}.
 * It contains the resolved name, params, query, hash, and matched records.
 */
interface ResolverLocationResolved<TMatched> extends LocationNormalized {
  /**
   * Name of the route record. A symbol if no name is provided.
   */
  name: RecordName;
  /**
   * Parsed params. Already decoded and formatted.
   */
  params: MatcherParamsFormatted;
  /**
   * Chain of route records that lead to the matched one. The last record is
   * the the one that matched the location. Each previous record is the parent
   * of the next one.
   */
  matched: TMatched[];
}
/**
 * Location object that can be passed to {@link
 * EXPERIMENTAL_Resolver_Base['resolve']} and is recognized as a `name`.
 *
 * @example
 * ```ts
 * resolver.resolve({ name: 'user', params: { id: 123 } })
 * resolver.resolve({ name: 'user-search', params: {}, query: { page: 2 } })
 * ```
 */
interface ResolverLocationAsNamed {
  name: RecordName;
  params: MatcherParamsFormatted;
  query?: LocationQueryRaw;
  hash?: string;
  /**
   * @deprecated This is ignored when `name` is provided
   */
  path?: undefined;
}
/**
 * Location object that can be passed to {@link EXPERIMENTAL_Resolver_Base['resolve']}
 * and is recognized as a relative path.
 *
 * @example
 * ```ts
 * resolver.resolve({ path: './123' }, currentLocation)
 * resolver.resolve({ path: '..' }, currentLocation)
 * ```
 */
interface ResolverLocationAsPathRelative {
  path: string;
  query?: LocationQueryRaw;
  hash?: string;
  /**
   * @deprecated This is ignored when `path` is provided
   */
  name?: undefined;
  /**
   * @deprecated This is ignored when `path` (instead of `name`) is provided
   */
  params?: undefined;
}
/**
 * Location object that can be passed to {@link EXPERIMENTAL_Resolver_Base['resolve']}
 * and is recognized as an absolute path.
 *
 * @example
 * ```ts
 * resolver.resolve({ path: '/team/123' })
 * ```
 */
interface ResolverLocationAsPathAbsolute extends ResolverLocationAsPathRelative {
  path: `/${string}`;
}
/**
 * Relative location object that can be passed to {@link EXPERIMENTAL_Resolver_Base['resolve']}
 * and is recognized as a relative location, copying the `params`, `query`, and
 * `hash` if not provided.
 *
 * @example
 * ```ts
 * resolver.resolve({ params: { id: 123 } }, currentLocation)
 * resolver.resolve({ hash: '#bottom' }, currentLocation)
 * ```
 */
interface ResolverLocationAsRelative {
  params?: MatcherParamsFormatted;
  query?: LocationQueryRaw;
  hash?: string;
  /**
   * @deprecated This location is relative to the next parameter. This `name` will be ignored.
   */
  name?: undefined;
  /**
   * @deprecated This location is relative to the next parameter. This `path` will be ignored.
   */
  path?: undefined;
}
//#endregion
//#region src/experimental/route-resolver/matchers/param-parsers/define-param-parser.d.ts
/**
 * Defines a param parser that works with any kind of param (path, repeatable,
 * optional, query, hash, ...) but requires the user to handle all cases in the
 * get and set functions (nullish, undefined, arrays, etc). This allows you to
 * have full control over the parsing logic, but it also means that you need to
 * handle all edge cases yourself. If possible, prefer using {@see defineParamParser}
 * which provides a more structured way to handle these
 * cases and automatically handles arrays and nullish values.
 *
 * @example
 *
 * Here is an example that allows arbitrary numbers (NaN values are filtered
 * out). It supports repeatable params, so it can be used both as a path param
 * parser and a query param parser.
 *
 * ```ts
 * export const parser = defineParamParserRaw<number>({
 *   get: value => {
 *     if (value == null) return null
 *     if (Array.isArray(value)) {
 *       return value
 *         .filter(v => v != null)
 *         .map(Number)
 *         .filter(v => !Number.isNaN(v))
 *     }
 *
 *     return Number.isNaN(Number(value))
 *       ? miss(`"${value}" is not a valid number`)
 *       : Number(value)
 *   },
 *
 *   set: value =>
 *     Array.isArray(value)
 *       ? value.map(String)
 *       : value == null
 *         ? null
 *         : String(value),
 * })
 * ```
 *
 * @see {@link defineParamParser}
 */
/*! #__NO_SIDE_EFFECTS__ */
declare function defineParamParserRaw<TParam, TParamRaw = TParam, TUrlParam = MatcherQueryParamsValue>(parser: Required<ParamParser<TParam, TUrlParam, TParamRaw>>): Required<ParamParser<TParam, TUrlParam, TParamRaw>>;
/**
 * Defines a param parser that transforms strings to another type. Handles
 * optional and repeatable params:
 *
 * - Optional params: become `null` if the value is nullish
 * - Repeatable params: the value becomes an array of the parsed type, and nullish values are filtered out
 *
 * @example
 *
 * Here is an example that allows arbitrary valid numbers (NaN values are
 * filtered out). It can be used in both, path, and query params.
 *
 * ```ts
 * import { defineParamParser, miss } from 'vue-router/experimental'
 *
 * export const parser = defineParamParser<number>({
 *   get: value => {
 *     const num = Number(value)
 *     if (Number.isNaN(num)) {
 *       miss(`"${value}" is not a valid number`)
 *     }
 *     return num
 *   },
 *
 *   set: value => String(value),
 * })
 * ```
 *
 * @see {@link defineParamParserRaw}
 */
declare function defineParamParser<TParam, TParamRaw = TParam>(parser: Required<ParamParser<TParam, string, TParamRaw>>): Required<ParamParser<TParam | TParam[] | null, MatcherQueryParamsValue, TParamRaw | TParamRaw[] | null | undefined>>;
//#endregion
//#region src/experimental/route-resolver/matchers/param-parsers/booleans.d.ts
/**
 * Native Param parser for booleans.
 *
 * @internal
 */
declare const PARAM_PARSER_BOOL: {
  get: (value: NoInfer<MatcherQueryParamsValue>) => boolean | boolean[] | undefined;
  set: (value: boolean | boolean[] | null | undefined) => string | string[] | null | undefined;
};
//#endregion
//#region src/experimental/route-resolver/matchers/param-parsers/integers.d.ts
/**
 * Native Param parser for integers.
 *
 * @internal
 */
declare const PARAM_PARSER_INT: {
  get: (value: NoInfer<MatcherQueryParamsValue>) => number | number[] | null;
  set: (value: number | number[] | null) => string | string[] | null;
};
//#endregion
//#region src/experimental/route-resolver/matchers/param-parsers/standard-schema-types.d.ts
/**
 * Inlined subset of `@standard-schema/spec` v1.1.0 so the distributed code
 * has zero external type dependencies. Keep the package installed to track
 * upstream changes.
 *
 * @see https://github.com/standard-schema/standard-schema
 */
/** The Standard Schema interface. */
interface StandardSchemaV1<Input = unknown, Output = Input> {
  /** The Standard Schema properties. */
  readonly '~standard': StandardSchemaV1.Props<Input, Output>;
}
declare namespace StandardSchemaV1 {
  /** The Standard Schema properties interface. */
  interface Props<Input = unknown, Output = Input> {
    /** The version number of the standard. */
    readonly version: 1;
    /** The vendor name of the schema library. */
    readonly vendor: string;
    /** Inferred types associated with the schema. */
    readonly types?: Types<Input, Output> | undefined;
    /** Validates unknown input values. */
    readonly validate: (value: unknown) => Result<Output> | Promise<Result<Output>>;
  }
  /** The Standard Schema types interface. */
  interface Types<Input = unknown, Output = Input> {
    /** The input type of the schema. */
    readonly input: Input;
    /** The output type of the schema. */
    readonly output: Output;
  }
  /** The result interface of the validate function. */
  type Result<Output> = SuccessResult<Output> | FailureResult;
  /** The result interface if validation succeeds. */
  interface SuccessResult<Output> {
    /** The typed output value. */
    readonly value: Output;
    /** A falsy value for `issues` indicates success. */
    readonly issues?: undefined;
  }
  /** The result interface if validation fails. */
  interface FailureResult {
    /** The issues of failed validation. */
    readonly issues: ReadonlyArray<Issue>;
  }
  /** The issue interface of the failure output. */
  interface Issue {
    /** The error message of the issue. */
    readonly message: string;
    /** The path of the issue, if any. */
    readonly path?: ReadonlyArray<PropertyKey | PathSegment> | undefined;
  }
  /** The path segment interface of the issue. */
  interface PathSegment {
    /** The key representing a path segment. */
    readonly key: PropertyKey;
  }
  /** Infers the input type of a Standard Schema. */
  type InferInput<Schema extends StandardSchemaV1> = NonNullable<Schema['~standard']['types']>['input'];
  /** Infers the output type of a Standard Schema. */
  type InferOutput<Schema extends StandardSchemaV1> = NonNullable<Schema['~standard']['types']>['output'];
}
//#endregion
//#region src/experimental/route-resolver/matchers/param-parsers/standard-schema.d.ts
/**
 * Normalizes a param parser input, converting a StandardSchema-compliant object
 * into a {@link ParamParser} if needed.
 *
 * @param parser - a param parser or a StandardSchema-compliant validator
 *
 * @internal
 */
declare function normalizeParamParser<TParam = MatcherQueryParamsValue, TUrlParam = MatcherQueryParamsValue, TParamRaw = TParam>(parser: ParamParser<TParam, TUrlParam, TParamRaw> | StandardSchemaV1<unknown, TParam>): ParamParser<TParam, TUrlParam, TParamRaw>;
/**
 * Extracts the param type from Param Parsers or StandardSchema validators.
 *
 * @internal
 */
type ExtractParamParserType<PP> = PP extends ParamParser<infer T, any, any> ? T : PP extends StandardSchemaV1<unknown, infer T> ? T : unknown;
//#endregion
//#region src/experimental/route-resolver/matchers/matcher-pattern-query.d.ts
/**
 * Handles the `query` part of a URL. It can transform a query object into an
 * object of params and vice versa.
 */
interface MatcherPatternQuery<TParams extends MatcherParamsFormatted = MatcherParamsFormatted> extends MatcherPattern<MatcherQueryParams, TParams> {}
/**
 * Matcher for a specific query parameter. It will read and write the parameter
 */
declare class MatcherPatternQueryParam<T, ParamName extends string> implements MatcherPatternQuery<Record<ParamName, T>> {
  private paramName;
  private queryKey;
  private format;
  private parser;
  private defaultValue?;
  private required?;
  constructor(paramName: ParamName, queryKey: string, format: 'value' | 'array', parser?: ParamParser<T>, defaultValue?: ((() => T) | T) | undefined, required?: boolean | undefined);
  match(query: MatcherQueryParams): Record<ParamName, T>;
  build(params: Record<ParamName, T>): MatcherQueryParams;
}
//#endregion
//#region src/experimental/route-resolver/resolver-fixed.d.ts
/**
 * Base interface for a resolver record that can be extended.
 */
interface EXPERIMENTAL_ResolverRecord_Base {
  /**
   * Name of the matcher. Unique across all matchers. If missing, this record
   * cannot be matched. This is useful for grouping records.
   */
  name?: RecordName;
  /**
   * {@link MatcherPattern} for the path section of the URI.
   */
  path?: MatcherPatternPath;
  /**
   * {@link MatcherPattern} for the query section of the URI.
   */
  query?: MatcherPatternQuery[];
  /**
   * {@link MatcherPattern} for the hash section of the URI.
   */
  hash?: MatcherPatternHash;
  /**
   * Parent record. The parent can be a group or a matchable record.
   * It will be included in the `matched` array of a resolved location.
   */
  parent?: EXPERIMENTAL_ResolverRecord_Base | null;
  /**
   * If this record is an alias of another record, this points to the original
   * record. Alias records are not added to the name map and resolve to the
   * original record's name.
   */
  aliasOf?: EXPERIMENTAL_ResolverRecord_Base | null;
}
/**
 * A group can contain other useful properties like `meta` defined by the router.
 */
interface EXPERIMENTAL_ResolverRecord_Group extends EXPERIMENTAL_ResolverRecord_Base {
  /**
   * A group route cannot be matched directly and cannot be named.
   */
  name?: undefined;
  /**
   * A group route can **only** match the `query`.
   */
  path?: undefined;
  /**
   * A group route can **only** match the `query`.
   */
  hash?: undefined;
}
/**
 * A matchable record is a record that can be matched by a path, query or hash
 * and will resolve to a location.
 */
interface EXPERIMENTAL_ResolverRecord_Matchable extends EXPERIMENTAL_ResolverRecord_Base {
  name: RecordName;
  path: MatcherPatternPath;
}
/**
 * @alias EXPERIMENTAL_Resolver_Base
 */
interface EXPERIMENTAL_ResolverFixed<TRecord> extends EXPERIMENTAL_Resolver_Base<TRecord> {}
/**
 * Creates a fixed resolver that must have all records defined at creation
 * time.
 *
 * @template TRecord - extended type of the records
 * @param {TRecord[]} records - Ordered array of records that will be used to resolve routes
 * @returns a resolver that can be passed to the router
 */
declare function createFixedResolver<TRecord extends EXPERIMENTAL_ResolverRecord_Matchable>(records: TRecord[]): EXPERIMENTAL_ResolverFixed<TRecord>;
//#endregion
//#region src/experimental/router.d.ts
/**
 * Options to initialize a {@link Router} instance.
 */
interface EXPERIMENTAL_RouterOptions_Base extends PathParserOptions {
  /**
   * History implementation used by the router. Most web applications should use
   * `createWebHistory` but it requires the server to be properly configured.
   * You can also use a _hash_ based history with `createWebHashHistory` that
   * does not require any configuration on the server but isn't handled at all
   * by search engines and does poorly on SEO.
   *
   * @example
   * ```js
   * createRouter({
   *   history: createWebHistory(),
   *   // other options...
   * })
   * ```
   */
  history: RouterHistory;
  /**
   * Function to control scrolling when navigating between pages. Can return a
   * Promise to delay scrolling.
   *
   * @see {@link RouterScrollBehavior}.
   *
   * @example
   * ```js
   * function scrollBehavior(to, from, savedPosition) {
   *   // `to` and `from` are both route locations
   *   // `savedPosition` can be null if there isn't one
   * }
   * ```
   */
  scrollBehavior?: RouterScrollBehavior;
  /**
   * Custom implementation to parse a query. See its counterpart,
   * {@link EXPERIMENTAL_RouterOptions_Base.stringifyQuery}.
   *
   * @example
   * Let's say you want to use the [qs package](https://github.com/ljharb/qs)
   * to parse queries, you can provide both `parseQuery` and `stringifyQuery`:
   * ```js
   * import qs from 'qs'
   *
   * createRouter({
   *   // other options...
   *   parseQuery: qs.parse,
   *   stringifyQuery: qs.stringify,
   * })
   * ```
   */
  parseQuery?: typeof experimental_parseQuery;
  /**
   * Custom implementation to stringify a query object. Should not prepend a leading `?`.
   * {@link parseQuery} counterpart to handle query parsing.
   */
  stringifyQuery?: typeof stringifyQuery;
  /**
   * Default class applied to active {@link RouterLink}. If none is provided,
   * `router-link-active` will be applied.
   */
  linkActiveClass?: string;
  /**
   * Default class applied to exact active {@link RouterLink}. If none is provided,
   * `router-link-exact-active` will be applied.
   */
  linkExactActiveClass?: string;
}
/**
 * Internal type for common properties among all kind of {@link RouteRecordRaw}.
 */
interface EXPERIMENTAL_RouteRecord_Base extends EXPERIMENTAL_ResolverRecord_Base {
  /**
   * Where to redirect if the route is directly matched. The redirection happens
   * before any navigation guard and triggers a new navigation with the new
   * target location.
   */
  redirect?: RouteRecordRedirectOption;
  /**
   * Before Enter guard specific to this record. Note `beforeEnter` has no
   * effect if the record has a `redirect` property.
   */
  /**
   * Arbitrary data attached to the record.
   */
  meta?: RouteMeta;
  /**
   * Components to display when the URL matches this route. Allow using named views.
   */
  components?: Record<string, RawRouteComponent>;
  /**
   * Parent of this component if any
   */
  parent?: EXPERIMENTAL_RouteRecordNormalized | null;
  /**
   * References another record if this record is an alias of it.
   */
  aliasOf?: EXPERIMENTAL_RouteRecordNormalized | null;
}
interface EXPERIMENTAL_RouteRecord_Redirect extends Omit<EXPERIMENTAL_RouteRecord_Base, 'name' | 'path'>, Omit<EXPERIMENTAL_ResolverRecord_Matchable, 'parent' | 'aliasOf'> {
  components?: Record<string, RawRouteComponent>;
  redirect: RouteRecordRedirectOption;
}
interface EXPERIMENTAL_RouteRecord_Group extends Omit<EXPERIMENTAL_RouteRecord_Base, 'name' | 'path' | 'query' | 'hash'>, Omit<EXPERIMENTAL_ResolverRecord_Group, 'parent' | 'aliasOf'> {
  components?: Record<string, RawRouteComponent>;
}
interface EXPERIMENTAL_RouteRecord_Components extends Omit<EXPERIMENTAL_RouteRecord_Base, 'name' | 'path'>, Omit<EXPERIMENTAL_ResolverRecord_Matchable, 'parent' | 'aliasOf'> {
  components: Record<string, RawRouteComponent>;
  redirect?: never;
}
type EXPERIMENTAL_RouteRecord_Matchable = EXPERIMENTAL_RouteRecord_Components | EXPERIMENTAL_RouteRecord_Redirect;
type EXPERIMENTAL_RouteRecordRaw = EXPERIMENTAL_RouteRecord_Matchable | EXPERIMENTAL_RouteRecord_Group;
interface EXPERIMENTAL_RouteRecordNormalized_Base {
  /**
   * Contains the original modules for lazy loaded components.
   *
   * @internal
   */
  mods: Record<string, unknown>;
  props: Record<string, _RouteRecordProps>;
  /**
   * Registered leave guards
   *
   * @internal
   */
  leaveGuards: Set<NavigationGuard>;
  /**
   * Registered update guards
   *
   * @internal
   */
  updateGuards: Set<NavigationGuard>;
  instances: Record<string, unknown>;
}
interface EXPERIMENTAL_RouteRecordNormalized_Group extends EXPERIMENTAL_RouteRecordNormalized_Base, EXPERIMENTAL_RouteRecord_Group {
  meta: RouteMeta;
}
interface EXPERIMENTAL_RouteRecordNormalized_Redirect extends EXPERIMENTAL_RouteRecordNormalized_Base, EXPERIMENTAL_RouteRecord_Redirect {
  meta: RouteMeta;
}
interface EXPERIMENTAL_RouteRecordNormalized_Components extends EXPERIMENTAL_RouteRecordNormalized_Base, EXPERIMENTAL_RouteRecord_Components {
  meta: RouteMeta;
}
type EXPERIMENTAL_RouteRecordNormalized_Matchable = EXPERIMENTAL_RouteRecordNormalized_Components | EXPERIMENTAL_RouteRecordNormalized_Redirect;
type EXPERIMENTAL_RouteRecordNormalized = EXPERIMENTAL_RouteRecordNormalized_Matchable | EXPERIMENTAL_RouteRecordNormalized_Group;
declare function normalizeRouteRecord(record: EXPERIMENTAL_RouteRecord_Group): EXPERIMENTAL_RouteRecordNormalized_Group;
declare function normalizeRouteRecord(record: EXPERIMENTAL_RouteRecord_Matchable): EXPERIMENTAL_RouteRecordNormalized_Matchable;
/**
 * Options to initialize an experimental {@link EXPERIMENTAL_Router} instance.
 * @experimental
 */
interface EXPERIMENTAL_RouterOptions extends EXPERIMENTAL_RouterOptions_Base {
  /**
   * Matcher to use to resolve routes.
   *
   * @experimental
   */
  resolver: EXPERIMENTAL_ResolverFixed<EXPERIMENTAL_RouteRecordNormalized_Matchable>;
}
/**
 * Router base instance.
 *
 * @experimental This version is not stable, it's meant to replace {@link Router} in the future.
 */
interface EXPERIMENTAL_Router_Base<TRecord> extends DataLoaderExtensions {
  /**
   * Current {@link RouteLocationNormalized}
   */
  readonly currentRoute: ShallowRef<RouteLocationNormalizedLoaded>;
  /**
   * Allows turning off the listening of history events. This is a low level api for micro-frontend.
   */
  listening: boolean;
  /**
   * Checks if a route with a given name exists
   *
   * @param name - Name of the route to check
   */
  hasRoute(name: NonNullable<RouteRecordNameGeneric>): boolean;
  /**
   * Get a full list of all the {@link RouteRecord | route records}.
   */
  getRoutes(): TRecord[];
  /**
   * Returns the {@link RouteLocation | normalized version} of a
   * {@link RouteLocationRaw | route location}. Also includes an `href` property
   * that includes any existing `base`. By default, the `currentLocation` used is
   * `router.currentRoute` and should only be overridden in advanced use cases.
   *
   * @param to - Raw route location to resolve
   * @param currentLocation - Optional current location to resolve against
   */
  resolve<Name extends keyof RouteMap = keyof RouteMap>(to: RouteLocationAsRelativeTyped<RouteMap, Name>, currentLocation?: RouteLocationNormalizedLoaded): RouteLocationResolved<Name>;
  resolve(to: RouteLocationAsString | RouteLocationAsRelative | RouteLocationAsPath, currentLocation?: RouteLocationNormalizedLoaded): RouteLocationResolved;
  /**
   * Programmatically navigate to a new URL by pushing an entry in the history
   * stack.
   *
   * @param to - Route location to navigate to
   */
  push(to: RouteLocationRaw): Promise<NavigationFailure | void | undefined>;
  /**
   * Programmatically navigate to a new URL by replacing the current entry in
   * the history stack.
   *
   * @param to - Route location to navigate to
   */
  replace(to: RouteLocationRaw): Promise<NavigationFailure | void | undefined>;
  /**
   * Go back in history if possible by calling `history.back()`. Equivalent to
   * `router.go(-1)`.
   */
  back(): void;
  /**
   * Go forward in history if possible by calling `history.forward()`.
   * Equivalent to `router.go(1)`.
   */
  forward(): void;
  /**
   * Allows you to move forward or backward through the history. Calls
   * `history.go()`.
   *
   * @param delta - The position in the history to which you want to move,
   * relative to the current page
   */
  go(delta: number): void;
  /**
   * Add a navigation guard that executes before any navigation. Returns a
   * function that removes the registered guard.
   *
   * @param guard - navigation guard to add
   */
  beforeEach(guard: NavigationGuardWithThis<undefined>): () => void;
  /**
   * Add a navigation guard that executes before navigation is about to be
   * resolved. At this state all component have been fetched and other
   * navigation guards have been successful. Returns a function that removes the
   * registered guard.
   *
   * @param guard - navigation guard to add
   * @returns a function that removes the registered guard
   *
   * @example
   * ```js
   * router.beforeResolve(to => {
   *   if (to.meta.requiresAuth && !isAuthenticated) return false
   * })
   * ```
   *
   */
  beforeResolve(guard: _NavigationGuardResolved): () => void;
  /**
   * Add a navigation hook that is executed after every navigation. Returns a
   * function that removes the registered hook.
   *
   * @param guard - navigation hook to add
   * @returns a function that removes the registered hook
   *
   * @example
   * ```js
   * router.afterEach((to, from, failure) => {
   *   if (isNavigationFailure(failure)) {
   *     console.log('failed navigation', failure)
   *   }
   * })
   * ```
   */
  afterEach(guard: NavigationHookAfter): () => void;
  /**
   * Adds an error handler that is called every time a non caught error happens
   * during navigation. This includes errors thrown synchronously and
   * asynchronously, errors returned or passed to `next` in any navigation
   * guard, and errors occurred when trying to resolve an async component that
   * is required to render a route.
   *
   * @param handler - error handler to register
   */
  onError(handler: _ErrorListener): () => void;
  /**
   * Returns a Promise that resolves when the router has completed the initial
   * navigation, which means it has resolved all async enter hooks and async
   * components that are associated with the initial route. If the initial
   * navigation already happened, the promise resolves immediately.
   *
   * This is useful in server-side rendering to ensure consistent output on both
   * the server and the client. Note that on server side, you need to manually
   * push the initial location while on client side, the router automatically
   * picks it up from the URL.
   */
  isReady(): Promise<void>;
  /**
   * Called automatically by `app.use(router)`. Should not be called manually by
   * the user. This will trigger the initial navigation when on client side.
   *
   * @internal
   * @param app - Application that uses the router
   */
  install(app: App): void;
}
interface EXPERIMENTAL_Router extends EXPERIMENTAL_Router_Base<EXPERIMENTAL_RouteRecordNormalized_Matchable> {
  /**
   * Original options object passed to create the Router
   */
  readonly options: EXPERIMENTAL_RouterOptions;
  /**
   * Dev only method to replace the resolver used by the router. Used during HMR
   *
   * @param newResolver - new resolver to use
   *
   * @internal
   */
  _hmrReplaceResolver?: (newResolver: EXPERIMENTAL_ResolverFixed<EXPERIMENTAL_RouteRecordNormalized_Matchable>) => void;
}
/**
 * Creates an experimental Router that allows passing a resolver instead of a
 * routes array. This router does not have `addRoute()` and `removeRoute()`
 * methods and is meant to be used with file-based routing thanks to
 * vue-router/vite or vue-router/unplugin resolver generation in
 * `'vue-router/auto-resolver'`.
 *
 * @param options - Options to initialize the router
 */
declare function experimental_createRouter(options: EXPERIMENTAL_RouterOptions): EXPERIMENTAL_Router;
//#endregion
//#region src/experimental/route-resolver/matchers/errors.d.ts
/**
 * Error throw when a matcher matches by regex but validation fails.
 *
 * @internal
 */
declare class MatchMiss extends Error {
  name: string;
}
/**
 * Helper to throw a {@link MatchMiss} error.
 * @param args - Arguments to pass to the `MatchMiss` constructor.
 *
 * @example
 * ```ts
 * miss()
 * // in a number param matcher
 * miss('Number must be finite')
 * ```
 */
declare const miss: (...args: ConstructorParameters<typeof MatchMiss>) => never;
//#endregion
//#region src/experimental/runtime.d.ts
/**
 * Helper to define page properties with file-based routing.
 * **Doesn't do anything**, used for types only.
 *
 * The `FilePath` type parameter is injected by the `sfc-typed-router` Volar
 * plugin so that `params.path` keys are restricted to the file's actual path
 * params. When omitted, `params.path` falls back to a loose record.
 *
 * @param route - route information to be added to this page
 *
 * @internal
 */
declare function definePage<FilePath extends string = string>(route: DefinePage<FilePath>): DefinePage<FilePath>;
/**
 * Resolves the union of valid path-param names for a given file path. Falls
 * back to `string` when no entry is augmented (default Volar-less usage).
 *
 * Wired via the `_RouteFileInfoMap` slot in the user's augmented
 * {@link TypesConfig} so the lookup survives the bundler that otherwise
 * inlines an empty version of the base interface.
 *
 * @internal
 */
type PathParamNamesForFilePath<FilePath extends string> = TypesConfig extends {
  _RouteFileInfoMap: { [K in FilePath]: {
    pathParamNames: infer N extends string;
  }; };
} ? N : string;
/**
 * Merges route records.
 *
 * @internal
 *
 * @param main - main route record
 * @param routeRecords - route records to merge
 * @returns merged route record
 */
declare function _mergeRouteRecord(main: RouteRecordRaw, ...routeRecords: Partial<RouteRecordRaw>[]): RouteRecordRaw;
/**
 * Type to define a page. Can be augmented to add custom properties.
 *
 * @typeParam FilePath - File path of the SFC declaring this page, used to
 * narrow `params.path` keys to the actual path parameters of the route. When
 * left as the default `string`, keys are unrestricted.
 */
interface DefinePage<FilePath extends string = string> extends Partial<Omit<RouteRecordRaw, 'children' | 'components' | 'component' | 'name'>> {
  /**
   * A route name. If not provided, the name will be generated based on the file path.
   * Can be set to `false` to remove the name from types.
   */
  name?: string | false;
  /**
   * Custom parameters for the route. Requires `experimental.paramParsers` enabled.
   *
   * @experimental
   */
  params?: {
    /**
     * Parameters extracted from the path. Allows to setup custom parsers without changing the filename.
     */
    path?: { [K in PathParamNamesForFilePath<FilePath>]?: ParamParserType; };
    /**
     * Parameters extracted from the query.
     */
    query?: Record<string, DefinePageQueryParamOptionsAny | ParamParserType>;
  };
}
/**
 * Built-in param parsers. Always available; merged into {@link ParamParsers}
 * alongside any entries the user augments into {@link TypesConfig.ParamParsers}.
 *
 * @internal
 */
interface ParamParsers_Native {
  int: {
    type: number;
  };
  bool: {
    type: boolean;
  };
  string: {
    type: string;
  };
}
type ParamParserType_Native = keyof ParamParsers_Native;
/**
 * Full registry of param parsers: built-ins merged with whatever the user
 * augments into {@link TypesConfig.ParamParsers}. Each entry is shaped
 * `{ type: T }` so the parsed value type can be looked up by name via
 * {@link ParamParserTypeOf}. The vue-router codegen emits this augmentation
 * automatically from files in the `params/` folder.
 *
 * @internal
 */
type ParamParsers = ParamParsers_Native & (TypesConfig extends {
  _ParamParsers: infer P;
} ? P : {});
/**
 * Union of all known parser names (built-in + augmented).
 */
type ParamParserType = keyof ParamParsers;
/**
 * Resolves the parsed value type for a given parser name. Distributes over a
 * union of names, so `ParamParserTypeOf<'int' | 'date'>` → `number | Date`.
 *
 * Falls back to `unknown` when an entry is registered without a `type` field.
 * The `_ParamParsers` slot is internal — vue-router's codegen populates it
 * from files in `params/`.
 *
 * @example
 * ```ts
 * declare module 'vue-router' {
 *   interface TypesConfig {
 *     _ParamParsers: {
 *       date: { type: Date }
 *     }
 *   }
 * }
 * ```
 */
type ParamParserTypeOf<Name extends ParamParserType> = Name extends keyof ParamParsers ? ParamParsers[Name] extends {
  type: infer T;
} ? T : unknown : unknown;
/**
 * Distributive variant of {@link DefinePageQueryParamOptions} used in the
 * `params.query` record. Distributing over `ParamParserType` produces one
 * variant per literal parser name so the `parser` field acts as a discriminant
 * and `default` is narrowed to that parser's resolved value type. Without
 * this, the bare interface defaults `Parser` to the full `ParamParserType`
 * union and `default` collapses to the union of every parser's value type.
 *
 * @internal
 */
type DefinePageQueryParamOptionsAny<P extends ParamParserType = ParamParserType> = P extends ParamParserType ? DefinePageQueryParamOptions<P> : never;
/**
 * Configures how to extract a route param from a specific query parameter.
 *
 * @typeParam Parser - name of the param parser used for this query parameter,
 * used to type the {@link DefinePageQueryParamOptions.default | `default`}
 * value via {@link ParamParserTypeOf}.
 */
interface DefinePageQueryParamOptions<Parser extends ParamParserType = ParamParserType> {
  /**
   * The type of the query parameter. Allowed values are native param parsers
   * and any parser in the {@link https://uvr.esm.is/TODO | params folder }. If
   * not provided, the value will kept as is.
   */
  parser?: Parser;
  /**
   * Default value if the query parameter is missing or if the match fails
   * (e.g. a invalid number is passed to the int param parser). If not provided
   * and the param is not required, the route will match with undefined.
   */
  default?: (() => ParamParserTypeOf<Parser>) | ParamParserTypeOf<Parser>;
  /**
   * How to format the query parameter value.
   *
   * - 'value' - keep the first value only and pass that to parser
   * - 'array' - keep all values (even one or none) as an array and pass that to parser
   *
   * @default 'value'
   */
  format?: 'value' | 'array';
  /**
   * Whether this query parameter is required. If true and the parameter is
   * missing (and no default is provided), the route will not match.
   *
   * @default false
   */
  required?: boolean;
}
/**
 * TODO: native parsers ideas:
 * - json -> just JSON.parse(value)
 * - boolean -> 'true' | 'false' -> boolean
 * - number -> Number(value) -> NaN if not a number
 */
//#endregion
//#region src/experimental/data-loaders/types-config.d.ts
/**
 * The default error type used for data loaders. Can be customized via {@link TypesConfig}.
 *
 * @example
 * ```ts
 * // types-extension.d.ts
 * import 'vue-router/experimental'
 * export {}
 * declare module 'vue-router/experimental' {
 *   interface TypesConfig {
 *     Error: MyCustomError
 *   }
 * }
 * ```
 *
 * @internal
 */
type ErrorDefault = TypesConfig extends Record<'Error', infer E> ? E : Error;
//#endregion
//#region src/experimental/data-loaders/defineLoader.d.ts
/**
 * Creates a data loader composable that can be exported by pages to attach the data loading to a route. In this version `data` is always defined.
 *
 * @param name - name of the route
 * @param loader - function that returns a promise with the data
 * @param options - options to configure the data loader
 */
declare function defineBasicLoader<Name extends keyof RouteMap, Data>(name: Name, loader: DefineLoaderFn<Data, DataLoaderContext, RouteLocationNormalizedLoaded<Name>>, options?: DefineDataLoaderOptions_DefinedData): UseDataLoaderBasic_DefinedData<Data>;
/**
 * Creates a data loader composable that can be exported by pages to attach the data loading to a route. In this version, `data` can be `undefined`.
 *
 * @param name - name of the route
 * @param loader - function that returns a promise with the data
 * @param options - options to configure the data loader
 */
declare function defineBasicLoader<Name extends keyof RouteMap, Data>(name: Name, loader: DefineLoaderFn<Data, DataLoaderContext, RouteLocationNormalizedLoaded<Name>>, options: DefineDataLoaderOptions_LaxData): UseDataLoaderBasic_LaxData<Data>;
/**
 * Creates a data loader composable that can be exported by pages to attach the data loading to a route. In this version `data` is always defined.
 *
 * @param loader - function that returns a promise with the data
 * @param options - options to configure the data loader
 */
declare function defineBasicLoader<Data>(loader: DefineLoaderFn<Data, DataLoaderContext, RouteLocationNormalizedLoaded>, options?: DefineDataLoaderOptions_DefinedData): UseDataLoaderBasic_DefinedData<Data>;
/**
 * Creates a data loader composable that can be exported by pages to attach the data loading to a route. In this version, `data` can be `undefined`.
 *
 * @param loader - function that returns a promise with the data
 * @param options - options to configure the data loader
 */
declare function defineBasicLoader<Data>(loader: DefineLoaderFn<Data, DataLoaderContext, RouteLocationNormalizedLoaded>, options: DefineDataLoaderOptions_LaxData): UseDataLoaderBasic_LaxData<Data>;
interface DefineDataLoaderOptions_LaxData extends DefineDataLoaderOptionsBase_LaxData {
  /**
   * Key to use for SSR state. This will be used to read the initial data from `initialData`'s object.
   */
  key?: string;
}
interface DefineDataLoaderOptions_DefinedData extends DefineDataLoaderOptionsBase_DefinedData {
  key?: string;
}
/**
 * @deprecated use {@link DefineDataLoaderOptions_LaxData} instead
 */
type DefineDataLoaderOptions = DefineDataLoaderOptions_LaxData;
interface DataLoaderContext extends DataLoaderContextBase {}
/**
 * Symbol used to store the data in the router so it can be retrieved after the initial navigation.
 * @internal
 */
declare const SERVER_INITIAL_DATA_KEY: unique symbol;
/**
 * Initial data generated on server and consumed on client.
 * @internal
 */
declare const INITIAL_DATA_KEY: unique symbol;
interface UseDataLoaderBasic_LaxData<Data> extends UseDataLoader<Data | undefined, ErrorDefault> {}
/**
 * @deprecated use {@link UseDataLoaderBasic_LaxData} instead
 */
type UseDataLoaderBasic<Data> = UseDataLoaderBasic_LaxData<Data>;
interface UseDataLoaderBasic_DefinedData<Data> extends UseDataLoader<Data, ErrorDefault> {}
interface DataLoaderBasicEntry<TData, TError = unknown, TDataInitial extends TData | undefined = TData | undefined> extends DataLoaderEntryBase<TData, TError, TDataInitial> {}
//#endregion
//#region src/experimental/index.d.ts
/**
 * @deprecated Use {@link reroute} instead.
 */
declare class NavigationResult$1 extends NavigationResult {
  constructor(...args: ConstructorParameters<typeof NavigationResult>);
}
//#endregion
//#region src/types/index.d.ts
type Lazy<T> = () => Promise<T>;
/**
 * @internal
 */
type RouteParamValue = string;
/**
 * @internal
 */
type RouteParamValueRaw = RouteParamValue | number | null | undefined;
type RouteParamsGeneric = Record<string, RouteParamValue | RouteParamValue[]>;
type RouteParamsRawGeneric = Record<string, RouteParamValueRaw | Exclude<RouteParamValueRaw, null | undefined>[]>;
/**
 * @internal
 */
interface RouteQueryAndHash {
  query?: LocationQueryRaw$1;
  hash?: string;
}
/**
 * @internal
 */
interface MatcherLocationAsPath {
  path: string;
}
/**
 * @internal
 */
interface MatcherLocationAsName {
  name: RouteRecordNameGeneric;
  /**
   * Ignored path property since we are dealing with a relative location. Only `undefined` is allowed.
   */
  path?: undefined;
  params?: RouteParamsGeneric;
}
/**
 * @internal
 */
interface MatcherLocationAsRelative {
  /**
   * Ignored path property since we are dealing with a relative location. Only `undefined` is allowed.
   */
  path?: undefined;
  params?: RouteParamsGeneric;
}
/**
 * @internal
 */
interface LocationAsRelativeRaw {
  name?: RouteRecordNameGeneric;
  /**
   * Ignored path property since we are dealing with a relative location. Only `undefined` is allowed.
   */
  path?: undefined;
  params?: RouteParamsRawGeneric;
}
/**
 * Common options for all navigation methods.
 */
interface RouteLocationOptions {
  /**
   * Replace the entry in the history instead of pushing a new entry
   */
  replace?: boolean;
  /**
   * Triggers the navigation even if the location is the same as the current one.
   * Note this will also add a new entry to the history unless `replace: true`
   * is passed.
   */
  force?: boolean;
  /**
   * State to save using the History API. This cannot contain any reactive
   * values and some primitives like Symbols are forbidden. More info at
   * https://developer.mozilla.org/en-US/docs/Web/API/History/state
   */
  state?: HistoryState;
}
/**
 * Route Location that can infer the necessary params based on the name.
 *
 * @internal
 */
interface RouteLocationNamedRaw extends RouteQueryAndHash, LocationAsRelativeRaw, RouteLocationOptions {}
/**
 * Route Location that can infer the possible paths.
 *
 * @internal
 */
interface RouteLocationPathRaw extends RouteQueryAndHash, MatcherLocationAsPath, RouteLocationOptions {}
interface RouteLocationMatched extends RouteRecordNormalized {
  components: Record<string, RouteComponent> | null | undefined;
}
/**
 * Base properties for a normalized route location.
 *
 * @internal
 */
interface _RouteLocationBase extends Pick<MatcherLocation, 'name' | 'path' | 'params' | 'meta'> {
  /**
   * The whole location including the `search` and `hash`. This string is
   * percentage encoded.
   */
  fullPath: string;
  /**
   * Object representation of the `search` property of the current location.
   */
  query: LocationQuery$1;
  /**
   * Hash of the current location. If present, starts with a `#`.
   */
  hash: string;
  /**
   * Contains the location we were initially trying to access before ending up
   * on the current location.
   */
  redirectedFrom: RouteLocation | undefined;
}
/**
 * Allowed Component in {@link RouteLocationMatched}
 */
type RouteComponent = Component$1 | DefineComponent;
/**
 * Allowed Component definitions in route records provided by the user
 */
type RawRouteComponent = RouteComponent | Lazy<RouteComponent>;
/**
 * Internal type for common properties among all kind of {@link RouteRecordRaw}.
 */
interface _RouteRecordBase extends PathParserOptions {
  /**
   * Path of the record. Should start with `/` unless the record is the child of
   * another record.
   *
   * @example `/users/:id` matches `/users/1` as well as `/users/posva`.
   */
  path: string;
  /**
   * Where to redirect if the route is directly matched. The redirection happens
   * before any navigation guard and triggers a new navigation with the new
   * target location.
   */
  redirect?: RouteRecordRedirectOption;
  /**
   * Aliases for the record. Allows defining extra paths that will behave like a
   * copy of the record. Allows having paths shorthands like `/users/:id` and
   * `/u/:id`. All `alias` and `path` values must share the same params.
   */
  alias?: string | string[];
  /**
   * Name for the route record. Must be unique.
   */
  name?: RouteRecordNameGeneric;
  /**
   * Before Enter guard specific to this record. Note `beforeEnter` has no
   * effect if the record has a `redirect` property.
   */
  beforeEnter?: NavigationGuardWithThis<undefined> | NavigationGuardWithThis<undefined>[];
  /**
   * Arbitrary data attached to the record.
   */
  meta?: RouteMeta;
  /**
   * Array of nested routes.
   */
  children?: RouteRecordRaw[];
  /**
   * Allow passing down params as props to the component rendered by `router-view`.
   */
  props?: _RouteRecordProps | Record<string, _RouteRecordProps>;
}
/**
 * Interface to type `meta` fields in route records.
 *
 * @example
 *
 * ```ts
 * // typings.d.ts or router.ts
 * import 'vue-router';
 *
 * declare module 'vue-router' {
 *   interface RouteMeta {
 *     requiresAuth?: boolean
 *   }
 * }
 * ```
 */
interface RouteMeta extends Record<PropertyKey, unknown> {
  /**
   * The data loaders for a route record. Add any data loader to this array to
   * have it called when the route is navigated to. Note this is only needed
   * when **not** using lazy components (`() => import('./pages/Home.vue')`) or
   * when not explicitly exporting data loaders from page components.
   *
   * This requires the use of the experimental data loaders and is ignored otherwise.
   *
   * @see {@link DataLoaderPlugin}
   */
  loaders?: UseDataLoader[];
  /**
   * Set of loaders for the current route. This is built once during navigation
   * and is used to merge the loaders from the lazy import in components or the
   * `loaders` array in the route record.
   *
   * @internal
   */
  [LOADER_SET_KEY]?: Set<UseDataLoader>;
  /**
   * The signal that is aborted when the navigation is canceled or an error
   * occurs.
   *
   * @internal
   */
  [ABORT_CONTROLLER_KEY]?: AbortController;
}
/**
 * Route Record defining one single component with the `component` option.
 */
interface RouteRecordSingleView extends _RouteRecordBase {
  /**
   * Component to display when the URL matches this route.
   */
  component: RawRouteComponent;
  components?: never;
  children?: never;
  redirect?: never;
  /**
   * Allow passing down params as props to the component rendered by `router-view`.
   */
  props?: _RouteRecordProps;
}
/**
 * Route Record defining one single component with a nested view. Differently
 * from {@link RouteRecordSingleView}, this record has children and allows a
 * `redirect` option.
 */
interface RouteRecordSingleViewWithChildren extends _RouteRecordBase {
  /**
   * Component to display when the URL matches this route.
   */
  component?: RawRouteComponent | null | undefined;
  components?: never;
  children: RouteRecordRaw[];
  /**
   * Allow passing down params as props to the component rendered by `router-view`.
   */
  props?: _RouteRecordProps;
}
/**
 * Route Record defining multiple named components with the `components` option.
 */
interface RouteRecordMultipleViews extends _RouteRecordBase {
  /**
   * Components to display when the URL matches this route. Allow using named views.
   */
  components: Record<string, RawRouteComponent>;
  component?: never;
  children?: never;
  redirect?: never;
  /**
   * Allow passing down params as props to the component rendered by
   * `router-view`. Should be an object with the same keys as `components` or a
   * boolean to be applied to every component.
   */
  props?: Record<string, _RouteRecordProps> | boolean;
}
/**
 * Route Record defining multiple named components with the `components` option and children.
 */
interface RouteRecordMultipleViewsWithChildren extends _RouteRecordBase {
  /**
   * Components to display when the URL matches this route. Allow using named views.
   */
  components?: Record<string, RawRouteComponent> | null | undefined;
  component?: never;
  children: RouteRecordRaw[];
  /**
   * Allow passing down params as props to the component rendered by
   * `router-view`. Should be an object with the same keys as `components` or a
   * boolean to be applied to every component.
   */
  props?: Record<string, _RouteRecordProps> | boolean;
}
/**
 * Route Record that defines a redirect. Cannot have `component` or `components`
 * as it is never rendered.
 */
interface RouteRecordRedirect extends _RouteRecordBase {
  redirect: RouteRecordRedirectOption;
  component?: never;
  components?: never;
  props?: never;
}
type RouteRecordRaw = RouteRecordSingleView | RouteRecordSingleViewWithChildren | RouteRecordMultipleViews | RouteRecordMultipleViewsWithChildren | RouteRecordRedirect;
/**
 * Route location that can be passed to the matcher.
 */
type MatcherLocationRaw = MatcherLocationAsPath | MatcherLocationAsName | MatcherLocationAsRelative;
/**
 * Normalized/resolved Route location that returned by the matcher.
 */
interface MatcherLocation {
  /**
   * Name of the matched record
   */
  name: RouteRecordNameGeneric | null | undefined;
  /**
   * Percentage encoded pathname section of the URL.
   */
  path: string;
  /**
   * Object of decoded params extracted from the `path`.
   */
  params: RouteParamsGeneric;
  /**
   * Merged `meta` properties from all the matched route records.
   */
  meta: RouteMeta;
  /**
   * Array of {@link RouteRecord} containing components as they were
   * passed when adding records. It can also contain redirect records. This
   * can't be used directly
   */
  matched: RouteRecord[];
}
//#endregion
//#region src/typed-routes/route-map.d.ts
/**
 * Helper type to define a Typed `RouteRecord`
 * @see {@link RouteRecord}
 */
interface RouteRecordInfo<Name extends string | symbol = string, Path extends string = string, ParamsRaw extends RouteParamsRawGeneric = RouteParamsRawGeneric, Params extends RouteParamsGeneric = RouteParamsGeneric, ChildrenNames extends string | symbol = never> {
  name: Name;
  path: Path;
  paramsRaw: ParamsRaw;
  params: Params;
  childrenNames: ChildrenNames;
}
type RouteRecordInfoGeneric = RouteRecordInfo<string | symbol, string, RouteParamsRawGeneric, RouteParamsGeneric, string | symbol>;
/**
 * Convenience type to get the typed RouteMap or a generic one if not provided.
 * It is extracted from the {@link TypesConfig} if it exists, it becomes
 * {@link RouteMapGeneric} otherwise.
 */
type RouteMap = TypesConfig extends Record<'RouteNamedMap', infer RouteNamedMap> ? RouteNamedMap : RouteMapGeneric;
/**
 * Generic version of the `RouteMap`.
 */
type RouteMapGeneric = Record<string | symbol, RouteRecordInfoGeneric>;
//#endregion
//#region src/typed-routes/params.d.ts
/**
 * Utility type for raw and non raw params like :id+
 *
 */
type ParamValueOneOrMore<isRaw extends boolean> = [ParamValue<isRaw>, ...ParamValue<isRaw>[]];
/**
 * Utility type for raw and non raw params like :id*
 *
 */
type ParamValueZeroOrMore<isRaw extends boolean> = true extends isRaw ? ParamValue<isRaw>[] | undefined | null : ParamValue<isRaw>[] | undefined;
/**
 * Utility type for raw and non raw params like :id?
 *
 */
type ParamValueZeroOrOne<isRaw extends boolean> = true extends isRaw ? string | number | null | undefined : string;
/**
 * Utility type for raw and non raw params like :id
 *
 */
type ParamValue<isRaw extends boolean> = true extends isRaw ? string | number : string;
/**
 * Generate a type safe params for a route location. Requires the name of the route to be passed as a generic.
 * @see {@link RouteParamsGeneric}
 */
type RouteParams<Name extends keyof RouteMap = keyof RouteMap> = RouteMap[Name]['params'];
/**
 * Generate a type safe raw params for a route location. Requires the name of the route to be passed as a generic.
 * @see {@link RouteParamsRaw}
 */
type RouteParamsRaw<Name extends keyof RouteMap = keyof RouteMap> = RouteMap[Name]['paramsRaw'];
//#endregion
//#region src/typed-routes/route-records.d.ts
/**
 * @internal
 */
type RouteRecordRedirectOption = RouteLocationRaw | ((to: RouteLocation, from: RouteLocationNormalizedLoaded) => RouteLocationRaw);
/**
 * Generic version of {@link RouteRecordName}.
 */
type RouteRecordNameGeneric = string | symbol | undefined;
/**
 * Possible values for a route record **after normalization**
 *
 * NOTE: since `RouteRecordName` is a type, it evaluates too early and it's often the generic version {@link RouteRecordNameGeneric}. If you need a typed version of all of the names of routes, use {@link RouteMap | `keyof RouteMap`}
 */
type RouteRecordName = RouteMapGeneric extends RouteMap ? RouteRecordNameGeneric : keyof RouteMap;
/**
 * @internal
 */
type _RouteRecordProps<Name extends keyof RouteMap = keyof RouteMap> = boolean | Record<string, any> | ((to: RouteLocationNormalized<Name>) => Record<string, any>);
//#endregion
//#region src/typed-routes/route-location.d.ts
/**
 * Generic version of {@link RouteLocation}. It is used when no {@link RouteMap} is provided.
 */
interface RouteLocationGeneric extends _RouteLocationBase, RouteLocationOptions {
  /**
   * Array of {@link RouteRecord} containing components as they were
   * passed when adding records. It can also contain redirect records. This
   * can't be used directly. **This property is non-enumerable**.
   */
  matched: RouteRecord[];
}
/**
 * Helper to generate a type safe version of the {@link RouteLocation} type.
 */
interface RouteLocationTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; }, Name extends keyof RouteMap> extends RouteLocationGeneric {
  name: Extract<Name, string | symbol>;
  params: RouteMap[Name]['params'];
}
/**
 * List of all possible {@link RouteLocation} indexed by the route name.
 * @internal
 */
type RouteLocationTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationTyped<RouteMap, N>; };
/**
 * Generic version of {@link RouteLocationNormalized} that is used when no {@link RouteMap} is provided.
 */
interface RouteLocationNormalizedGeneric extends _RouteLocationBase {
  name: RouteRecordNameGeneric;
  /**
   * Array of {@link RouteRecordNormalized}
   */
  matched: RouteRecordNormalized[];
}
/**
 * Helper to generate a type safe version of the {@link RouteLocationNormalized} type.
 */
interface RouteLocationNormalizedTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric, Name extends keyof RouteMap = keyof RouteMap> extends RouteLocationNormalizedGeneric {
  name: Extract<Name, string | symbol>;
  params: RouteMap[Name]['params'];
  /**
   * Array of {@link RouteRecordNormalized}
   */
  matched: RouteRecordNormalized[];
}
/**
 * List of all possible {@link RouteLocationNormalized} indexed by the route name.
 * @internal
 */
type RouteLocationNormalizedTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationNormalizedTyped<RouteMap, N>; };
/**
 * Generic version of {@link RouteLocationNormalizedLoaded} that is used when no {@link RouteMap} is provided.
 */
interface RouteLocationNormalizedLoadedGeneric extends RouteLocationNormalizedGeneric {
  /**
   * Array of {@link RouteLocationMatched} containing only plain components (any
   * lazy-loaded components have been loaded and were replaced inside the
   * `components` object) so it can be directly used to display routes. It
   * cannot contain redirect records either. **This property is non-enumerable**.
   */
  matched: RouteLocationMatched[];
}
/**
 * Helper to generate a type safe version of the {@link RouteLocationNormalizedLoaded} type.
 */
interface RouteLocationNormalizedLoadedTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric, Name extends keyof RouteMap = keyof RouteMap> extends RouteLocationNormalizedLoadedGeneric {
  name: Extract<Name, string | symbol>;
  params: RouteMap[Name]['params'];
}
/**
 * List of all possible {@link RouteLocationNormalizedLoaded} indexed by the route name.
 * @internal
 */
type RouteLocationNormalizedLoadedTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationNormalizedLoadedTyped<RouteMap, N>; };
/**
 * Generic version of {@link RouteLocationAsRelative}. It is used when no {@link RouteMap} is provided.
 */
interface RouteLocationAsRelativeGeneric extends RouteQueryAndHash, RouteLocationOptions {
  name?: RouteRecordNameGeneric;
  params?: RouteParamsRawGeneric;
  /**
   * A relative path to the current location. This property should be removed
   */
  path?: undefined;
}
/**
 * Helper to generate a type safe version of the {@link RouteLocationAsRelative} type.
 */
interface RouteLocationAsRelativeTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric, Name extends keyof RouteMap = keyof RouteMap> extends RouteLocationAsRelativeGeneric {
  name?: Extract<Name, string | symbol>;
  params?: RouteMap[Name]['paramsRaw'];
}
/**
 * List of all possible {@link RouteLocationAsRelative} indexed by the route name.
 * @internal
 */
type RouteLocationAsRelativeTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationAsRelativeTyped<RouteMap, N>; };
/**
 * Generic version of {@link RouteLocationAsPath}. It is used when no {@link RouteMap} is provided.
 */
interface RouteLocationAsPathGeneric extends RouteQueryAndHash, RouteLocationOptions {
  /**
   * Percentage encoded pathname section of the URL.
   */
  path: string;
}
/**
 * Helper to generate a type safe version of the {@link RouteLocationAsPath} type.
 */
interface RouteLocationAsPathTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric, Name extends keyof RouteMap = keyof RouteMap> extends RouteLocationAsPathGeneric {
  path: _LiteralUnion<RouteMap[Name]['path']>;
}
/**
 * List of all possible {@link RouteLocationAsPath} indexed by the route name.
 * @internal
 */
type RouteLocationAsPathTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationAsPathTyped<RouteMap, N>; };
/**
 * Helper to generate a type safe version of the {@link RouteLocationAsString} type.
 */
type RouteLocationAsStringTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric, Name extends keyof RouteMap = keyof RouteMap> = RouteMap[Name]['path'];
/**
 * List of all possible {@link RouteLocationAsString} indexed by the route name.
 * @internal
 */
type RouteLocationAsStringTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationAsStringTyped<RouteMap, N>; };
/**
 * Generic version of {@link RouteLocationResolved}. It is used when no {@link RouteMap} is provided.
 */
interface RouteLocationResolvedGeneric extends RouteLocationGeneric {
  /**
   * Resolved `href` for the route location that will be set on the `<a href="...">`.
   */
  href: string;
}
/**
 * Helper to generate a type safe version of the {@link RouteLocationResolved} type.
 */
interface RouteLocationResolvedTyped<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; }, Name extends keyof RouteMap> extends RouteLocationTyped<RouteMap, Name> {
  /**
   * Resolved `href` for the route location that will be set on the `<a href="...">`.
   */
  href: string;
}
/**
 * List of all possible {@link RouteLocationResolved} indexed by the route name.
 * @internal
 */
type RouteLocationResolvedTypedList<RouteMap extends { [K in keyof RouteMap]: RouteRecordInfoGeneric; } = RouteMapGeneric> = { [N in keyof RouteMap]: RouteLocationResolvedTyped<RouteMap, N>; };
/**
 * Type safe versions of types that are exposed by vue-router. We have to use a generic check to allow for names to be `undefined` when no `RouteMap` is provided.
 */
/**
 * {@link RouteLocationRaw} resolved using the matcher
 */
type RouteLocation<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationGeneric : RouteLocationTypedList<RouteMap>[Name];
/**
 * Similar to {@link RouteLocation} but its
 * {@link RouteLocationNormalizedTyped.matched | `matched` property} cannot contain redirect records
 */
type RouteLocationNormalized<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationNormalizedGeneric : RouteLocationNormalizedTypedList<RouteMap>[Name];
/**
 * Similar to {@link RouteLocationNormalized} but its `components` do not contain any function to lazy load components.
 * In other words, it's ready to be rendered by `<RouterView>`.
 */
type RouteLocationNormalizedLoaded<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationNormalizedLoadedGeneric : RouteLocationNormalizedLoadedTypedList<RouteMap>[Name];
/**
 * Route location relative to the current location. It accepts other properties than `path` like `params`, `query` and
 * `hash` to conveniently change them.
 */
type RouteLocationAsRelative<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationAsRelativeGeneric : RouteLocationAsRelativeTypedList<RouteMap>[Name];
/**
 * Route location resolved with {@link Router | `router.resolve()`}.
 */
type RouteLocationResolved<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationResolvedGeneric : RouteLocationResolvedTypedList<RouteMap>[Name];
/**
 * Same as {@link RouteLocationAsPath} but as a string literal.
 */
type RouteLocationAsString<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? string : _LiteralUnion<RouteLocationAsStringTypedList<RouteMap>[Name], string>;
/**
 * Route location as an object with a `path` property.
 */
type RouteLocationAsPath<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationAsPathGeneric : RouteLocationAsPathTypedList<RouteMap>[Name];
/**
 * Route location that can be passed to `router.push()` and other user-facing APIs.
 */
type RouteLocationRaw<Name extends keyof RouteMap = keyof RouteMap> = RouteMapGeneric extends RouteMap ? RouteLocationAsString | RouteLocationAsRelativeGeneric | RouteLocationAsPathGeneric : _LiteralUnion<RouteLocationAsStringTypedList<RouteMap>[Name], string> | RouteLocationAsRelativeTypedList<RouteMap>[Name] | RouteLocationAsPathTypedList<RouteMap>[Name];
//#endregion
//#region src/typed-routes/navigation-guards.d.ts
/**
 * Return types for a Navigation Guard. Based on `TypesConfig`
 *
 * @see {@link TypesConfig}
 */
type NavigationGuardReturn = void | Error | boolean | RouteLocationRaw;
/**
 * Navigation Guard with a type parameter for `this`.
 * @see {@link TypesConfig}
 */
interface NavigationGuardWithThis<T> {
  (this: T, to: RouteLocationNormalized, from: RouteLocationNormalizedLoaded,
  /**
   * @deprecated Return a value from the guard instead of calling `next(value)`.
   * The callback will be removed in a future version of Vue Router.
   */
  next: NavigationGuardNext): _Awaitable<NavigationGuardReturn>;
}
/**
 * In `router.beforeResolve((to) => {})`, the `to` is typed as `RouteLocationNormalizedLoaded`, not
 * `RouteLocationNormalized` like in `router.beforeEach()`. In practice it doesn't change much as users do not rely on
 * the difference between them but if we update the type in vue-router, we will have to update this type too.
 * @internal
 */
interface _NavigationGuardResolved {
  (this: undefined, to: RouteLocationNormalizedLoaded, from: RouteLocationNormalizedLoaded,
  /**
   * @deprecated Return a value from the guard instead of calling `next(value)`.
   * The callback will be removed in a future version of Vue Router.
   */
  next: NavigationGuardNext): _Awaitable<NavigationGuardReturn>;
}
/**
 * Navigation Guard.
 */
interface NavigationGuard {
  (to: RouteLocationNormalized, from: RouteLocationNormalizedLoaded,
  /**
   * @deprecated Return a value from the guard instead of calling `next(value)`.
   * The callback will be removed in a future version of Vue Router.
   */
  next: NavigationGuardNext): _Awaitable<NavigationGuardReturn>;
}
/**
 * Navigation hook triggered after a navigation is settled.
 */
interface NavigationHookAfter {
  (to: RouteLocationNormalizedLoaded, from: RouteLocationNormalizedLoaded, failure?: NavigationFailure | void): unknown;
}
/**
 * Callback passed to navigation guards to continue or abort the navigation.
 *
 * @deprecated Prefer returning a value from the guard instead of calling
 * `next(value)`. The callback will be removed in a future version of Vue Router.
 */
interface NavigationGuardNext {
  (): void;
  (error: Error): void;
  (location: RouteLocationRaw): void;
  (valid: boolean | undefined): void;
  (cb: NavigationGuardNextCallback): void;
}
/**
 * Callback that can be passed to `next()` in `beforeRouteEnter()` guards.
 */
type NavigationGuardNextCallback = (vm: ComponentPublicInstance) => unknown;
//#endregion
//#region src/experimental/data-loaders/meta-extensions.d.ts
/**
 * Map type for the entries used by data loaders.
 * @internal
 */
type _DefineLoaderEntryMap<DataLoaderEntry extends DataLoaderEntryBase<unknown> = DataLoaderEntryBase<unknown>> = WeakMap<object, DataLoaderEntry>;
/**
 * The extensions added to the router instance for data loaders. These are used
 * internally by the router and should not be accessed directly by users.
 *
 * @internal
 */
interface DataLoaderExtensions {
  /**
   * The entries used by data loaders. Put on the router for convenience.
   * @internal
   */
  [LOADER_ENTRIES_KEY]: _DefineLoaderEntryMap;
  /**
   * Pending navigation that is waiting for data loaders to resolve.
   * @internal
   */
  [PENDING_LOCATION_KEY]: RouteLocationNormalizedLoaded | null;
  /**
   * The app instance that is used by the router.
   * @internal
   */
  [APP_KEY]: App<unknown>;
  /**
   * Whether the router is running in server-side rendering mode.
   * @internal
   */
  [IS_SSR_KEY]: boolean;
  /**
   * The effect scope used to run data loaders.
   * @internal
   */
  [DATA_LOADERS_EFFECT_SCOPE_KEY]: EffectScope;
  /**
   * Gives access to the initial state during rendering. Should be set to `false` once it's consumed.
   * Used by `defineLoader`
   *
   * @internal
   */
  [SERVER_INITIAL_DATA_KEY]?: Record<string, unknown> | false;
  /**
   * Used by `defineLoader`
   *
   * @internal
   */
  [INITIAL_DATA_KEY]?: Record<string, unknown> | false;
}
//#endregion
//#region src/history/hash.d.ts
/**
 * Creates a hash history. Useful for web applications with no host (e.g. `file://`) or when configuring a server to
 * handle any URL is not possible.
 *
 * @param base - optional base to provide. Defaults to `location.pathname + location.search` If there is a `<base>` tag
 * in the `head`, its value will be ignored in favor of this parameter **but note it affects all the history.pushState()
 * calls**, meaning that if you use a `<base>` tag, it's `href` value **has to match this parameter** (ignoring anything
 * after the `#`).
 *
 * @example
 * ```js
 * // at https://example.com/folder
 * createWebHashHistory() // gives a url of `https://example.com/folder#`
 * createWebHashHistory('/folder/') // gives a url of `https://example.com/folder/#`
 * // if the `#` is provided in the base, it won't be added by `createWebHashHistory`
 * createWebHashHistory('/folder/#/app/') // gives a url of `https://example.com/folder/#/app/`
 * // you should avoid doing this because it changes the original url and breaks copying urls
 * createWebHashHistory('/other-folder/') // gives a url of `https://example.com/other-folder/#`
 *
 * // at file:///usr/etc/folder/index.html
 * // for locations with no `host`, the base is ignored
 * createWebHashHistory('/iAmIgnored') // gives a url of `file:///usr/etc/folder/index.html#`
 * ```
 */
declare function createWebHashHistory(base?: string): RouterHistory;
//#endregion
//#region src/history/html5.d.ts
/**
 * Creates an HTML5 history. Most common history for single page applications.
 *
 * @param base -
 */
declare function createWebHistory(base?: string): RouterHistory;
//#endregion
//#region src/history/memory.d.ts
/**
 * Creates an in-memory based history. The main purpose of this history is to handle SSR. It starts in a special location that is nowhere.
 * It's up to the user to replace that location with the starter location by either calling `router.push` or `router.replace`.
 *
 * @param base - Base applied to all urls, defaults to '/'
 * @returns a history object that can be passed to the router constructor
 */
declare function createMemoryHistory(base?: string): RouterHistory;
//#endregion
//#region src/router.d.ts
/**
 * Options to initialize a {@link Router} instance.
 */
interface RouterOptions extends EXPERIMENTAL_RouterOptions_Base {
  /**
   * Initial list of routes that should be added to the router.
   */
  routes: Readonly<RouteRecordRaw[]>;
}
/**
 * Router instance.
 */
interface RouterClassic extends EXPERIMENTAL_Router_Base<RouteRecordNormalized> {
  /**
   * Original options object passed to create the Router
   */
  readonly options: RouterOptions;
  /**
   * Add a new {@link RouteRecordRaw | route record} as the child of an existing route.
   *
   * @param parentName - Parent Route Record where `route` should be appended at
   * @param route - Route Record to add
   */
  addRoute(parentName: NonNullable<RouteRecordNameGeneric>, route: RouteRecordRaw): () => void;
  /**
   * Add a new {@link RouteRecordRaw | route record} to the router.
   *
   * @param route - Route Record to add
   */
  addRoute(route: RouteRecordRaw): () => void;
  /**
   * Remove an existing route by its name.
   *
   * @param name - Name of the route to remove
   */
  removeRoute(name: NonNullable<RouteRecordNameGeneric>): void;
  /**
   * Delete all routes from the router.
   */
  clearRoutes(): void;
}
/**
 * Router instance.
 *
 * By default this resolves to the classic {@link RouterClassic}. Augment
 * {@link TypesConfig} with a `Router` slot to swap the public type.
 *
 * ```ts
 * import { router } from './router'
 * declare module 'vue-router' {
 *   interface TypesConfig {
 *     Router: typeof router
 *   }
 * }
 * ```
 */
type Router = TypesConfig extends Record<'Router', infer T> ? T : RouterClassic;
/**
 * Creates a Router instance that can be used by a Vue app.
 *
 * @param options - {@link RouterOptions}
 */
declare function createRouter(options: RouterOptions): Router;
//#endregion
//#region src/injectionSymbols.d.ts
/**
 * RouteRecord being rendered by the closest ancestor Router View. Used for
 * `onBeforeRouteUpdate` and `onBeforeRouteLeave`. rvlm stands for Router View
 * Location Matched
 *
 * @internal
 */
declare const matchedRouteKey: InjectionKey<ComputedRef<RouteRecordNormalized | undefined>>;
/**
 * Allows overriding the router view depth to control which component in
 * `matched` is rendered. rvd stands for Router View Depth
 *
 * @internal
 */
declare const viewDepthKey: InjectionKey<Ref<number> | number>;
/**
 * Allows overriding the router instance returned by `useRouter` in tests. r
 * stands for router
 *
 * @internal
 */
declare const routerKey: InjectionKey<Router>;
/**
 * Allows overriding the current route returned by `useRoute` in tests. rl
 * stands for route location
 *
 * @internal
 */
declare const routeLocationKey: InjectionKey<RouteLocationNormalizedLoaded>;
/**
 * Allows overriding the current route used by router-view. Internally this is
 * used when the `route` prop is passed.
 *
 * @internal
 */
declare const routerViewLocationKey: InjectionKey<Ref<RouteLocationNormalizedLoaded>>;
//#endregion
//#region src/navigationGuards.d.ts
/**
 * Add a navigation guard that triggers whenever the component for the current
 * location is about to be left. Similar to {@link beforeRouteLeave} but can be
 * used in any component. The guard is removed when the component is unmounted.
 *
 * @param leaveGuard - {@link NavigationGuard}
 */
declare function onBeforeRouteLeave(leaveGuard: NavigationGuard): void;
/**
 * Add a navigation guard that triggers whenever the current location is about
 * to be updated. Similar to {@link beforeRouteUpdate} but can be used in any
 * component. The guard is removed when the component is unmounted.
 *
 * @param updateGuard - {@link NavigationGuard}
 */
declare function onBeforeRouteUpdate(updateGuard: NavigationGuard): void;
/**
 * Ensures a route is loaded, so it can be passed as o prop to `<RouterView>`.
 *
 * @param route - resolved route to load
 */
declare function loadRouteLocation(route: RouteLocation | RouteLocationNormalized): Promise<RouteLocationNormalizedLoaded>;
//#endregion
//#region src/RouterLink.d.ts
interface RouterLinkOptions {
  /**
   * Route Location the link should navigate to when clicked on.
   */
  to: RouteLocationRaw;
  /**
   * Calls `router.replace` instead of `router.push`.
   */
  replace?: boolean;
}
interface RouterLinkProps extends RouterLinkOptions {
  /**
   * Whether RouterLink should not wrap its content in an `a` tag. Useful when
   * using `v-slot` to create a custom RouterLink
   */
  custom?: boolean;
  /**
   * Class to apply when the link is active
   */
  activeClass?: string;
  /**
   * Class to apply when the link is exact active
   */
  exactActiveClass?: string;
  /**
   * Value passed to the attribute `aria-current` when the link is exact active.
   *
   * @defaultValue `'page'`
   */
  ariaCurrentValue?: 'page' | 'step' | 'location' | 'date' | 'time' | 'true' | 'false';
  /**
   * Pass the returned promise of `router.push()` to `document.startViewTransition()` if supported.
   */
  viewTransition?: boolean;
}
/**
 * Options passed to {@link useLink}.
 */
interface UseLinkOptions<Name extends keyof RouteMap = keyof RouteMap> {
  to: MaybeRef<RouteLocationAsString | RouteLocationAsRelativeTyped<RouteMap, Name> | RouteLocationAsPath | RouteLocationRaw>;
  replace?: MaybeRef<boolean | undefined>;
  /**
   * Pass the returned promise of `router.push()` to `document.startViewTransition()` if supported.
   */
  viewTransition?: boolean;
}
/**
 * Return type of {@link useLink}.
 * @internal
 */
interface UseLinkReturn<Name extends keyof RouteMap = keyof RouteMap> {
  route: ComputedRef<RouteLocationResolved<Name>>;
  href: ComputedRef<string>;
  isActive: ComputedRef<boolean>;
  isExactActive: ComputedRef<boolean>;
  navigate(e?: MouseEvent): Promise<void | NavigationFailure>;
}
/**
 * Returns the internal behavior of a {@link RouterLink} without the rendering part.
 *
 * @param props - a `to` location and an optional `replace` flag
 */
declare function useLink<Name extends keyof RouteMap = keyof RouteMap>(props: UseLinkOptions<Name>): UseLinkReturn<Name>;
/**
 * Component to render a link that triggers a navigation on click.
 */
declare const RouterLink: _RouterLinkI;
/**
 * @internal
 */
type _RouterLinkPropsTypedBase = AllowedComponentProps & ComponentCustomProps & VNodeProps & RouterLinkProps;
/**
 * @internal
 */
type RouterLinkPropsTyped<Custom extends boolean | undefined> = Custom extends true ? _RouterLinkPropsTypedBase & {
  custom: true;
} : _RouterLinkPropsTypedBase & {
  custom?: false | undefined;
} & Omit<AnchorHTMLAttributes, 'href'>;
/**
 * Typed version of the `RouterLink` component. Its generic defaults to the typed router, so it can be inferred
 * automatically for JSX.
 *
 * @internal
 */
interface _RouterLinkI {
  new <Custom extends boolean | undefined = boolean | undefined>(): {
    $props: RouterLinkPropsTyped<Custom>;
    $slots: {
      default?: ({ route, href, isActive, isExactActive, navigate }: UnwrapRef<UseLinkReturn>) => VNode[];
    };
  };
  /**
   * Access to `useLink()` without depending on using vue-router
   *
   * @internal
   */
  useLink: typeof useLink;
}
//#endregion
//#region src/RouterView.d.ts
interface RouterViewProps {
  name?: string;
  route?: RouteLocationNormalized;
}
/**
 * Component to display the current route the user is at.
 */
declare const RouterView: {
  new (): {
    $props: AllowedComponentProps & ComponentCustomProps & VNodeProps & RouterViewProps;
    $slots: {
      default?: ({ Component, route }: {
        Component: VNode;
        route: RouteLocationNormalizedLoaded;
      }) => VNode[];
    };
  };
};
//#endregion
//#region src/useApi.d.ts
/**
 * Returns the router instance. Equivalent to using `$router` inside
 * templates.
 */
declare function useRouter(): Router;
/**
 * Returns the current route location. Equivalent to using `$route` inside
 * templates.
 */
declare function useRoute<Name extends keyof RouteMap = keyof RouteMap>(_name?: Name): RouteLocationNormalizedLoaded<Name | RouteMap[Name]["childrenNames"]>;
//#endregion
//#region src/index.d.ts
declare module 'vue' {
  interface ComponentCustomOptions {
    /**
     * Guard called when the router is navigating to the route that is rendering
     * this component from a different route. Differently from `beforeRouteUpdate`
     * and `beforeRouteLeave`, `beforeRouteEnter` does not have access to the
     * component instance through `this` because it triggers before the component
     * is even mounted.
     *
     * @param to - RouteLocationRaw we are navigating to
     * @param from - RouteLocationRaw we are navigating from
     * @param next - function to validate, cancel or modify (by redirecting) the
     * navigation
     */
    beforeRouteEnter?: TypesConfig extends Record<'beforeRouteEnter', infer T> ? T : NavigationGuardWithThis<undefined>;
    /**
     * Guard called whenever the route that renders this component has changed, but
     * it is reused for the new route. This allows you to guard for changes in
     * params, the query or the hash.
     *
     * @param to - RouteLocationRaw we are navigating to
     * @param from - RouteLocationRaw we are navigating from
     * @param next - function to validate, cancel or modify (by redirecting) the
     * navigation
     */
    beforeRouteUpdate?: TypesConfig extends Record<'beforeRouteUpdate', infer T> ? T : NavigationGuard;
    /**
     * Guard called when the router is navigating away from the current route that
     * is rendering this component.
     *
     * @param to - RouteLocationRaw we are navigating to
     * @param from - RouteLocationRaw we are navigating from
     * @param next - function to validate, cancel or modify (by redirecting) the
     * navigation
     */
    beforeRouteLeave?: TypesConfig extends Record<'beforeRouteLeave', infer T> ? T : NavigationGuard;
  }
  interface ComponentCustomProperties {
    /**
     * Normalized current location. See {@link RouteLocationNormalizedLoaded}.
     */
    $route: TypesConfig extends Record<'$route', infer T> ? T : RouteLocationNormalizedLoaded;
    /**
     * {@link Router} instance used by the application.
     */
    $router: TypesConfig extends Record<'$router', infer T> ? T : Router;
  }
  interface GlobalComponents {
    RouterView: TypesConfig extends Record<'RouterView', infer T> ? T : typeof RouterView;
    RouterLink: TypesConfig extends Record<'RouterLink', infer T> ? T : typeof RouterLink;
  }
}
//#endregion
//#region src/experimental/data-loaders/utils.d.ts
/**
 * @internal: data loaders authoring only. Use `getCurrentContext` instead.
 */
declare let currentContext: readonly [entry: DataLoaderEntryBase, router: Router, route: RouteLocationNormalizedLoaded] | undefined | null;
declare function getCurrentContext(): readonly [entry: DataLoaderEntryBase<unknown, unknown, unknown>, router: RouterClassic, route: RouteLocationNormalizedLoadedGeneric] | readonly [];
/**
 * Sets the current context for data loaders. This allows for nested loaders to be aware of their parent context.
 * INTERNAL ONLY.
 *
 * @param context - the context to set
 * @internal
 */
declare function setCurrentContext(context?: typeof currentContext | readonly []): void;
/**
 * Restore the current context after a promise is resolved.
 * @param promise - promise to wrap
 */
declare function withLoaderContext<P extends Promise<unknown>>(promise: P): P;
/**
 * Object and promise of the object itself. Used when we can await some of the properties of an object to be loaded.
 * @internal
 */
type _PromiseMerged<PromiseType, RawType = PromiseType> = RawType & Promise<PromiseType>;
/**
 * Track the reads of a route and its properties
 * @internal
 * @param route - route to track
 */
declare function trackRoute(route: RouteLocationNormalizedLoaded): readonly [{
  readonly hash: string;
  readonly params: RouteParamsGeneric;
  readonly query: LocationQuery$1;
  readonly matched: RouteLocationMatched[];
  readonly name: RouteRecordNameGeneric;
  readonly fullPath: string;
  readonly redirectedFrom: RouteLocation | undefined;
  readonly path: string;
  readonly meta: RouteMeta;
}, Partial<RouteParamsGeneric>, Partial<LocationQuery$1>, {
  v: string | null;
}];
//#endregion
//#region src/experimental/data-loaders/navigation-guard.d.ts
/**
 * Options to initialize the data loader guard.
 */
interface SetupLoaderGuardOptions extends DataLoaderPluginOptions {
  /**
   * The Vue app instance. Used to access the `provide` and `inject` APIs.
   */
  app: App<unknown>;
  /**
   * The effect scope to use for the data loaders.
   */
  effect: EffectScope;
}
/**
 * Possible values to change the result of a navigation within a loader
 * @internal
 */
type _DataLoaderRedirectResult = Exclude<ReturnType<NavigationGuard>, Promise<unknown> | Function | true | void | undefined>;
/**
 * Allows data loaders to change navigation. Called by {@link reroute}.
 *
 * @internal
 */
declare class NavigationResult {
  readonly value: _DataLoaderRedirectResult;
  constructor(value: _DataLoaderRedirectResult);
}
/**
 * Changes the navigation from within a data loader. Accepts the same values as a navigation
 * guard return: a route location to redirect to, or `false` to cancel the navigation.
 *
 * @example
 * ```ts
 * export const useUserData = defineBasicLoader(async (to) => {
 *   const user = await fetchUser(to.params.id)
 *   if (!user) {
 *     reroute('/404')
 *   }
 *   return user
 * })
 * ```
 */
declare function reroute(to: _DataLoaderRedirectResult): never;
/**
 * Data Loader plugin to add data loading support to Vue Router.
 *
 * @example
 * ```ts
 * const router = createRouter({
 *   routes,
 *   history: createWebHistory(),
 * })
 *
 * const app = createApp({})
 * app.use(DataLoaderPlugin, { router })
 * app.use(router)
 * ```
 */
declare function DataLoaderPlugin(app: App, options: DataLoaderPluginOptions): void;
/**
 * Options passed to the DataLoaderPlugin.
 */
interface DataLoaderPluginOptions {
  /**
   * The router instance. Adds the guards to it
   */
  router: Router;
  isSSR?: boolean;
  /**
   * List of _expected_ errors that shouldn't abort the navigation (for non-lazy loaders). Provide a list of
   * constructors that can be checked with `instanceof` or a custom function that returns `true` for expected errors.
   */
  errors?: Array<new (...args: any) => any> | ((reason?: unknown) => boolean);
}
/**
 * Return a ref that reflects the global loading state of all loaders within a navigation.
 * This state doesn't update if `refresh()` is manually called.
 */
declare function useIsDataLoading(): ShallowRef<boolean>;
//#endregion
//#region src/experimental/data-loaders/createDataLoader.d.ts
/**
 * Base type for a data loader entry. Each Data Loader has its own entry in the `loaderEntries` (accessible via `[LOADER_ENTRIES_KEY]`) map.
 */
interface DataLoaderEntryBase<TData = unknown, TError = unknown, TDataInitial extends TData | undefined = TData | undefined> {
  /**
   * Data stored in the entry.
   */
  data: ShallowRef<TData | TDataInitial>;
  /**
   * Error if there was an error.
   */
  error: ShallowRef<TError | null>;
  /**
   * Location the data was loaded for or `null` if the data is not loaded.
   */
  to: RouteLocationNormalizedLoaded | null;
  /**
   * Whether there is an ongoing request.
   */
  isLoading: ShallowRef<boolean>;
  options: DefineDataLoaderOptionsBase_LaxData;
  /**
   * Called by the navigation guard when the navigation is duplicated. Should be used to reset pendingTo and pendingLoad and any other property that should be reset.
   */
  resetPending: () => void;
  /**
   * The latest pending load. Allows to verify if the load is still valid when it resolves.
   */
  pendingLoad: Promise<void> | null;
  /**
   * The latest pending navigation's `to` route. Used to verify if the navigation is still valid when it resolves.
   */
  pendingTo: RouteLocationNormalizedLoaded | null;
  /**
   * Data that was staged by a loader. This is used to avoid showing the old data while the new data is loading. Calling
   * the internal `commit()` function will replace the data with the staged data.
   */
  staged: TData | typeof STAGED_NO_VALUE;
  /**
   * Error that was staged by a loader. This is used to avoid showing the old error while the new data is loading.
   * Calling the internal `commit()` function will replace the error with the staged error.
   */
  stagedError: TError | null;
  /**
   * Other data loaders that depend on this one. This is used to invalidate the data when a dependency is invalidated.
   */
  children: Set<DataLoaderEntryBase>;
  /**
   * Commits the pending data to the entry. This is called by the navigation guard when all non-lazy loaders have
   * finished loading. It should be implemented by the loader. It **must be called** from the entry itself:
   * `entry.commit(to)`.
   */
  commit(to: RouteLocationNormalizedLoaded): void;
}
/**
 * Common properties for the options of `defineLoader()`. Types are `unknown` to allow for more specific types in the
 * extended types while having documentation in one single place.
 * @internal
 */
interface _DefineDataLoaderOptionsBase_Common {
  /**
   * When the data should be committed to the entry. In the case of lazy loaders, the loader will try to commit the data
   * after all non-lazy loaders have finished loading, but it might not be able to if the lazy loader hasn't been
   * resolved yet.
   *
   * @see {@link DefineDataLoaderCommit}
   * @defaultValue `'after-load'`
   */
  commit?: DefineDataLoaderCommit;
  /**
   * Whether the data should be lazy loaded without blocking the client side navigation or not. When set to true, the loader will no longer block the navigation and the returned composable can be called even
   * without having the data ready.
   *
   * @defaultValue `false`
   */
  lazy?: unknown;
  /**
   * Whether this loader should be awaited on the server side or not. Combined with the `lazy` option, this gives full
   * control over how to await for the data.
   *
   * @defaultValue `true`
   */
  server?: unknown;
  /**
   * List of _expected_ errors that shouldn't abort the navigation (for non-lazy loaders). Provide a list of
   * constructors that can be checked with `instanceof` or a custom function that returns `true` for expected errors. Can also be set to `true` to accept all globally defined errors. Defaults to `false` to abort on any error.
   * @default `false`
   */
  errors?: unknown;
}
/**
 * Options for a data loader that returns a data that is possibly `undefined`. Available for data loaders
 * implementations so they can be used in `defineLoader()` overloads.
 */
interface DefineDataLoaderOptionsBase_LaxData extends _DefineDataLoaderOptionsBase_Common {
  lazy?: boolean | ((to: RouteLocationNormalizedLoaded, from?: RouteLocationNormalizedLoaded) => boolean);
  server?: boolean;
  errors?: boolean | Array<new (...args: any[]) => any> | ((reason?: unknown) => boolean);
}
/**
 * Options for a data loader making the data defined without it being possibly `undefined`. Available for data loaders
 * implementations so they can be used in `defineLoader()` overloads.
 */
interface DefineDataLoaderOptionsBase_DefinedData extends _DefineDataLoaderOptionsBase_Common {
  lazy?: false;
  server?: true;
  errors?: false;
}
declare const toLazyValue: (lazy: undefined | DefineDataLoaderOptionsBase_LaxData["lazy"], to: RouteLocationNormalizedLoaded, from?: RouteLocationNormalizedLoaded) => boolean | undefined;
/**
 * When the data should be committed to the entry.
 * - `immediate`: the data is committed as soon as it is loaded.
 * - `after-load`: the data is committed after all non-lazy loaders have finished loading.
 */
type DefineDataLoaderCommit = 'immediate' | 'after-load';
interface DataLoaderContextBase {
  /**
   * Signal associated with the current navigation. It is aborted when the navigation is canceled or an error occurs.
   */
  signal: AbortSignal | undefined;
}
/**
 * Data Loader composable returned by `defineLoader()`.
 * @see {@link DefineDataLoader}
 */
interface UseDataLoader<Data = unknown, TError = unknown> {
  [IS_USE_DATA_LOADER_KEY]: true;
  /**
   * Data Loader composable returned by `defineLoader()`.
   *
   * @example
   * Returns the Data loader data, isLoading, error etc. Meant to be used in `setup()` or `<script setup>` **without `await`**:
   * ```vue
   * <script setup>
   * const { data, isLoading, error } = useUserData()
   * </script>
   * ```
   *
   * @example
   * It also returns a promise of the data when used in nested loaders. Note this `data` is **not a ref**. This is not meant to be used in `setup()` or `<script setup>`.
   * ```ts
   * export const useUserConnections = defineLoader(async () => {
   *   const user = await useUserData()
   *   return fetchUserConnections(user.id)
   * })
   * ```
   */
  (): _PromiseMerged<Exclude<Data, NavigationResult | undefined>, UseDataLoaderResult<Exclude<Data, NavigationResult>, TError>>;
  /**
   * Internals of the data loader.
   * @internal
   */
  _: UseDataLoaderInternals<Exclude<Data, NavigationResult | undefined>, TError>;
}
/**
 * Internal properties of a data loader composable. Used by the internal implementation of `defineLoader()`. **Should
 * not be used in application code.**
 */
interface UseDataLoaderInternals<Data = unknown, TError = unknown> {
  /**
   * Loads the data from the cache if possible, otherwise loads it from the loader and awaits it.
   *
   * @param to - route location to load the data for
   * @param router - router instance
   * @param from - route location we are coming from
   * @param parent - parent data loader entry
   */
  load: (to: RouteLocationNormalizedLoaded, router: Router, from?: RouteLocationNormalizedLoaded, parent?: DataLoaderEntryBase) => Promise<void>;
  /**
   * Resolved options for the loader.
   */
  options: DefineDataLoaderOptionsBase_LaxData;
  /**
   * Gets the entry associated with the router instance. Assumes the data loader has been loaded and that the entry
   * exists.
   *
   * @param router - router instance
   */
  getEntry(router: Router): DataLoaderEntryBase<Data, TError>;
}
/**
 * Return value of a loader composable defined with `defineLoader()`.
 */
interface UseDataLoaderResult<TData = unknown, TError = ErrorDefault> {
  /**
   * Data returned by the loader. If the data loader is lazy, it will be undefined until the first load.
   */
  data: ShallowRef<TData>;
  /**
   * Whether there is an ongoing request.
   */
  isLoading: ShallowRef<boolean>;
  /**
   * Error if there was an error.
   */
  error: ShallowRef<TError | null>;
  /**
   * Reload the data using the current route location. Returns a promise that resolves when the data is reloaded. This
   * method should not be called during a navigation as it can conflict with an ongoing load and lead to
   * inconsistencies.
   */
  reload(): Promise<void>;
  /**
   * Reload the data using the route location passed as argument. Returns a promise that resolves when the data is reloaded.
   *
   * @param route - route location to load the data for
   */
  reload(route: RouteLocationNormalizedLoaded): Promise<void>;
}
/**
 * Loader function that can be passed to `defineLoader()`.
 */
interface DefineLoaderFn<Data, Context extends DataLoaderContextBase = DataLoaderContextBase, Route = RouteLocationNormalizedLoaded> {
  (route: Route, context: Context): Promise<Data>;
}
//#endregion
export { RouteLocationAsPath as $, defineParamParserRaw as $n, RouteQueryAndHash as $t, useLink as A, MatchMiss as An, LocationQueryValue$1 as Ar, ParamValue as At, RouterClassic as B, EXPERIMENTAL_RouterOptions as Bn, LocationAsRelativeRaw as Bt, RouterView as C, ParamParserTypeOf as Cn, createRouterMatcher as Cr, RouteLocationResolvedTypedList as Ct, UseLinkOptions as D, PathParamNamesForFilePath as Dn, RouteRecordNormalized as Dr, RouteRecordNameGeneric as Dt, RouterLinkProps as E, ParamParsers_Native as En, RouteRecord as Er, RouteRecordName as Et, routeLocationKey as F, EXPERIMENTAL_RouteRecordRaw as Fn, RouteParamsRaw as Ft, createWebHashHistory as G, createFixedResolver as Gn, RouteLocationNamedRaw as Gt, createRouter as H, EXPERIMENTAL_Router_Base as Hn, MatcherLocationAsPath as Ht, routerKey as I, EXPERIMENTAL_RouteRecord_Base as In, RouteMap as It, NavigationGuardNextCallback as J, ExtractParamParserType as Jn, RouteMeta as Jt, NavigationGuard as K, MatcherPatternQuery as Kn, RouteLocationOptions as Kt, routerViewLocationKey as L, EXPERIMENTAL_RouteRecord_Group as Ln, RouteMapGeneric as Lt, onBeforeRouteLeave as M, EXPERIMENTAL_RouteRecordNormalized as Mn, parseQuery as Mr, ParamValueZeroOrMore as Mt, onBeforeRouteUpdate as N, EXPERIMENTAL_RouteRecordNormalized_Group as Nn, stringifyQuery as Nr, ParamValueZeroOrOne as Nt, UseLinkReturn as O, _mergeRouteRecord as On, LocationQuery$1 as Or, RouteRecordRedirectOption as Ot, matchedRouteKey as P, EXPERIMENTAL_RouteRecordNormalized_Matchable as Pn, TypesConfig as Pr, RouteParams as Pt, RouteLocation as Q, defineParamParser as Qn, RouteParamsRawGeneric as Qt, viewDepthKey as R, EXPERIMENTAL_RouteRecord_Matchable as Rn, RouteRecordInfo as Rt, useRouter as S, ParamParserType as Sn, RouterMatcher as Sr, RouteLocationResolvedTyped as St, RouterLink as T, ParamParsers as Tn, _PathParserOptions as Tr, RouteLocationTypedList as Tt, createMemoryHistory as U, experimental_createRouter as Un, RouteComponent as Ut, RouterOptions as V, EXPERIMENTAL_RouterOptions_Base as Vn, MatcherLocation as Vt, createWebHistory as W, normalizeRouteRecord as Wn, RouteLocationMatched as Wt, NavigationGuardWithThis as X, PARAM_PARSER_INT as Xn, RouteParamValueRaw as Xt, NavigationGuardReturn as Y, normalizeParamParser as Yn, RouteParamValue as Yt, NavigationHookAfter as Z, PARAM_PARSER_BOOL as Zn, RouteParamsGeneric as Zt, getCurrentContext as _, defineBasicLoader as _n, NavigationFailureType as _r, RouteLocationNormalizedTyped as _t, DefineLoaderFn as a, RouteRecordSingleViewWithChildren as an, MatcherPatternPath as ar, RouteLocationAsRelativeTyped as at, withLoaderContext as b, DefinePageQueryParamOptions as bn, HistoryState as br, RouteLocationResolved as bt, UseDataLoaderResult as c, NavigationResult$1 as cn, MatcherPatternPathStatic as cr, RouteLocationAsStringTyped as ct, DataLoaderPluginOptions as d, DefineDataLoaderOptions as dn, _Awaitable as dr, RouteLocationNormalized as dt, RouteRecordMultipleViews as en, START_LOCATION_NORMALIZED as er, RouteLocationAsPathGeneric as et, NavigationResult as f, DefineDataLoaderOptions_DefinedData as fn, ParamParser as fr, RouteLocationNormalizedGeneric as ft, _PromiseMerged as g, UseDataLoaderBasic_LaxData as gn, NavigationFailure as gr, RouteLocationNormalizedLoadedTypedList as gt, useIsDataLoading as h, UseDataLoaderBasic_DefinedData as hn, ErrorTypes as hr, RouteLocationNormalizedLoadedTyped as ht, DefineDataLoaderOptionsBase_LaxData as i, RouteRecordSingleView as in, MatcherPatternHash as ir, RouteLocationAsRelativeGeneric as it, loadRouteLocation as j, miss as jn, LocationQueryValueRaw$1 as jr, ParamValueOneOrMore as jt, _RouterLinkI as k, definePage as kn, LocationQueryRaw$1 as kr, _RouteRecordProps as kt, toLazyValue as l, DataLoaderBasicEntry as ln, MatcherQueryParams as lr, RouteLocationAsStringTypedList as lt, reroute as m, UseDataLoaderBasic as mn, LocationQuery as mr, RouteLocationNormalizedLoadedGeneric as mt, DataLoaderEntryBase as n, RouteRecordRaw as nn, MatcherParamsFormatted as nr, RouteLocationAsPathTypedList as nt, UseDataLoader as o, _RouteLocationBase as on, MatcherPatternPathDynamic as or, RouteLocationAsRelativeTypedList as ot, SetupLoaderGuardOptions as p, DefineDataLoaderOptions_LaxData as pn, RouterScrollBehavior as pr, RouteLocationNormalizedLoaded as pt, NavigationGuardNext as q, MatcherPatternQueryParam as qn, RouteLocationPathRaw as qt, DefineDataLoaderOptionsBase_DefinedData as r, RouteRecordRedirect as rn, MatcherPattern as rr, RouteLocationAsRelative as rt, UseDataLoaderInternals as s, _RouteRecordBase as sn, MatcherPatternPathDynamic_ParamOptions as sr, RouteLocationAsString as st, DataLoaderContextBase as t, RouteRecordMultipleViewsWithChildren as tn, EmptyParams as tr, RouteLocationAsPathTyped as tt, DataLoaderPlugin as u, DataLoaderContext as un, MatcherQueryParamsValue as ur, RouteLocationGeneric as ut, setCurrentContext as v, ErrorDefault as vn, NavigationRedirectError as vr, RouteLocationNormalizedTypedList as vt, RouterViewProps as w, ParamParserType_Native as wn, PathParserOptions as wr, RouteLocationTyped as wt, useRoute as x, DefinePageQueryParamOptionsAny as xn, RouterHistory as xr, RouteLocationResolvedGeneric as xt, trackRoute as y, DefinePage as yn, isNavigationFailure as yr, RouteLocationRaw as yt, Router as z, EXPERIMENTAL_Router as zn, RouteRecordInfoGeneric as zt };