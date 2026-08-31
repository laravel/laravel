export async function resolvePageComponent(path, pages) {
    for (const p of (Array.isArray(path) ? path : [path])) {
        const page = pages[p];
        if (typeof page === 'undefined') {
            continue;
        }
        return typeof page === 'function' ? page() : page;
    }
    throw new Error(`Page not found: ${path}`);
}
