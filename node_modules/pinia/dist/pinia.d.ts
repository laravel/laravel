/*!
 * pinia v4.0.2
 * (c) 2026 Eduardo San Martin Morote
 * @license MIT
 */
import { App, ComputedRef, DebuggerEvent, EffectScope, InjectionKey, Ref, ToRef, ToRefs, UnwrapRef, WatchOptions, WritableComputedRef } from "vue";
//#region src/types.d.ts
/**
 * Generic state of a Store
 */
type StateTree = Record<PropertyKey, any>;
/**
 * Recursive `Partial<T>`. Used by {@link Store['$patch']}.
 *
 * For internal use **only**
 */
type _DeepPartial<T> = { [K in keyof T]?: _DeepPartial<T[K]>; };
/**
 * Possible types for SubscriptionCallback
 */
declare enum MutationType {
  /**
   * Direct mutation of the state:
   *
   * - `store.name = 'new name'`
   * - `store.$state.name = 'new name'`
   * - `store.list.push('new item')`
   */
  direct = "direct",
  /**
   * Mutated the state with `$patch` and an object
   *
   * - `store.$patch({ name: 'newName' })`
   */
  patchObject = "patch object",
  /**
   * Mutated the state with `$patch` and a function
   *
   * - `store.$patch(state => state.name = 'newName')`
   */
  patchFunction = "patch function"
}
/**
 * Base type for the context passed to a subscription callback. Internal type.
 */
interface _SubscriptionCallbackMutationBase {
  /**
   * Type of the mutation.
   */
  type: MutationType;
  /**
   * `id` of the store doing the mutation.
   */
  storeId: string;
  /**
   * 🔴 DEV ONLY, DO NOT use for production code. Different mutation calls. Comes from
   * https://vuejs.org/guide/extras/reactivity-in-depth.html#reactivity-debugging and allows to track mutations in
   * devtools and plugins **during development only**.
   */
  events?: DebuggerEvent[] | DebuggerEvent;
}
/**
 * Context passed to a subscription callback when directly mutating the state of
 * a store with `store.someState = newValue` or `store.$state.someState =
 * newValue`.
 */
interface SubscriptionCallbackMutationDirect extends _SubscriptionCallbackMutationBase {
  type: MutationType.direct;
  events: DebuggerEvent;
}
/**
 * Context passed to a subscription callback when `store.$patch()` is called
 * with an object.
 */
interface SubscriptionCallbackMutationPatchObject<S> extends _SubscriptionCallbackMutationBase {
  type: MutationType.patchObject;
  events: DebuggerEvent[];
  /**
   * Object passed to `store.$patch()`.
   */
  payload: _DeepPartial<UnwrapRef<S>>;
}
/**
 * Context passed to a subscription callback when `store.$patch()` is called
 * with a function.
 */
interface SubscriptionCallbackMutationPatchFunction extends _SubscriptionCallbackMutationBase {
  type: MutationType.patchFunction;
  events: DebuggerEvent[];
}
/**
 * Context object passed to a subscription callback.
 */
type SubscriptionCallbackMutation<S> = SubscriptionCallbackMutationDirect | SubscriptionCallbackMutationPatchObject<S> | SubscriptionCallbackMutationPatchFunction;
/**
 * Callback of a subscription
 */
type SubscriptionCallback<S> = (
/**
 * Object with information relative to the store mutation that triggered the
 * subscription.
 */
mutation: SubscriptionCallbackMutation<S>,
/**
 * State of the store when the subscription is triggered. Same as
 * `store.$state`.
 */
state: UnwrapRef<S>) => void;
/**
 * Actual type for {@link StoreOnActionListenerContext}. Exists for refactoring
 * purposes. For internal use only.
 * For internal use **only**
 */
interface _StoreOnActionListenerContext<Store, ActionName extends string, A> {
  /**
   * Name of the action
   */
  name: ActionName;
  /**
   * Store that is invoking the action
   */
  store: Store;
  /**
   * Parameters passed to the action
   */
  args: A extends Record<ActionName, _Method> ? Parameters<A[ActionName]> : unknown[];
  /**
   * Sets up a hook once the action is finished. It receives the return value
   * of the action, if it's a Promise, it will be unwrapped.
   */
  after: (callback: A extends Record<ActionName, _Method> ? (resolvedReturn: Awaited<ReturnType<A[ActionName]>>) => void : () => void) => void;
  /**
   * Sets up a hook if the action fails. Return `false` to catch the error and
   * stop it from propagating.
   */
  onError: (callback: (error: unknown) => void) => void;
}
/**
 * Context object passed to callbacks of `store.$onAction(context => {})`
 * TODO: should have only the Id, the Store and Actions to generate the proper object
 */
