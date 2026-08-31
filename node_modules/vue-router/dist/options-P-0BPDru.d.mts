/*!
 * vue-router v5.2.0
 * (c) 2026 Eduardo San Martin Morote
 * @license MIT
 */
import { AllowedComponentProps, AnchorHTMLAttributes, App, Component as Component$1, ComponentCustomProps, ComponentPublicInstance, ComputedRef, DefineComponent, EffectScope, MaybeRef, ShallowRef, UnwrapRef, VNode, VNodeProps } from "vue";
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
type LocationQueryValueRaw = LocationQueryValue$1 | number | undefined;
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
type LocationQueryRaw = Record<string | number, LocationQueryValueRaw | LocationQueryValueRaw[]>;
/**
 * Stringifies a {@link LocationQueryRaw} object. Like `URLSearchParams`, it
 * doesn't prepend a `?`
 *
 * @internal
 *
 * @param query - query object to stringify
 * @returns string version of the query without the leading `?`
 */
declare function stringifyQuery(query: LocationQueryRaw | undefined): string;
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
type _Awaitable$1<T> = T | PromiseLike<T>;
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
  next: NavigationGuardNext): _Awaitable$1<NavigationGuardReturn>;
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
  next: NavigationGuardNext): _Awaitable$1<NavigationGuardReturn>;
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
  next: NavigationGuardNext): _Awaitable$1<NavigationGuardReturn>;
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
 * Normalized query object that appears in {@link RouteLocationNormalized}
 *
 * @public
 */
type LocationQuery = Record<string, LocationQueryValue | LocationQueryValue[]>;
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
 * Object and promise of the object itself. Used when we can await some of the properties of an object to be loaded.
 * @internal
 */
type _PromiseMerged<PromiseType, RawType = PromiseType> = RawType & Promise<PromiseType>;
//#endregion
//#region src/experimental/data-loaders/navigation-guard.d.ts
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
 * When the data should be committed to the entry.
 * - `immediate`: the data is committed as soon as it is loaded.
 * - `after-load`: the data is committed after all non-lazy loaders have finished loading.
 */
type DefineDataLoaderCommit = 'immediate' | 'after-load';
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
//#endregion
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
//#region src/experimental/data-loaders/defineLoader.d.ts
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
  query?: LocationQueryRaw;
  hash?: string;
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
//#region src/experimental/runtime.d.ts
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
//#region src/unplugin/core/customBlock.d.ts
interface CustomRouteBlock extends Partial<Omit<RouteRecordRaw, 'components' | 'component' | 'children' | 'beforeEnter' | 'name' | 'alias'>> {
  name?: string | undefined | false;
  alias?: string[];
  params?: {
    /**
     * Override the parser for a given path param. Set to `null` to remove a
     * filename-based parser (e.g. revert `[id=int]` back to no parser).
     */
    path?: Record<string, string | null>;
    /**
     * Declare query params for the route. The value is either a parser name
     * or an options object with `parser`, `format`, `default`, and `required`.
     */
    query?: Record<string, string | CustomRouteBlockQueryParamOptions>;
  };
}
interface CustomRouteBlockQueryParamOptions {
  parser?: string;
  format?: DefinePageQueryParamOptions['format'];
  default?: string;
  required?: boolean;
}
//#endregion
//#region src/unplugin/core/treeNodeValue.d.ts
declare const enum TreeNodeType {
  static = 0,
  group = 1,
  param = 2
}
/**
 * Internal merged-overrides shape used by tree nodes. Same structure as
 * {@link CustomRouteBlock} (the user-facing `<route>` block / `definePage()`
 * payload).
 */
