import { ResolveHook, ResolveHookSync } from 'node:module';

declare let resolve: ResolveHook;
declare let resolveSync: ResolveHookSync;

export { resolve, resolveSync };