type StoreOnActionListenerContext<Id extends string, S extends StateTree, G, A> = _ActionsTree extends A ? _StoreOnActionListenerContext<StoreGeneric, string, _ActionsTree> : { [Name in keyof A]: Name extends string ? _StoreOnActionListenerContext<Store<Id, S, G, A>, Name, A> : never; }[keyof A];
/**
 * Argument of `store.$onAction()`
 */
type StoreOnActionListener<Id extends string, S extends StateTree, G, A> = (context: StoreOnActionListenerContext<Id, S, G, {} extends A ? _ActionsTree : A>) => void;
/**
 * Properties of a store.
 */
interface StoreProperties<Id extends string> {
  /**
   * Unique identifier of the store
   */
  $id: Id;
  /**
   * Private property defining the pinia the store is attached to.
   *
   * @internal
   */
  _p: Pinia;
  /**
   * Used by devtools plugin to retrieve getters. Removed in production.
   *
   * @internal
   */
  _getters?: string[];
  /**
   * Used (and added) by devtools plugin to detect Setup vs Options API usage.
   *
   * @internal
   */
  _isOptionsAPI?: boolean;
  /**
   * Used by devtools plugin to retrieve properties added with plugins. Removed
   * in production. Can be used by the user to add property keys of the store
   * that should be displayed in devtools.
   */
  _customProperties: Set<string>;
  /**
   * Handles a HMR replacement of this store. Dev Only.
   *
   * @internal
   */
  _hotUpdate(useStore: StoreGeneric): void;
  /**
   * Allows pausing some of the watching mechanisms while the store is being
   * patched with a newer version.
   *
   * @internal
   */
  _hotUpdating: boolean;
  /**
   * Payload of the hmr update. Dev only.
   *
   * @internal
   */
  _hmrPayload: {
    state: string[];
    hotState: Ref<StateTree>;
    actions: _ActionsTree;
    getters: _ActionsTree;
  };
}
/**
 * Base store with state and functions. Should not be used directly.
 */
interface _StoreWithState<Id extends string, S extends StateTree, G, A> extends StoreProperties<Id> {
  /**
   * State of the Store. Setting it will internally call `$patch()` to update the state.
   */
  $state: UnwrapRef<S> & PiniaCustomStateProperties<S>;
  /**
   * Applies a state patch to current state. Allows passing nested values
   *
   * @param partialState - patch to apply to the state
   */
  $patch(partialState: _DeepPartial<UnwrapRef<S>>): void;
  /**
   * Group multiple changes into one function. Useful when mutating objects like
   * Sets or arrays and applying an object patch isn't practical, e.g. appending
   * to an array. The function passed to `$patch()` **must be synchronous**.
   *
   * @param stateMutator - function that mutates `state`, cannot be asynchronous
   */
  $patch<F extends (state: UnwrapRef<S>) => any>(stateMutator: ReturnType<F> extends Promise<any> ? never : F): void;
  /**
   * Resets the store to its initial state by building a new state object.
   */
  $reset(): void;
  /**
   * Setups a callback to be called whenever the state changes. It also returns a function to remove the callback. Note
   * that when calling `store.$subscribe()` inside of a component, it will be automatically cleaned up when the
   * component gets unmounted unless `detached` is set to true.
   *
   * @param callback - callback passed to the watcher
   * @param options - `watch` options + `detached` to detach the subscription from the context (usually a component)
   * this is called from. Note that the `flush` option does not affect calls to `store.$patch()`.
   * @returns function that removes the watcher
   */
  $subscribe(callback: SubscriptionCallback<S>, options?: {
    detached?: boolean;
  } & WatchOptions): () => void;
  /**
   * Setups a callback to be called every time an action is about to get
   * invoked. The callback receives an object with all the relevant information
   * of the invoked action:
   * - `store`: the store it is invoked on
   * - `name`: The name of the action
   * - `args`: The parameters passed to the action
   *
   * On top of these, it receives two functions that allow setting up a callback
   * once the action finishes or when it fails.
   *
   * It also returns a function to remove the callback. Note that when calling
   * `store.$onAction()` inside of a component, it will be automatically cleaned
   * up when the component gets unmounted unless `detached` is set to true.
   *
   * @example
   *
   *```js
   *store.$onAction(({ after, onError }) => {
   *  // Here you could share variables between all of the hooks as well as
   *  // setting up watchers and clean them up
   *  after((resolvedValue) => {
   *    // can be used to cleanup side effects
   * .  // `resolvedValue` is the value returned by the action, if it's a
   * .  // Promise, it will be the resolved value instead of the Promise
   *  })
   *  onError((error) => {
   *    // can be used to pass up errors
   *  })
   *})
   *```
   *
   * @param callback - callback called before every action
   * @param detached - detach the subscription from the context this is called from
   * @returns function that removes the watcher
   */
  $onAction(callback: StoreOnActionListener<Id, S, G, A>, detached?: boolean): () => void;
  /**
   * Stops the associated effect scope of the store and remove it from the store
   * registry. Plugins can override this method to cleanup any added effects.
   * e.g. devtools plugin stops displaying disposed stores from devtools.
   * Note this doesn't delete the state of the store, you have to do it manually with
   * `delete pinia.state.value[store.$id]` if you want to. If you don't and the
   * store is used again, it will reuse the previous state.
   */
  $dispose(): void;
}
/**
 * Generic type for a function that can infer arguments and return type
 *
 * For internal use **only**
 */