type RouteRecordOverride = CustomRouteBlock;
type SubSegment = string | TreePathParam;
declare class _TreeNodeValueBase {
  /**
   * flag based on the type of the segment
   */
  _type: TreeNodeType;
  parent: TreeNodeValue | undefined;
  /**
   * segment as defined by the file structure e.g. keeps the `index` name, `(group-name)`
   */
  rawSegment: string;
  /**
   * transformed version of the segment into a vue-router path. e.g. `'index'` becomes `''` and `[param]` becomes
   * `:param`, `prefix-[param]-end` becomes `prefix-:param-end`.
   */
  pathSegment: string;
  /**
   * Array of sub segments. This is usually one single elements but can have more for paths like `prefix-[param]-end.vue`
   */
  subSegments: SubSegment[];
  /**
   * Overrides defined by each file. The map is necessary to handle named views.
   */
  private _overrides;
  /**
   * View name (Vue Router feature) mapped to their corresponding file. By default, the view name is `default` unless
   * specified with a `@` e.g. `index@aux.vue` will have a view name of `aux`.
   */
  components: Map<string, string>;
  constructor(rawSegment: string, parent: TreeNodeValue | undefined, pathSegment?: string, subSegments?: SubSegment[]);
  /**
   * Path of the node. Can be absolute or not. If it has been overridden, it
   * will return the overridden path.
   */
  get path(): string;
  /**
   * Aliases of the node if any.
   */
  get alias(): string[];
  /**
   * Full path of the node including parent nodes.
   */
  get fullPath(): string;
  /**
   * Gets all the query params for the node. This does not include params from parent nodes.
   */
  get queryParams(): TreeQueryParam[];
  /**
   * Gets all the params for the node including path and query params. This
   * does not include params from parent nodes.
   */
  get params(): (TreePathParam | TreeQueryParam)[];
  toString(): string;
  isParam(): this is TreeNodeValueParam;
  isStatic(): this is TreeNodeValueStatic;
  isGroup(): this is TreeNodeValueGroup;
  get overrides(): CustomRouteBlock;
  setOverride(filePath: string, routeBlock: CustomRouteBlock | undefined): void;
  /**
   * Remove all overrides for a given key.
   *
   * @param key - key to remove from the override, e.g. path, name, etc
   */
  removeOverride(key: keyof CustomRouteBlock): void;
  /**
   * Add an override to the current node by merging with the existing values.
   *
   * @param filePath - The file path to add to the override
   * @param routeBlock - The route block to add to the override
   */
  mergeOverride(filePath: string, routeBlock: CustomRouteBlock): void;
  /**
   * Add an override to the current node using the special file path `@@edits` that makes this added at build time.
   *
   * @param routeBlock -  The route block to add to the override
   */
  addEditOverride(routeBlock: CustomRouteBlock): void;
  /**
   * Set a specific value in the _edits_ override.
   *
   * @param key - key to set in the override, e.g. path, name, etc
   * @param value - value to set in the override
   */
  setEditOverride<K extends keyof RouteRecordOverride>(key: K, value: RouteRecordOverride[K]): void;
}
/**
 * - Static
 * - Static + Custom Param (subSegments)
 * - Static + Param (subSegments)
 * - Custom Param
 * - Param
 * - CatchAll
 */
/**
 * Static path like `/users`, `/users/list`, etc
 * @extends _TreeNodeValueBase
 */
declare class TreeNodeValueStatic extends _TreeNodeValueBase {
  _type: TreeNodeType.static;
  readonly score: number[];
  constructor(rawSegment: string, parent: TreeNodeValue | undefined, pathSegment?: string);
}
declare class TreeNodeValueGroup extends _TreeNodeValueBase {
  _type: TreeNodeType.group;
  groupName: string;
  readonly score: number[];
  constructor(rawSegment: string, parent: TreeNodeValue | undefined, pathSegment: string, groupName: string);
}
interface TreePathParam {
  paramName: string;
  modifier: string;
  optional: boolean;
  repeatable: boolean;
  isSplat: boolean;
  parser: string | null;
}
interface TreeQueryParam {
  paramName: string;
  queryKey?: string;
  parser: string | null;
  format: 'value' | 'array' | null;
  /**
   * Expression to be passed as is to the default value of the param.
   */
  defaultValue?: string;
  /**
   * Whether the query param is required.
   *
   * @default false
   */
  required?: boolean;
}
declare class TreeNodeValueParam extends _TreeNodeValueBase {
  private filenamePathParams;
  _type: TreeNodeType.param;
  /**
   * @param rawSegment The raw segment as defined by the file structure, e.g.
   * `[id]`, `prefix-[param]-end`, etc.
   *
   * @param parent The parent node in the tree, if any.
   *
   * @param filenamePathParams Path params parsed from the file segment
   * (filename convention). The public `pathParams` getter overlays
   * `definePage()` parser overrides on top of these.
   *
   * @param pathSegment The transformed version of the segment into a
   * vue-router path, e.g. `:id`, `prefix-:param-end`, etc.
   *
   * @param subSegments Array of sub segments. This is usually one single
   * element but can have more for paths like `prefix-[param]-end.vue`.
   */
  constructor(rawSegment: string, parent: TreeNodeValue | undefined, filenamePathParams: TreePathParam[], pathSegment: string, subSegments: SubSegment[]);
  /**
   * Path params for this node, with `definePage({ params: { path: ... } })`
   * parser overrides applied on top of the filename-based parsers.
   */
  get pathParams(): TreePathParam[];
  get score(): number[];
  /**
   * Generates the regex pattern for the path segment.
   */
  get re(): string;
  toString(): string;
}
type TreeNodeValue = TreeNodeValueStatic | TreeNodeValueParam | TreeNodeValueGroup;
interface TreeNodeValueOptions extends ParseSegmentOptions {
  /**
   * Format of the route path. Defaults to `file` which is the format used by vue-router and matches the file
   * structure (e.g. `index`, ``, or `users/[id]`). In `path` format, routes are expected in the format of vue-router
   * (e.g. `/` or '/users/:id' ).
   *
   * @default `'file'`
   */
  format?: 'file' | 'path';
}
/**
 * Creates a new TreeNodeValue based on the segment. The result can be a static segment, group segment or a param segment.
 *
 * @param segment - path segment
 * @param parent - parent node
 * @param options - options
 */