type _Method = (...args: any[]) => any;
/**
 * Store augmented for actions. For internal usage only.
 * For internal use **only**
 */
type _StoreWithActions<A> = { [k in keyof A]: A[k] extends ((...args: infer P) => infer R) ? (...args: P) => R : never; };
/**
 * Store augmented with getters. For internal usage only.
 * For internal use **only**
 */
type _StoreWithGetters<G> = _StoreWithGetters_Readonly<G> & _StoreWithGetters_Writable<G>;
/**
 * Store augmented with readonly getters. For internal usage **only**.
 */
type _StoreWithGetters_Readonly<G> = { readonly [K in keyof G as G[K] extends ((...args: any[]) => any) ? K : ComputedRef extends G[K] ? K : never]: G[K] extends ((...args: any[]) => infer R) ? R : UnwrapRef<G[K]>; };
/**
 * Store augmented with writable getters. For internal usage **only**.
 */
type _StoreWithGetters_Writable<G> = { [K in keyof G as G[K] extends WritableComputedRef<any> ? K : never]: G[K] extends Readonly<WritableComputedRef<infer R>> ? R : never; };
/**
 * Store type to build a store.
 */
type Store<Id extends string = string, S extends StateTree = {}, G = {}, A = {}> = _StoreWithState<Id, S, G, A> & UnwrapRef<S> & _StoreWithGetters<G> & (_ActionsTree extends A ? {} : A) & PiniaCustomProperties<Id, S, G, A> & PiniaCustomStateProperties<S>;
/**
 * Generic and type-unsafe version of Store. Doesn't fail on access with
 * strings, making it much easier to write generic functions that do not care
 * about the kind of store that is passed.
 */
type StoreGeneric = Store<string, StateTree, _GettersTree<StateTree>, _ActionsTree>;
/**
 * Return type of `defineStore()`. Function that allows instantiating a store.
 */
interface StoreDefinition<Id extends string = string, S extends StateTree = StateTree, G = _GettersTree<S>, A = _ActionsTree> {
  /**
   * Returns a store, creates it if necessary.
   *
   * @param pinia - Pinia instance to retrieve the store
   * @param hot - dev only hot module replacement
   */
  (pinia?: Pinia | null | undefined, hot?: StoreGeneric): Store<Id, S, G, A>;
  /**
   * Id of the store. Used by map helpers.
   */
  $id: Id;
  /**
   * Dev only pinia for HMR.
   *
   * @internal
   */
  _pinia?: Pinia;
}
/**
 * Interface to be extended by the user when they add properties through plugins.
 */
interface PiniaCustomProperties<Id extends string = string, S extends StateTree = StateTree, G = _GettersTree<S>, A = _ActionsTree> {}
/**
 * Properties that are added to every `store.$state` by `pinia.use()`.
 */
interface PiniaCustomStateProperties<S extends StateTree = StateTree> {}
/**
 * Type of an object of Getters that infers the argument. For internal usage only.
 * For internal use **only**
 */
type _GettersTree<S extends StateTree> = Record<string, ((state: UnwrapRef<S> & UnwrapRef<PiniaCustomStateProperties<S>>) => any) | (() => any)>;
/**
 * Type of an object of Actions. For internal usage only.
 * For internal use **only**
 */
type _ActionsTree = Record<string, _Method>;
/**
 * Type that enables refactoring through IDE.
 * For internal use **only**
 */
type _ExtractStateFromSetupStore_Keys<SS> = keyof { [K in keyof SS as SS[K] extends _Method | ComputedRef ? never : K]: any; };
/**
 * Type that enables refactoring through IDE.
 * For internal use **only**
 */
type _ExtractActionsFromSetupStore_Keys<SS> = keyof { [K in keyof SS as SS[K] extends _Method ? K : never]: any; };
/**
 * Type that enables refactoring through IDE.
 * For internal use **only**
 */
type _ExtractGettersFromSetupStore_Keys<SS> = keyof { [K in keyof SS as SS[K] extends ComputedRef ? K : never]: any; };
/**
 * Type that enables refactoring through IDE.
 * For internal use **only**
 */
type _UnwrapAll<SS> = { [K in keyof SS]: UnwrapRef<SS[K]>; };
/**
 * For internal use **only**
 */
type _ExtractStateFromSetupStore<SS> = SS extends undefined | void ? {} : Pick<SS, _ExtractStateFromSetupStore_Keys<SS>>;
/**
 * For internal use **only**
 */
type _ExtractActionsFromSetupStore<SS> = SS extends undefined | void ? {} : Pick<SS, _ExtractActionsFromSetupStore_Keys<SS>>;
/**
 * For internal use **only**
 */
type _ExtractGettersFromSetupStore<SS> = SS extends undefined | void ? {} : Pick<SS, _ExtractGettersFromSetupStore_Keys<SS>>;
/**
 * Options passed to `defineStore()` that are common between option and setup
 * stores. Extend this interface if you want to add custom options to both kinds
 * of stores.
 */
interface DefineStoreOptionsBase<S extends StateTree, Store> {}
/**
 * Options parameter of `defineStore()` for option stores. Can be extended to
 * augment stores with the plugin API. @see {@link DefineStoreOptionsBase}.
 */
interface DefineStoreOptions<Id extends string, S extends StateTree, G, A> extends DefineStoreOptionsBase<S, Store<Id, S, G, A>> {
  /**
   * Unique string key to identify the store across the application.
   */
  id: Id;
  /**
   * Function to create a fresh state. **Must be an arrow function** to ensure
   * correct typings!
   */
  state?: () => S;
  /**
   * Optional object of getters.
   */
  getters?: G & ThisType<UnwrapRef<S> & _StoreWithGetters<G> & PiniaCustomProperties> & _GettersTree<S>;
  /**
   * Optional object of actions.
   */
  actions?: A & ThisType<A & UnwrapRef<S> & _StoreWithState<Id, S, G, A> & _StoreWithGetters<G> & PiniaCustomProperties>;
  /**
   * Allows hydrating the store during SSR when complex state (like client side only refs) are used in the store
   * definition and copying the value from `pinia.state` isn't enough.
   *
   * @example
   * If in your `state`, you use any `customRef`s, any `computed`s, or any `ref`s that have a different value on
   * Server and Client, you need to manually hydrate them. e.g., a custom ref that is stored in the local
   * storage:
   *
   * ```ts
   * const useStore = defineStore('main', {
   *   state: () => ({
   *     n: useLocalStorage('key', 0)
   *   }),
   *   hydrate(storeState, initialState) {
   *     // @ts-expect-error: https://github.com/microsoft/TypeScript/issues/43826
   *     storeState.n = useLocalStorage('key', 0)
   *   }
   * })
   * ```
   *
   * @param storeState - the current state in the store
   * @param initialState - initialState
   */
  hydrate?(storeState: UnwrapRef<S>, initialState: UnwrapRef<S>): void;
}
/**
 * Options parameter of `defineStore()` for setup stores. Can be extended to
 * augment stores with the plugin API. @see {@link DefineStoreOptionsBase}.
 */
interface DefineSetupStoreOptions<Id extends string, S extends StateTree, G, A> extends DefineStoreOptionsBase<S, Store<Id, S, G, A>> {
  /**
   * Extracted actions. Added by useStore(). SHOULD NOT be added by the user when
   * creating the store. Can be used in plugins to get the list of actions in a
   * store defined with a setup function. Note this is always defined
   */
  actions?: A;
}
/**
 * Available `options` when creating a pinia plugin.
 */
interface DefineStoreOptionsInPlugin<Id extends string, S extends StateTree, G, A> extends Omit<DefineStoreOptions<Id, S, G, A>, 'id' | 'actions'> {
  /**
   * Extracted object of actions. Added by useStore() when the store is built
   * using the setup API, otherwise uses the one passed to `defineStore()`.
   * Defaults to an empty object if no actions are defined.
   */
  actions: A;
}
//#endregion
//#region src/rootStore.d.ts
/**
 * Sets or unsets the active pinia. Used in SSR and internally when calling
 * actions and getters
 *
 * @param pinia - Pinia instance
 */
declare const setActivePinia: _SetActivePinia;
interface _SetActivePinia {
  (pinia: Pinia): Pinia;
  (pinia: undefined): undefined;
  (pinia: Pinia | undefined): Pinia | undefined;
}
/**
 * Get the currently active pinia if there is any.
 */
declare const getActivePinia: () => Pinia | undefined;
/**
 * Every application must own its own pinia to be able to create stores
 */
interface Pinia {
  install: (app: App) => void;
  /**
   * root state
   */
  state: Ref<Record<string, StateTree>>;
  /**
   * Adds a store plugin to extend every store
   *
   * @param plugin - store plugin to add
   */
  use(plugin: PiniaPlugin): Pinia;
  /**
   * Installed store plugins
   *
   * @internal
   */
  _p: PiniaPlugin[];
  /**
   * App linked to this Pinia instance
   *
   * @internal
   */
  _a: App;
  /**
   * Effect scope the pinia is attached to
   *
   * @internal
   */
  _e: EffectScope;
  /**
   * Registry of stores used by this pinia.
   *
   * @internal
   */
  _s: Map<string, StoreGeneric>;
  /**
   * Added by `createTestingPinia()` to bypass `useStore(pinia)`.
   *
   * @internal
   */
  _testing?: boolean;
}
/**
 * Symbol used to provide/inject the pinia instance in the app. Used internally
 * and exposed for testing purposes and edge cases like storybook. Could break
 * in a minor, **USE AT YOUR OWN RISK**.
 *
 * For context, see:
 * - https://github.com/vuejs/pinia/issues/870
 * - https://github.com/vuejs/pinia/pull/2973
 *
 * @internal
 */