declare function createTreeNodeValue(segment: string, parent?: TreeNodeValue, opts?: TreeNodeValueOptions): TreeNodeValue;
/**
 * Options passed to `parseSegment()`to control how a segment of a file path is
 * parsed. e.g. in `/users/[id]`, `users` and `[id]` are segments.
 */
interface ParseSegmentOptions {
  /**
   * Should we allow dot nesting in the param name. e.g. `users.[id]` will be
   * parsed as `users/[id]` if this is `true`, nesting. Note this only works
   * for the `file` format.
   *
   * @default `true`
   */
  dotNesting?: boolean;
}
//#endregion
//#region src/unplugin/core/tree.d.ts
interface TreeNodeOptions extends ResolvedOptions {
  treeNodeOptions?: TreeNodeValueOptions;
}
/**
 * Parts used by MatcherPatternPathDynamic to match a route.
 *
 * @internal
 */
type TreeNodeValueMatcherPart = Array<string | number | Array<string | number>>;
/**
 * Makes the `name` property required and a string. Used for readability
 *
 * @internal
 */
type TreeNodeNamed = TreeNode & {
  name: Extract<TreeNode['name'], string>;
};
declare class TreeNode {
  /**
   * value of the node
   */
  value: TreeNodeValue;
  /**
   * children of the node
   */
  children: Map<string, TreeNode>;
  /**
   * Parent node.
   */
  parent: TreeNode | undefined;
  /**
   * Plugin options taken into account by the tree.
   */
  options: TreeNodeOptions;
  /**
   * Set of file paths that use `definePage()` with runtime properties (meta, props, etc.) that require a `?definePage`
   * import at build time. Tracked per-file to avoid race conditions when multiple files (e.g. named views) map to the
   * same node.
   */
  private _needsDefinePageImport;
  /**
   * Whether at least one component file uses `definePage()` with runtime properties (meta, props, etc.) that require a
   * `?definePage` import at build time.
   */
  get needsDefinePageImport(): boolean;
  /**
   * Mark whether a file needs a `?definePage` import.
   */
  setDefinePageImport(filePath: string, needsImport: boolean): void;
  /**
   * Check if a specific file needs a `?definePage` import.
   */
  fileNeedsDefinePageImport(filePath: string): boolean;
  /**
   * Creates a new tree node.
   *
   * @param options - TreeNodeOptions shared by all nodes
   * @param pathSegment - path segment of this node e.g. `users` or `:id`
   * @param parent
   */
  constructor(options: TreeNodeOptions, pathSegment: string, parent?: TreeNode);
  /**
   * Adds a path to the tree. `path` cannot start with a `/`.
   *
   * @param path - path segment to insert. **It shouldn't contain the file extension**
   * @param filePath - file path, must be a file (not a folder)
   */
  insert(path: string, filePath: string): TreeNode;
  /**
   * Adds a path that has already been parsed to the tree. `path` cannot start with a `/`. This method is similar to
   * `insert` but the path argument should be already parsed. e.g. `users/:id` for a file named `users/[id].vue`.
   *
   * @param path - path segment to insert, already parsed (e.g. users/:id)
   * @param filePath - file path, defaults to path for convenience and testing
   */
  insertParsedPath(path: string, filePath?: string): TreeNode;
  /**
   * Saves a custom route block for a specific file path. The file path is used
   * as a key. Some special file paths will have a lower or higher priority.
   *
   * @param filePath - file path where the custom block is located
   * @param routeBlock - custom block to set
   */
  setCustomRouteBlock(filePath: string, routeBlock: CustomRouteBlock | undefined): void;
  /**
   * Generator that yields all descendants without sorting.
   * Use with Array.from() for now, native .map() support in Node 22+.
   */
  getChildrenDeep(): Generator<TreeNode>;
  /**
   * Comparator function for sorting TreeNodes.
   *
   * @internal
   */
  static compare(a: TreeNode, b: TreeNode): number;
  /**
   * Get the children of this node sorted by their path.
   */
  getChildrenSorted(): TreeNode[];
  /**
   * Calls {@link getChildrenDeep} and sorts the result by path in the end.
   */
  getChildrenDeepSorted(): TreeNode[];
  /**
   * Delete a child node. If the child node has no more children and no
   * components, it will be deleted as well. This is used to recursively delete
   * empty nodes after removing a route.
   *
   * @param child - child node to delete
   */
  protected deleteChild(child: TreeNode): void;
  /**
   * Delete and detach itself from the tree.
   */
  delete(): void;
  /**
   * Remove a route from the tree. The path shouldn't start with a `/` but it can be a nested one. e.g. `foo/bar`.
   * The `path` should be relative to the page folder.
   *
   * @param path - path segment of the file
   */
  remove(path: string): void;
  /**
   * Returns the route path of the node without parent paths. If the path was overridden, it returns the override.
   */
  get path(): string;
  /**
   * Returns the route path of the node including parent paths.
   */
  get fullPath(): string;
  /**
   * Returns the alias of the node
   */
  get alias(): string[];
  /**
   * Object of components (filepaths) for this node.
   */
  get components(): {
    [k: string]: string;
  };
  /**
   * Does this node render any component?
   */
  get hasComponents(): boolean;
  /**
   * Returns the route name of the node. If the name was overridden, it returns the override.
   */
  get name(): string | false;
  /**
   * Returns the meta property as an object.
   */
  get metaAsObject(): Readonly<RouteMeta>;
  /**
   * Returns the JSON string of the meta object of the node. If the meta was overridden, it returns the override. If
   * there is no override, it returns an empty string.
   */
  get meta(): string;
  /**
   * Array of route params for this node. It includes **all** the params from the parents as well.
   */
  get params(): (TreePathParam | TreeQueryParam)[];
  /**
   * Array of route params coming from the path. It includes all the params from the parents as well.
   */
  get pathParams(): TreePathParam[];
  /**
   * Array of query params extracted from definePage. Only returns query params from this specific node.
   */
  get queryParams(): TreeQueryParam[];
  /**
   * Generates a regexp based on this node and its parents. This regexp is used by the custom resolver
   */
  get regexp(): string;
  /**
   * Score of the path used for sorting routes.
   */
  get score(): number[][];
  /**
   * Is this node a splat (catch-all) param
   */
  get isSplat(): boolean;
  /**
   * Returns an array of matcher parts that is consumed by
   * MatcherPatternPathDynamic to render the path.
   */
  get matcherPatternPathDynamicParts(): TreeNodeValueMatcherPart;
  /**
   * Is this tree node matchable? A matchable node has at least one component
   * and a name.
   */
  isMatchable(): this is TreeNode & {
    name: string;
  };
  /**
   * Returns wether this tree node is the root node of the tree.
   *
   * @returns true if the node is the root node
   */
  isRoot(): this is PrefixTree;
  /**
   * Returns wether this tree node has a name. This allows to coerce the type
   * of TreeNode
   */
  isNamed(): this is TreeNodeNamed;
  toString(): string;
  /**
   * Iterates over the tree in a breadth-first way. It first yields the direct
   * children of the node, then their children and so on. The order of the
   * children is not guaranteed.
   */
  [Symbol.iterator](): Generator<TreeNode, void, unknown>;
}
/**
 * Creates a new prefix tree. This is meant to only be the root node. It has access to extra methods that only make
 * sense on the root node.
 */