declare const piniaSymbol: InjectionKey<Pinia>;
/**
 * Context argument passed to Pinia plugins.
 */
interface PiniaPluginContext<Id extends string = string, S extends StateTree = StateTree, G = _GettersTree<S>, A = _ActionsTree> {
  /**
   * pinia instance.
   */
  pinia: Pinia;
  /**
   * Current app created with `Vue.createApp()`.
   */
  app: App;
  /**
   * Current store being extended.
   */
  store: Store<Id, S, G, A>;
  /**
   * Initial options defining the store when calling `defineStore()`.
   */
  options: DefineStoreOptionsInPlugin<Id, S, G, A>;
}
/**
 * Plugin to extend every store.
 */
interface PiniaPlugin {
  /**
   * Plugin to extend every store. Returns an object to extend the store or
   * nothing.
   *
   * @param context - Context
   */
  (context: PiniaPluginContext): Partial<PiniaCustomProperties & PiniaCustomStateProperties> | void;
}
//#endregion
//#region src/createPinia.d.ts
/**
 * Creates a Pinia instance to be used by the application
 */
declare function createPinia(): Pinia;
/**
 * Dispose a Pinia instance by stopping its effectScope and removing the state, plugins and stores. This is mostly
 * useful in tests, with both a testing pinia or a regular pinia and in applications that use multiple pinia instances.
 * Once disposed, the pinia instance cannot be used anymore.
 *
 * @param pinia - pinia instance
 */
declare function disposePinia(pinia: Pinia): void;
//#endregion
//#region src/store.d.ts
/**
 * Tells Pinia to skip the hydration process of a given object. This is useful in setup stores (only) when you return a
 * stateful object in the store but it isn't really state. e.g. returning a router instance in a setup store.
 *
 * @param obj - target object
 * @returns obj
 */
declare function skipHydrate<T = any>(obj: T): T;
/**
 * Returns whether a value should be hydrated
 *
 * @param obj - target variable
 * @returns true if `obj` should be hydrated
 */
declare function shouldHydrate(obj: any): boolean;
/**
 * Extract the actions of a store type. Works with both a Setup Store or an
 * Options Store.
 */
type StoreActions<SS> = SS extends Store<string, StateTree, _GettersTree<StateTree>, infer A> ? A : _ExtractActionsFromSetupStore<SS>;
/**
 * Extract the getters of a store type. Works with both a Setup Store or an
 * Options Store.
 */
type StoreGetters<SS> = SS extends Store<string, StateTree, infer G, _ActionsTree> ? _StoreWithGetters<G> : _ExtractGettersFromSetupStore<SS>;
/**
 * Extract the state of a store type. Works with both a Setup Store or an
 * Options Store. Note this unwraps refs.
 */
type StoreState<SS> = SS extends Store<string, infer S, _GettersTree<StateTree>, _ActionsTree> ? UnwrapRef<S> : _ExtractStateFromSetupStore<SS>;
interface SetupStoreHelpers {
  /**
   * Helper that wraps function so it can be tracked with $onAction when the
   * action is called **within the store**. This helper is rarely needed in
   * applications. It's intended for advanced use cases like Pinia Colada.
   *
   * @param fn - action to wrap
   * @param name - name of the action. Will be picked up by the store at creation
   */
  action: <Fn extends _Method>(fn: Fn, name?: string) => Fn;
}
/**
 * Creates a `useStore` function that retrieves the store instance
 *
 * @param id - id of the store (must be unique)
 * @param options - options to define the store
 */
declare function defineStore<Id extends string, S extends StateTree = {}, G extends _GettersTree<S> = {}, A = {}>(id: Id, options: Omit<DefineStoreOptions<Id, S, G, A>, 'id'>): StoreDefinition<Id, S, G, A>;
/**
 * Creates a `useStore` function that retrieves the store instance
 *
 * @param id - id of the store (must be unique)
 * @param storeSetup - function that defines the store
 * @param options - extra options
 */
declare function defineStore<Id extends string, SS>(id: Id, storeSetup: (helpers: SetupStoreHelpers) => SS, options?: DefineSetupStoreOptions<Id, _ExtractStateFromSetupStore<SS>, _ExtractGettersFromSetupStore<SS>, _ExtractActionsFromSetupStore<SS>>): SetupStoreDefinition<Id, SS>;
/**
 * Return type of `defineStore()` with a setup function.
 * - `Id` is a string literal of the store's name
 * - `SS` is the return type of the setup function
 * @see {@link StoreDefinition}
 */
interface SetupStoreDefinition<Id extends string, SS> extends StoreDefinition<Id, _ExtractStateFromSetupStore<SS>, _ExtractGettersFromSetupStore<SS>, _ExtractActionsFromSetupStore<SS>> {}
//#endregion
//#region src/mapHelpers.d.ts
/**
 * Interface to allow customizing map helpers. Extend this interface with the
 * following properties:
 *
 * - `suffix`: string. Affects the suffix of `mapStores()`, defaults to `Store`.
 */
interface MapStoresCustomization {}
/**
 * For internal use **only**.
 */
type _StoreObject<S> = S extends StoreDefinition<infer Ids, infer State, infer Getters, infer Actions> ? { [Id in `${Ids}${MapStoresCustomization extends Record<'suffix', infer Suffix> ? Suffix : 'Store'}`]: () => Store<Id extends `${infer RealId}${MapStoresCustomization extends Record<'suffix', infer Suffix> ? Suffix : 'Store'}` ? RealId : string, State, Getters, Actions>; } : {};
/**
 * For internal use **only**.
 */
type _Spread<A extends readonly any[]> = A extends [infer L, ...infer R] ? _StoreObject<L> & _Spread<R> : unknown;
/**
 * Changes the suffix added by `mapStores()`. Can be set to an empty string.
 * Defaults to `"Store"`. Make sure to extend the MapStoresCustomization
 * interface if you are using TypeScript.
 *
 * @param suffix - new suffix
 */
declare function setMapStoreSuffix(suffix: MapStoresCustomization extends Record<'suffix', infer Suffix> ? Suffix : string): void;
/**
 * Allows using stores without the composition API (`setup()`) by generating an
 * object to be spread in the `computed` field of a component. It accepts a list
 * of store definitions.
 *
 * @example
 * ```js
 * export default {
 *   computed: {
 *     // other computed properties
 *     ...mapStores(useUserStore, useCartStore)
 *   },
 *
 *   created() {
 *     this.userStore // store with id "user"
 *     this.cartStore // store with id "cart"
 *   }
 * }
 * ```
 *
 * @param stores - list of stores to map to an object
 */
declare function mapStores<Stores extends any[]>(...stores: [...Stores]): _Spread<Stores>;
/**
 * For internal use **only**
 */
type _MapStateReturn<S extends StateTree, G extends _GettersTree<S> | {
  [key: string]: ComputedRef;
}, Keys extends keyof S | keyof G = keyof S | keyof G> = { [key in Keys]: key extends keyof Store<string, S, G, {}> ? () => Store<string, S, G, {}>[key] : never; };
/**
 * For internal use **only**
 */
type _MapStateObjectReturn<Id extends string, S extends StateTree, G extends _GettersTree<S> | {
  [key: string]: ComputedRef;
}, A, T extends Record<string, keyof S | keyof G | ((store: Store<Id, S, G, A>) => any)> = {}> = { [key in keyof T]: () => T[key] extends ((store: any) => infer R) ? R : T[key] extends keyof Store<Id, S, G, A> ? Store<Id, S, G, A>[T[key]] : never; };
/**
 * Allows using state and getters from one store without using the composition
 * API (`setup()`) by generating an object to be spread in the `computed` field
 * of a component. The values of the object are the state properties/getters
 * while the keys are the names of the resulting computed properties.
 * Optionally, you can also pass a custom function that will receive the store
 * as its first argument. Note that while it has access to the component
 * instance via `this`, it won't be typed.
 *
 * @example
 * ```js
 * export default {
 *   computed: {
 *     // other computed properties
 *     // useCounterStore has a state property named `count` and a getter `double`
 *     ...mapState(useCounterStore, {
 *       n: 'count',
 *       triple: store => store.n * 3,
 *       // note we can't use an arrow function if we want to use `this`
 *       custom(store) {
 *         return this.someComponentValue + store.n
 *       },
 *       doubleN: 'double'
 *     })
 *   },
 *
 *   created() {
 *     this.n // 2
 *     this.doubleN // 4
 *   }
 * }
 * ```
 *
 * @param useStore - store to map from
 * @param keyMapper - object of state properties or getters
 */
declare function mapState<Id extends string, S extends StateTree, G extends _GettersTree<S> | {
  [key: string]: ComputedRef;
}, A, KeyMapper extends Record<string, keyof S | keyof G | ((store: Store<Id, S, G, A>) => any)>>(useStore: StoreDefinition<Id, S, G, A>, keyMapper: KeyMapper): _MapStateObjectReturn<Id, S, G, A, KeyMapper>;
/**
 * Allows using state and getters from one store without using the composition
 * API (`setup()`) by generating an object to be spread in the `computed` field
 * of a component.
 *
 * @example
 * ```js
 * export default {
 *   computed: {
 *     // other computed properties
 *     ...mapState(useCounterStore, ['count', 'double'])
 *   },
 *
 *   created() {
 *     this.count // 2
 *     this.double // 4
 *   }
 * }
 * ```
 *
 * @param useStore - store to map from
 * @param keys - array of state properties or getters
 */