declare class PrefixTree extends TreeNode {
  map: Map<string, TreeNode>;
  constructor(options: ResolvedOptions);
  insert(path: string, filePath: string): TreeNode;
  /**
   * Returns the tree node of the given file path.
   *
   * @param filePath - file path of the tree node to get
   */
  getChild(filePath: string): TreeNode | undefined;
  /**
   * Removes the tree node of the given file path.
   *
   * @param filePath - file path of the tree node to remove
   */
  removeChild(filePath: string): void;
}
//#endregion
//#region src/unplugin/core/utils.d.ts
/**
 * Creates a name based of the node path segments.
 *
 * @param node - the node to get the path from
 * @param parent - the parent node
 * @returns a route name
 */
declare function getPascalCaseRouteName(node: TreeNode): string;
/**
 * Joins the path segments of a node into a name that corresponds to the filepath represented by the node.
 *
 * @param node - the node to get the path from
 * @returns a route name
 */
declare function getFileBasedRouteName(node: TreeNode): string;
//#endregion
//#region src/unplugin/core/extendRoutes.d.ts
/**
 * A route node that can be modified by the user. The tree can be iterated to be traversed.
 * @example
 * ```js
 * [...node] // creates an array of all the children
 * for (const child of node) {
 *   // do something with the child node
 * }
 * ```
 *
 * @experimental
 */