declare function mapState<Id extends string, S extends StateTree, G extends _GettersTree<S> | {
  [key: string]: ComputedRef;
}, A, Keys extends keyof S | keyof G>(useStore: StoreDefinition<Id, S, G, A>, keys: readonly Keys[]): _MapStateReturn<S, G, Keys>;
/**
 * Alias for `mapState()`. You should use `mapState()` instead.
 * @deprecated use `mapState()` instead.
 */
declare const mapGetters: typeof mapState;
/**
 * For internal use **only**
 */
type _MapActionsReturn<A> = { [key in keyof A]: A[key]; };
/**
 * For internal use **only**
 */
type _MapActionsObjectReturn<A, T extends Record<string, keyof A>> = { [key in keyof T]: A[T[key]]; };
/**
 * Allows directly using actions from your store without using the composition
 * API (`setup()`) by generating an object to be spread in the `methods` field
 * of a component. The values of the object are the actions while the keys are
 * the names of the resulting methods.
 *
 * @example
 * ```js
 * export default {
 *   methods: {
 *     // other methods properties
 *     // useCounterStore has two actions named `increment` and `setCount`
 *     ...mapActions(useCounterStore, { more: 'increment', setIt: 'setCount' })
 *   },
 *
 *   created() {
 *     this.more()
 *     this.setIt(2)
 *   }
 * }
 * ```
 *
 * @param useStore - store to map from
 * @param keyMapper - object to define new names for the actions
 */
declare function mapActions<Id extends string, S extends StateTree, G extends _GettersTree<S>, A, KeyMapper extends Record<string, keyof A>>(useStore: StoreDefinition<Id, S, G, A>, keyMapper: KeyMapper): _MapActionsObjectReturn<A, KeyMapper>;
/**
 * Allows directly using actions from your store without using the composition
 * API (`setup()`) by generating an object to be spread in the `methods` field
 * of a component.
 *
 * @example
 * ```js
 * export default {
 *   methods: {
 *     // other methods properties
 *     ...mapActions(useCounterStore, ['increment', 'setCount'])
 *   },
 *
 *   created() {
 *     this.increment()
 *     this.setCount(2) // pass arguments as usual
 *   }
 * }
 * ```
 *
 * @param useStore - store to map from
 * @param keys - array of action names to map
 */
declare function mapActions<Id extends string, S extends StateTree, G extends _GettersTree<S>, A>(useStore: StoreDefinition<Id, S, G, A>, keys: Array<keyof A>): _MapActionsReturn<A>;
/**
 * For internal use **only**
 */
type _MapWritableStateKeys<S extends StateTree, G> = keyof UnwrapRef<S> | keyof _StoreWithGetters_Writable<G>;
/**
 * For internal use **only**
 */
type _MapWritableStateReturn<S extends StateTree, G, Keys extends _MapWritableStateKeys<S, G>> = { [key in Keys]: {
  get: () => UnwrapRef<(S & G)[key]>;
  set: (value: UnwrapRef<(S & G)[key]>) => any;
}; };
/**
 * For internal use **only**
 */
type _MapWritableStateObjectReturn<S extends StateTree, G, KeyMapper extends Record<string, _MapWritableStateKeys<S, G>>> = { [key in keyof KeyMapper]: {
  get: () => UnwrapRef<(S & G)[KeyMapper[key]]>;
  set: (value: UnwrapRef<(S & G)[KeyMapper[key]]>) => any;
}; };
/**
 * Same as `mapState()` but creates computed setters as well so the state can be
 * modified. Differently from `mapState()`, only `state` properties can be
 * added.
 *
 * @param useStore - store to map from
 * @param keyMapper - object of state properties
 */
declare function mapWritableState<Id extends string, S extends StateTree, G, A, KeyMapper extends Record<string, _MapWritableStateKeys<S, G>>>(useStore: StoreDefinition<Id, S, G, A>, keyMapper: KeyMapper): _MapWritableStateObjectReturn<S, G, KeyMapper>;
/**
 * Allows using state and getters from one store without using the composition
 * API (`setup()`) by generating an object to be spread in the `computed` field
 * of a component.
 *
 * @param useStore - store to map from
 * @param keys - array of state properties
 */
declare function mapWritableState<Id extends string, S extends StateTree, G, A, Keys extends _MapWritableStateKeys<S, G>>(useStore: StoreDefinition<Id, S, G, A>, keys: readonly Keys[]): Pick<_MapWritableStateReturn<S, G, Keys>, Keys>;
//#endregion
//#region src/storeToRefs.d.ts
/**
 * Internal utility type
 */
type _IfEquals<X, Y, A = true, B = false> = (<T>() => T extends X ? 1 : 2) extends (<T>() => T extends Y ? 1 : 2) ? A : B;
/**
 * Internal utility type
 */
type _IsReadonly<T, K extends keyof T> = _IfEquals<{ [P in K]: T[P]; }, { -readonly [P in K]: T[P]; }, false // Property is not readonly if they are the same
, true>;
/**
 * Extracts the getters of a store while keeping writable and readonly properties. **Internal type DO NOT USE**.
 */
type _ToComputedRefs<SS> = { [K in keyof SS]: true extends _IsReadonly<SS, K> ? ComputedRef<SS[K]> : WritableComputedRef<SS[K]>; };
/**
 * Extracts the refs of a state object from a store. If the state value is a Ref or type that extends ref, it will be kept as is.
 * Otherwise, it will be converted into a Ref. **Internal type DO NOT USE**.
 */
type _ToStateRefs<SS> = SS extends Store<string, infer UnwrappedState, _GettersTree<StateTree>, _ActionsTree> ? UnwrappedState extends _UnwrapAll<Pick<infer State, infer Key>> ? { [K in Key]: ToRef<State[K]>; } : ToRefs<UnwrappedState> : ToRefs<StoreState<SS>>;
/**
 * Extracts the return type for `storeToRefs`.
 * Will convert any `getters` into `ComputedRef`.
 */
type StoreToRefs<SS extends StoreGeneric> = SS extends unknown ? _ToStateRefs<SS> & ToRefs<PiniaCustomStateProperties<StoreState<SS>>> & _ToComputedRefs<StoreGetters<SS>> : never;
/**
 * Creates an object of references with all the state, getters, and plugin-added
 * state properties of the store. Similar to `toRefs()` but specifically
 * designed for Pinia stores so methods and non reactive properties are
 * completely ignored.
 *
 * @param store - store to extract the refs from
 */
declare function storeToRefs<SS extends StoreGeneric>(store: SS): StoreToRefs<SS>;
//#endregion
//#region src/hmr.d.ts
/**
 * Creates an _accept_ function to pass to `import.meta.hot` in Vite applications.
 *
 * @example
 * ```js
 * const useUser = defineStore(...)
 * if (import.meta.hot) {
 *   import.meta.hot.accept(acceptHMRUpdate(useUser, import.meta.hot))
 * }
 * ```
 *
 * @param initialUseStore - return of the defineStore to hot update
 * @param hot - `import.meta.hot`
 */
declare function acceptHMRUpdate<Id extends string = string, S extends StateTree = StateTree, G extends _GettersTree<S> = _GettersTree<S>, A = _ActionsTree>(initialUseStore: StoreDefinition<Id, S, G, A>, hot: any): (newModule: any) => any;
//#endregion
//#region src/globalExtensions.d.ts
declare module 'vue' {
  interface GlobalComponents {}
  interface ComponentCustomProperties {
    /**
     * Access to the application's Pinia
     */
    $pinia: Pinia;
    /**
     * Cache of stores instantiated by the current instance. Used by devtools to
     * list currently used stores. Used internally by Pinia.
     *
     * @internal
     */
    _pStores?: Record<string, StoreGeneric>;
  }
}
//#endregion
export { type DefineSetupStoreOptions, type DefineStoreOptions, type DefineStoreOptionsBase, type DefineStoreOptionsInPlugin, type MapStoresCustomization, MutationType, type Pinia, type PiniaCustomProperties, type PiniaCustomStateProperties, type PiniaPlugin, type PiniaPluginContext, type SetupStoreDefinition, type StateTree, type Store, type StoreActions, type StoreDefinition, type StoreGeneric, type StoreGetters, type StoreOnActionListener, type StoreOnActionListenerContext, type StoreProperties, type StoreState, type SubscriptionCallback, type SubscriptionCallbackMutation, type SubscriptionCallbackMutationDirect, type SubscriptionCallbackMutationPatchFunction, type SubscriptionCallbackMutationPatchObject, type _ActionsTree, type _DeepPartial, type _ExtractActionsFromSetupStore, type _ExtractActionsFromSetupStore_Keys, type _ExtractGettersFromSetupStore, type _ExtractGettersFromSetupStore_Keys, type _ExtractStateFromSetupStore, type _ExtractStateFromSetupStore_Keys, type _GettersTree, type _MapActionsObjectReturn, type _MapActionsReturn, type _MapStateObjectReturn, type _MapStateReturn, type _MapWritableStateObjectReturn, type _MapWritableStateReturn, type _Method, type _Spread, type _StoreObject, type _StoreOnActionListenerContext, type _StoreWithActions, type _StoreWithGetters, type _StoreWithState, type _SubscriptionCallbackMutationBase, type _UnwrapAll, acceptHMRUpdate, createPinia, defineStore, disposePinia, getActivePinia, mapActions, mapGetters, mapState, mapStores, mapWritableState, piniaSymbol, setActivePinia, setMapStoreSuffix, shouldHydrate, skipHydrate, storeToRefs };