declare class EditableTreeNode {
  private node;
  constructor(node: TreeNode);
  /**
   * Remove and detach the current route node from the tree. Subsequently, its children will be removed as well.
   */
  delete(): void;
  /**
   * Inserts a new route as a child of this route. This route cannot use `definePage()`. If it was meant to be included,
   * add it to the `routesFolder` option.
   *
   * @param path - path segment to insert. Note this is relative to the current route. **It shouldn't start with `/`**. If it does, it will be added to the root of the tree.
   * @param filePath - file path
   * @returns the new editable route node
   */
  insert(path: string, filePath: string): EditableTreeNode;
  /**
   * Get an editable version of the parent node if it exists.
   */
  get parent(): EditableTreeNode | undefined;
  /**
   * Return a Map of the files associated to the current route. The key of the map represents the name of the view (Vue
   * Router feature) while the value is the **resolved** file path.
   * By default, the name of the view is `default`.
   */
  get components(): Map<string, string>;
  /**
   * Alias for `route.components.get('default')`.
   */
  get component(): string | undefined;
  /**
   * Name of the route. Note that **all routes are named** but when the final `routes` array is generated, routes
   * without a `component` will not include their `name` property to avoid accidentally navigating to them and display
   * nothing.
   * @see {@link isPassThrough}
   */
  get name(): string | false;
  /**
   * Override the name of the route.
   */
  set name(name: string | undefined);
  /**
   * Whether the route is a pass-through route. A pass-through route is a route that does not have a component and is
   * used to group other routes under the same prefix `path` and/or `meta` properties.
   */
  get isPassThrough(): boolean;
  /**
   * Meta property of the route as an object. Note this property is readonly and will be serialized as JSON. It won't contain the meta properties defined with `definePage()` as it could contain expressions **but it does contain the meta properties defined with `<route>` blocks**.
   */
  get meta(): Readonly<RouteMeta>;
  /**
   * Override the meta property of the route. This will discard any other meta property defined with `<route>` blocks or
   * through other means. If you want to keep the existing meta properties, use `addToMeta`.
   * @see {@link addToMeta}
   */
  set meta(meta: RouteMeta);
  /**
   * Add meta properties to the route keeping the existing ones. The passed object will be deeply merged with the
   * existing meta object if any. Note that the meta property is later on serialized as JSON so you can't pass functions
   * or any other non-serializable value.
   */
  addToMeta(meta: Partial<RouteMeta>): void;
  /**
   * Path of the route without parent paths.
   */
  get path(): string;
  /**
   * Override the path of the route. You must ensure `params` match with the existing path.
   */
  set path(path: string);
  /**
   * Alias of the route.
   */
  get alias(): string[] | undefined;
  /**
   * Add an alias to the route.
   *
   * @param alias - Alias to add to the route
   */
  addAlias(alias: CustomRouteBlock['alias']): void;
  /**
   * Array of the route params and all of its parent's params. Note that
   * changing the params will not update the path, you need to update both.
   */
  get params(): TreePathParam[];
  /**
   * Path of the route including parent paths.
   */
  get fullPath(): string;
  /**
   * Computes an array of EditableTreeNode from the current node. Differently from iterating over the tree, this method
   * **only returns direct children**.
   */
  get children(): EditableTreeNode[];
  /**
   * DFS traversal of the tree.
   * @example
   * ```ts
   * for (const node of tree) {
   *   // ...
   * }
   * ```
   */
  traverseDFS(): Generator<EditableTreeNode, void, unknown>;
  [Symbol.iterator](): Generator<EditableTreeNode, void, unknown>;
  /**
   * BFS traversal of the tree as a generator.
   *
   * @example
   * ```ts
   * for (const node of tree) {
   *   // ...
   * }
   * ```
   */
  traverseBFS(): Generator<EditableTreeNode, void, unknown>;
}
//#endregion
//#region src/unplugin/utils/index.d.ts
/**
 * Maybe a promise maybe not
 * @internal
 */
type _Awaitable<T> = T | PromiseLike<T>;
//#endregion
//#region src/unplugin/options.d.ts
/**
 * Options for a routes folder.
 */
interface RoutesFolderOption {
  /**
   * Folder to scan files that should be used for routes. **Cannot be a glob**, use the `path`, `filePatterns`, and
   * `exclude` options to filter out files. This section will **be removed** from the resulting path.
   */
  src: string;
  /**
   * Prefix to add to the route path **as is**. Defaults to `''`. Can also be a function
   * to reuse parts of the filepath, in that case you should return a **modified version of the filepath**.
   *
   * @example
   * ```js
   * {
   *   src: 'src/pages',
   *   // this is equivalent to the default behavior
   *   path: (file) => file.slice(file.lastIndexOf('src/pages') + 'src/pages'.length
   * },
   * {
   *   src: 'src/features',
   *   // match all files (note the \ is not needed in real code)
   *   filePatterns: '*‍/pages/**\/',
   *   path: (file) => {
   *     const prefix = 'src/features'
   *     // remove the everything before src/features and removes /pages
   *     // /src/features/feature1/pages/index.vue -> feature1/index.vue
   *     return file.slice(file.lastIndexOf(prefix) + prefix.length + 1).replace('/pages', '')
   *   },
   * },
   * {
   *   src: 'src/docs',
   *   // adds a prefix with a param
   *   path: 'docs/[lang]/',
   * },
   * ```
   */
  path?: string | ((filepath: string) => string);
  /**
   * Allows to override the global `filePattern` option for this folder. It can also extend the global values by passing
   * a function that returns an array.
   */
  filePatterns?: _OverridableOption<string[], string | string[]>;
  /**
   * Allows to override the global `exclude` option for this folder. It can
   * also extend the global values by passing a function that returns an array.
   */
  exclude?: _OverridableOption<string[], string | string[]>;
  /**
   * Allows to override the global `extensions` option for this folder. It can
   * also extend the global values by passing a function that returns an array.
   * The provided extensions are removed from the final route. For example,
   * `.page.vue` allows to suffix all pages with `.page.vue` and remove it from
   * the route name.
   */
  extensions?: _OverridableOption<string[]>;
}
/**
 * Normalized options for a routes folder.
 */
interface RoutesFolderOptionResolved extends RoutesFolderOption {
  path: string | ((filepath: string) => string);
  /**
   * Final glob pattern to match files in the folder.
   */
  pattern: string[];
  filePatterns: string[];
  exclude: string[];
  extensions: string[];
}
/**
 * An option that can be overridden by providing a function that receives the
 * existing value and returns a new one.
 *
 * @internal
 */
type _OverridableOption<T, AllowedTypes = T> = AllowedTypes | ((existing: T) => T);
/**
 * Resolves an overridable option by calling the function with the existing
 * value if it's a function, otherwise returning the passed `value`. If `value`
 * is undefined, it returns the `defaultValue` instead.
 *
 * @param defaultValue default value for the option
 * @param value and overridable option
 *
 * @internal
 */
declare function resolveOverridableOption<T>(defaultValue: T, value?: _OverridableOption<T, T>): T;
/**
 * @internal
 */
type _RoutesFolder = string | RoutesFolderOption;
/**
 * Type for the {@link Options.routesFolder} option.
 */
type RoutesFolder = _RoutesFolder[] | _RoutesFolder;
/**
 * vue-router plugin options.
 */
interface Options {
  /**
   * Extensions of files to be considered as pages. Cannot be empty. This allows to strip a
   * bigger part of the filename e.g. `index.page.vue` -> `index` if an extension of `.page.vue` is provided.
   * @default `['.vue']`
   */
  extensions?: string[];
  /**
   * Folder(s) to scan for files and generate routes. Can also be an array if you want to add multiple
   * folders, or an object if you want to define a route prefix. Supports glob patterns but must be a folder, use
   * `extensions` and `exclude` to filter files.
   *
   * @default `"src/pages"`
   */
  routesFolder?: RoutesFolder;
  /**
   * Array of `picomatch` globs to ignore. Note the globs are relative to the cwd, so avoid writing
   * something like `['ignored']` to match folders named that way, instead provide a path similar to the `routesFolder`:
   * `['src/pages/ignored/**']` or use `['**​/ignored']` to match every folder named `ignored`.
   * @default `[]`
   */
  exclude?: string[] | string;
  /**
   * Pattern to match files in the `routesFolder`. Defaults to `*\/*` plus a
   * combination of all the possible extensions, e.g. `*\/*.{vue,md}` if
   * `extensions` is set to `['.vue', '.md']`. This is relative to the {@link
   * RoutesFolderOption['src']} and
   *
   * @default `['*\/*']`
   */
  filePatterns?: string[] | string;
  /**
   * Method to generate the name of a route. It's recommended to keep the default value to guarantee a consistent,
   * unique, and predictable naming.
   */
  getRouteName?: (node: TreeNode) => string;
  /**
   * Allows extending a route by modifying its node, adding children, or even deleting it. This will be invoked once for
   * each route.
   *
   * @param route - {@link EditableTreeNode} of the route to extend
   */
  extendRoute?: (route: EditableTreeNode) => _Awaitable<void>;
  /**
   * Allows to do some changes before writing the files. This will be invoked **every time** the files need to be written.
   *
   * @param rootRoute - {@link EditableTreeNode} of the root route
   */
  beforeWriteFiles?: (rootRoute: EditableTreeNode) => _Awaitable<void>;
  /**
   * Defines how page components should be imported. Defaults to dynamic imports to enable lazy loading of pages.
   * @default `'async'`
   */
  importMode?: 'sync' | 'async' | ((filepath: string) => 'sync' | 'async');
  /**
   * Root of the project. All paths are resolved relatively to this one.
   * @default `process.cwd()`
   */
  root?: string;
  /**
   * Language for `<route>` blocks in SFC files.
   * @default `'json5'`
   */
  routeBlockLang?: 'yaml' | 'yml' | 'json5' | 'json';
  /**
   * Should we generate d.ts files or ont. Defaults to `true` if `typescript` is installed. Can be set to a string of
   * the filepath to write the d.ts files to. By default it will generate a file named `typed-router.d.ts`.
   * @default `true`
   */
  dts?: boolean | string;
  /**
   * Allows inspection by vite-plugin-inspect by not adding the leading `\0` to the id of virtual modules.
   * @internal
   */
  _inspect?: boolean;
  /**
   * Activates debug logs.
   */
  logs?: boolean;
  /**
   * @inheritDoc ParseSegmentOptions
   */
  pathParser?: ParseSegmentOptions;
  /**
   * Whether to watch the files for changes.
   *
   * Defaults to `true` unless the `CI` environment variable is set.
   *
   * @default `!process.env.CI`
   */
  watch?: boolean;
  /**
   * Experimental options. **Warning**: these can change or be removed at any time, even it patch releases. Keep an eye
   * on the Changelog.
   */
  experimental?: {
    /**
     * (Vite only). File paths or globs where loaders are exported. This will be used to filter out imported loaders and
     * automatically re export them in page components. You can for example set this to `'src/loaders/**\/*'` (without
     * the backslash) to automatically re export any imported variable from files in the `src/loaders` folder within a
     * page component.
     */
    autoExportsDataLoaders?: string | string[];
    /**
     * Enable experimental support for the new custom resolvers and allows
     * defining custom param matchers.
     */
    paramParsers?: boolean | ParamParsersOptions;
  };
}
/**
 * Options for experimental param parsers.
 */
interface ParamParsersOptions {
  /**
   * Folder(s) to scan for param matchers. Set to an empty array to disable the feature.
   *
   * @default `['src/params']`
   */
  dir?: string | string[];
  /**
   * Glob pattern(s) of files to include when scanning param parser folders. Only flat
   * matches are supported (no nested files).
   *
   * @default `['*.ts']`
   */
  include?: string | string[];
  /**
   * Glob pattern(s) of files to ignore when scanning param parser folders. Useful to skip
   * test files that live next to their implementations.
   *
   * @default `['*.test.{ts,js}', '*.spec.{ts,js}']`
   */
  exclude?: string | string[];
}
/**
 * Default options for experimental param parsers.
 */
declare const DEFAULT_PARAM_PARSERS_OPTIONS: {
  dir: string[];
  include: string[];
  exclude: string[];
};
/**
 * Default plugin options.
 */
declare const DEFAULT_OPTIONS: {
  extensions: string[];
  exclude: never[];
  routesFolder: string;
  filePatterns: string[];
  routeBlockLang: "json5";
  getRouteName: typeof getFileBasedRouteName;
  importMode: "async";
  root: string;
  dts: boolean;
  logs: false;
  _inspect: false;
  pathParser: {
    dotNesting: true;
  };
  watch: boolean;
  experimental: {};
};
/**
 * Expected server context provided to hooks.
 *
 * @internal
 */
interface ServerContext {
  /**
   * Invalidates a module by its id.
   * @param module - module id to invalidate
   *
   * @returns A promise that resolves when the module is invalidated, or `false` if the module was not found.
   */
  invalidate: (module: string) => Promise<void> | false;
  /**
   * Invalidates all modules associated with a page file.
   * @param filepath - file path of the page to invalidate
   *
   * @returns A promise that resolves when the page is invalidated, or `false` if no modules were found for the page.
   */
  invalidatePage: (filepath: string) => Promise<void> | false;
  /**
   * Triggers HMR for the routes module.
   */
  updateRoutes: () => Promise<void>;
  /**
   * Triggers a full page reload.
   */
  reload: () => void;
}
/**
 * Normalize user options with defaults and resolved paths.
 *
 * @param options - user provided options
 * @returns normalized options
 */
declare function resolveOptions(options: Options): {
  experimental: {
    autoExportsDataLoaders: string[] | undefined;
    paramParsers: {
      dir: string[];
      include: string[];
      exclude: string[];
    } | undefined;
  };
  routesFolder: {
    src: string;
    filePatterns: string[] | ((existing: string[]) => string[]) | undefined;
    exclude: string[] | ((existing: string[]) => string[]) | undefined;
    /**
     * Prefix to add to the route path **as is**. Defaults to `''`. Can also be a function
     * to reuse parts of the filepath, in that case you should return a **modified version of the filepath**.
     *
     * @example
     * ```js
     * {
     *   src: 'src/pages',
     *   // this is equivalent to the default behavior
     *   path: (file) => file.slice(file.lastIndexOf('src/pages') + 'src/pages'.length
     * },
     * {
     *   src: 'src/features',
     *   // match all files (note the \ is not needed in real code)
     *   filePatterns: '*‍/pages/**\/',
     *   path: (file) => {
     *     const prefix = 'src/features'
     *     // remove the everything before src/features and removes /pages
     *     // /src/features/feature1/pages/index.vue -> feature1/index.vue
     *     return file.slice(file.lastIndexOf(prefix) + prefix.length + 1).replace('/pages', '')
     *   },
     * },
     * {
     *   src: 'src/docs',
     *   // adds a prefix with a param
     *   path: 'docs/[lang]/',
     * },
     * ```
     */
    path?: string | ((filepath: string) => string);
    /**
     * Allows to override the global `extensions` option for this folder. It can
     * also extend the global values by passing a function that returns an array.
     * The provided extensions are removed from the final route. For example,
     * `.page.vue` allows to suffix all pages with `.page.vue` and remove it from
     * the route name.
     */
    extensions?: _OverridableOption<string[]>;
  }[];
  filePatterns: string[];
  exclude: string[];
  extensions: string[];
  getRouteName: typeof getFileBasedRouteName;
  /**
   * Allows extending a route by modifying its node, adding children, or even deleting it. This will be invoked once for
   * each route.
   *
   * @param route - {@link EditableTreeNode} of the route to extend
   */
  extendRoute?: (route: EditableTreeNode) => _Awaitable<void>;
  /**
   * Allows to do some changes before writing the files. This will be invoked **every time** the files need to be written.
   *
   * @param rootRoute - {@link EditableTreeNode} of the root route
   */
  beforeWriteFiles?: (rootRoute: EditableTreeNode) => _Awaitable<void>;
  importMode: "sync" | "async" | ((filepath: string) => "sync" | "async");
  root: string;
  routeBlockLang: "yaml" | "yml" | "json5" | "json";
  dts: boolean | string;
  _inspect: boolean;
  logs: boolean;
  pathParser: ParseSegmentOptions;
  watch: boolean;
};
/**
 * @internal
 */
type ResolvedOptions = ReturnType<typeof resolveOptions>;
/**
 * Merge all the possible extensions as an array of unique values
 * @param options - user provided options
 * @internal
 */
declare function mergeAllExtensions(options: ResolvedOptions): string[];
//#endregion
export { createTreeNodeValue as C, TreeNodeValueStatic as S, getPascalCaseRouteName as _, ResolvedOptions as a, TreeNodeValueGroup as b, RoutesFolderOptionResolved as c, _RoutesFolder as d, mergeAllExtensions as f, getFileBasedRouteName as g, EditableTreeNode as h, ParamParsersOptions as i, ServerContext as l, resolveOverridableOption as m, DEFAULT_PARAM_PARSERS_OPTIONS as n, RoutesFolder as o, resolveOptions as p, Options as r, RoutesFolderOption as s, DEFAULT_OPTIONS as t, _OverridableOption as u, TreeNode as v, TreeNodeValueParam as x, TreeNodeValue as y };