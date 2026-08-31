<?php

namespace Illuminate\Foundation;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Vite implements Htmlable
{
    use Macroable;

    /**
     * The Content Security Policy nonce to apply to all generated tags.
     *
     * @var string|null
     */
    protected $nonce;

    /**
     * The key to check for integrity hashes within the manifest.
     *
     * @var string|false
     */
    protected $integrityKey = 'integrity';

    /**
     * The configured entry points.
     *
     * @var array
     */
    protected $entryPoints = [];

    /**
     * The path to the "hot" file.
     *
     * @var string|null
     */
    protected $hotFile;

    /**
     * The path to the build directory.
     *
     * @var string
     */
    protected $buildDirectory = 'build';

    /**
     * The name of the manifest file.
     *
     * @var string
     */
    protected $manifestFilename = 'manifest.json';

    /**
     * The custom asset path resolver.
     *
     * @var callable|null
     */
    protected $assetPathResolver = null;

    /**
     * The script tag attributes resolvers.
     *
     * @var array
     */
    protected $scriptTagAttributesResolvers = [];

    /**
     * The style tag attributes resolvers.
     *
     * @var array
     */
    protected $styleTagAttributesResolvers = [];

    /**
     * The preload tag attributes resolvers.
     *
     * @var array
     */
    protected $preloadTagAttributesResolvers = [];

    /**
     * The preloaded assets.
     *
     * @var array
     */
    protected $preloadedAssets = [];

    /**
     * The cached manifest files.
     *
     * @var array
     */
    protected static $manifests = [];

    /**
     * The prefetching strategy to use.
     *
     * @var null|'waterfall'|'aggressive'
     */
    protected $prefetchStrategy = null;

    /**
     * The number of assets to load concurrently when using the "waterfall" strategy.
     *
     * @var int
     */
    protected $prefetchConcurrently = 3;

    /**
     * The name of the event that should trigger prefetching. The event must be dispatched on the `window`.
     *
     * @var string
     */
    protected $prefetchEvent = 'load';

    /**
     * Get the preloaded assets.
     *
     * @return array
     */
    public function preloadedAssets()
    {
        return $this->preloadedAssets;
    }

    /**
     * Get the Content Security Policy nonce applied to all generated tags.
     *
     * @return string|null
     */
    public function cspNonce()
    {
        return $this->nonce;
    }

    /**
     * Generate or set a Content Security Policy nonce to apply to all generated tags.
     *
     * @param  string|null  $nonce
     * @return string
     */
    public function useCspNonce($nonce = null)
    {
        return $this->nonce = $nonce ?? Str::random(40);
    }

    /**
     * Use the given key to detect integrity hashes in the manifest.
     *
     * @param  string|false  $key
     * @return $this
     */
    public function useIntegrityKey($key)
    {
        $this->integrityKey = $key;

        return $this;
    }

    /**
     * Set the Vite entry points.
     *
     * @param  array  $entryPoints
     * @return $this
     */
    public function withEntryPoints($entryPoints)
    {
        $this->entryPoints = $entryPoints;

        return $this;
    }

    /**
     * Merge additional Vite entry points with the current set.
     *
     * @param  array  $entryPoints
     * @return $this
     */
    public function mergeEntryPoints($entryPoints)
    {
        return $this->withEntryPoints(array_unique([
            ...$this->entryPoints,
            ...$entryPoints,
        ]));
    }

    /**
     * Set the filename for the manifest file.
     *
     * @param  string  $filename
     * @return $this
     */
    public function useManifestFilename($filename)
    {
        $this->manifestFilename = $filename;

        return $this;
    }

    /**
     * Resolve asset paths using the provided resolver.
     *
     * @param  callable|null  $resolver
     * @return $this
     */
    public function createAssetPathsUsing($resolver)
    {
        $this->assetPathResolver = $resolver;

        return $this;
    }

    /**
     * Get the Vite "hot" file path.
     *
     * @return string
     */
    public function hotFile()
    {
        return $this->hotFile ?? public_path('/hot');
    }

    /**
     * Set the Vite "hot" file path.
     *
     * @param  string  $path
     * @return $this
     */
    public function useHotFile($path)
    {
        $this->hotFile = $path;

        return $this;
    }

    /**
     * Set the Vite build directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useBuildDirectory($path)
    {
        $this->buildDirectory = $path;

        return $this;
    }

    /**
     * Use the given callback to resolve attributes for script tags.
     *
     * @param  (callable(string, string, ?array, ?array): array)|array  $attributes
     * @return $this
     */
    public function useScriptTagAttributes($attributes)
    {
        if (! is_callable($attributes)) {
            $attributes = fn () => $attributes;
        }

        $this->scriptTagAttributesResolvers[] = $attributes;

        return $this;
    }

    /**
     * Use the given callback to resolve attributes for style tags.
     *
     * @param  (callable(string, string, ?array, ?array): array)|array  $attributes
     * @return $this
     */
    public function useStyleTagAttributes($attributes)
    {
        if (! is_callable($attributes)) {
            $attributes = fn () => $attributes;
        }

        $this->styleTagAttributesResolvers[] = $attributes;

        return $this;
    }

    /**
     * Use the given callback to resolve attributes for preload tags.
     *
     * @param  (callable(string, string, ?array, ?array): (array|false))|array|false  $attributes
     * @return $this
     */
    public function usePreloadTagAttributes($attributes)
    {
        if (! is_callable($attributes)) {
            $attributes = fn () => $attributes;
        }

        $this->preloadTagAttributesResolvers[] = $attributes;

        return $this;
    }

    /**
     * Eagerly prefetch assets.
     *
     * @param  int|null  $concurrency
     * @param  string  $event
     * @return $this
     */
    public function prefetch($concurrency = null, $event = 'load')
    {
        $this->prefetchEvent = $event;

        return $concurrency === null
            ? $this->usePrefetchStrategy('aggressive')
            : $this->usePrefetchStrategy('waterfall', ['concurrency' => $concurrency]);
    }

    /**
     * Use the "waterfall" prefetching strategy.
     *
     * @return $this
     */
    public function useWaterfallPrefetching(?int $concurrency = null)
    {
        return $this->usePrefetchStrategy('waterfall', [
            'concurrency' => $concurrency ?? $this->prefetchConcurrently,
        ]);
    }

    /**
     * Use the "aggressive" prefetching strategy.
     *
     * @return $this
     */
    public function useAggressivePrefetching()
    {
        return $this->usePrefetchStrategy('aggressive');
    }

    /**
     * Set the prefetching strategy.
     *
     * @param  'waterfall'|'aggressive'|null  $strategy
     * @param  array  $config
     * @return $this
     */
    public function usePrefetchStrategy($strategy, $config = [])
    {
        $this->prefetchStrategy = $strategy;

        if ($strategy === 'waterfall') {
            $this->prefetchConcurrently = $config['concurrency'] ?? $this->prefetchConcurrently;
        }

        return $this;
    }

    /**
     * Generate Vite tags for an entrypoint.
     *
     * @param  string|string[]  $entrypoints
     * @param  string|null  $buildDirectory
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Exception
     */
    public function __invoke($entrypoints, $buildDirectory = null)
    {
        $entrypoints = new Collection($entrypoints);
        $buildDirectory ??= $this->buildDirectory;

        if ($this->isRunningHot()) {
            return new HtmlString(
                $entrypoints
                    ->prepend('@vite/client')
                    ->map(fn ($entrypoint) => $this->makeTagForChunk($entrypoint, $this->hotAsset($entrypoint), null, null))
                    ->join('')
            );
        }

        $manifest = $this->manifest($buildDirectory);

        $tags = new Collection;
        $preloads = new Collection;

        foreach ($entrypoints as $entrypoint) {
            $chunk = $this->chunk($manifest, $entrypoint);

            $preloads->push([
                $chunk['src'],
                $this->assetPath("{$buildDirectory}/{$chunk['file']}"),
                $chunk,
                $manifest,
            ]);

            foreach ($chunk['imports'] ?? [] as $import) {
                $preloads->push([
                    $import,
                    $this->assetPath("{$buildDirectory}/{$manifest[$import]['file']}"),
                    $manifest[$import],
                    $manifest,
                ]);

                foreach ($manifest[$import]['css'] ?? [] as $css) {
                    $partialManifest = (new Collection($manifest))->where('file', $css);

                    $preloads->push([
                        $partialManifest->keys()->first(),
                        $this->assetPath("{$buildDirectory}/{$css}"),
                        $partialManifest->first(),
                        $manifest,
                    ]);

                    $tags->push($this->makeTagForChunk(
                        $partialManifest->keys()->first(),
                        $this->assetPath("{$buildDirectory}/{$css}"),
                        $partialManifest->first(),
                        $manifest
                    ));
                }
            }

            $tags->push($this->makeTagForChunk(
                $entrypoint,
                $this->assetPath("{$buildDirectory}/{$chunk['file']}"),
                $chunk,
                $manifest
            ));

            foreach ($chunk['css'] ?? [] as $css) {
                $partialManifest = (new Collection($manifest))->where('file', $css);

                $preloads->push([
                    $partialManifest->keys()->first(),
                    $this->assetPath("{$buildDirectory}/{$css}"),
                    $partialManifest->first(),
                    $manifest,
                ]);

                $tags->push($this->makeTagForChunk(
                    $partialManifest->keys()->first(),
                    $this->assetPath("{$buildDirectory}/{$css}"),
                    $partialManifest->first(),
                    $manifest
                ));
            }
        }

        [$stylesheets, $scripts] = $tags->unique()->partition(fn ($tag) => str_starts_with($tag, '<link'));

        $preloads = $preloads->unique()
            ->sortByDesc(fn ($args) => $this->isCssPath($args[1]))
            ->map(fn ($args) => $this->makePreloadTagForChunk(...$args));

        $base = $preloads->join('').$stylesheets->join('').$scripts->join('');

        if ($this->prefetchStrategy === null || $this->isRunningHot()) {
            return new HtmlString($base);
        }

        $discoveredImports = [];

        return (new Collection($entrypoints))
            ->flatMap(fn ($entrypoint) => (new Collection($manifest[$entrypoint]['dynamicImports'] ?? []))
                ->map(fn ($import) => $manifest[$import])
                ->filter(fn ($chunk) => str_ends_with($chunk['file'], '.js') || str_ends_with($chunk['file'], '.css'))
                ->flatMap($f = function ($chunk) use (&$f, $manifest, &$discoveredImports) {
                    return (new Collection([...$chunk['imports'] ?? [], ...$chunk['dynamicImports'] ?? []]))
                        ->reject(function ($import) use (&$discoveredImports) {
                            if (isset($discoveredImports[$import])) {
                                return true;
                            }

                            return ! $discoveredImports[$import] = true;
                        })
                        ->reduce(
                            fn ($chunks, $import) => $chunks->merge(
                                $f($manifest[$import])
                            ), new Collection([$chunk]))
                        ->merge((new Collection($chunk['css'] ?? []))->map(
                            fn ($css) => (new Collection($manifest))->first(fn ($chunk) => $chunk['file'] === $css) ?? [
                                'file' => $css,
                            ],
                        ));
                })
                ->map(function ($chunk) use ($buildDirectory, $manifest) {
                    return (new Collection([
                        ...$this->resolvePreloadTagAttributes(
                            $chunk['src'] ?? null,
                            $url = $this->assetPath("{$buildDirectory}/{$chunk['file']}"),
                            $chunk,
                            $manifest,
                        ),
                        'rel' => 'prefetch',
                        'fetchpriority' => 'low',
                        'href' => $url,
                    ]))->reject(
                        fn ($value) => in_array($value, [null, false], true)
                    )->mapWithKeys(fn ($value, $key) => [
                        $key = (is_int($key) ? $value : $key) => $value === true ? $key : $value,
                    ])->all();
                })
                ->reject(fn ($attributes) => isset($this->preloadedAssets[$attributes['href']])))
            ->unique('href')
            ->values()
            ->pipe(fn ($assets) => with(Js::from($assets), fn ($assets) => match ($this->prefetchStrategy) {
                'waterfall' => new HtmlString($base.<<<HTML

                    <script{$this->nonceAttribute()}>
                         window.addEventListener('{$this->prefetchEvent}', () => window.setTimeout(() => {
                            const makeLink = (asset) => {
                                const link = document.createElement('link')

                                Object.keys(asset).forEach((attribute) => {
                                    link.setAttribute(attribute, asset[attribute])
                                })

                                return link
                            }

                            const loadNext = (assets, count) => window.setTimeout(() => {
                                if (count > assets.length) {
                                    count = assets.length

                                    if (count === 0) {
                                        return
                                    }
                                }

                                const fragment = new DocumentFragment

                                while (count > 0) {
                                    const link = makeLink(assets.shift())
                                    fragment.append(link)
                                    count--

                                    if (assets.length) {
                                        link.onload = () => loadNext(assets, 1)
                                        link.onerror = () => loadNext(assets, 1)
                                    }
                                }

                                document.head.append(fragment)
                            })

                            loadNext({$assets}, {$this->prefetchConcurrently})
                        }))
                    </script>
                    HTML),
                'aggressive' => new HtmlString($base.<<<HTML

                    <script{$this->nonceAttribute()}>
                         window.addEventListener('{$this->prefetchEvent}', () => window.setTimeout(() => {
                            const makeLink = (asset) => {
                                const link = document.createElement('link')

                                Object.keys(asset).forEach((attribute) => {
                                    link.setAttribute(attribute, asset[attribute])
                                })

                                return link
                            }

                            const fragment = new DocumentFragment;
                            {$assets}.forEach((asset) => fragment.append(makeLink(asset)))
                            document.head.append(fragment)
                         }))
                    </script>
                    HTML),
            }));
    }

    /**
     * Make tag for the given chunk.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array|null  $chunk
     * @param  array|null  $manifest
     * @return string
     */
    protected function makeTagForChunk($src, $url, $chunk, $manifest)
    {
        if (
            $this->nonce === null
            && $this->integrityKey !== false
            && ! array_key_exists($this->integrityKey, $chunk ?? [])
            && $this->scriptTagAttributesResolvers === []
            && $this->styleTagAttributesResolvers === []) {
            return $this->makeTag($url);
        }

        if ($this->isCssPath($url)) {
            return $this->makeStylesheetTagWithAttributes(
                $url,
                $this->resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)
            );
        }

        return $this->makeScriptTagWithAttributes(
            $url,
            $this->resolveScriptTagAttributes($src, $url, $chunk, $manifest)
        );
    }

    /**
     * Make a preload tag for the given chunk.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array  $chunk
     * @param  array  $manifest
     * @return string
     */
    protected function makePreloadTagForChunk($src, $url, $chunk, $manifest)
    {
        $attributes = $this->resolvePreloadTagAttributes($src, $url, $chunk, $manifest);

        if ($attributes === false) {
            return '';
        }

        $this->preloadedAssets[$url] = $this->parseAttributes(
            (new Collection($attributes))->forget('href')->all()
        );

        return '<link '.implode(' ', $this->parseAttributes($attributes)).' />';
    }

    /**
     * Resolve the attributes for the chunks generated script tag.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array|null  $chunk
     * @param  array|null  $manifest
     * @return array
     */
    protected function resolveScriptTagAttributes($src, $url, $chunk, $manifest)
    {
        $attributes = $this->integrityKey !== false
            ? ['integrity' => $chunk[$this->integrityKey] ?? false]
            : [];

        foreach ($this->scriptTagAttributesResolvers as $resolver) {
            $attributes = array_merge($attributes, $resolver($src, $url, $chunk, $manifest));
        }

        return $attributes;
    }

    /**
     * Resolve the attributes for the chunks generated stylesheet tag.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array|null  $chunk
     * @param  array|null  $manifest
     * @return array
     */
    protected function resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)
    {
        $attributes = $this->integrityKey !== false
            ? ['integrity' => $chunk[$this->integrityKey] ?? false]
            : [];

        foreach ($this->styleTagAttributesResolvers as $resolver) {
            $attributes = array_merge($attributes, $resolver($src, $url, $chunk, $manifest));
        }

        return $attributes;
    }

    /**
     * Resolve the attributes for the chunks generated preload tag.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array  $chunk
     * @param  array  $manifest
     * @return array|false
     */
    protected function resolvePreloadTagAttributes($src, $url, $chunk, $manifest)
    {
        $attributes = $this->isCssPath($url) ? [
            'rel' => 'preload',
            'as' => 'style',
            'href' => $url,
            'nonce' => $this->nonce ?? false,
            'crossorigin' => $this->resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)['crossorigin'] ?? false,
        ] : [
            'rel' => 'modulepreload',
            'as' => 'script',
            'href' => $url,
            'nonce' => $this->nonce ?? false,
            'crossorigin' => $this->resolveScriptTagAttributes($src, $url, $chunk, $manifest)['crossorigin'] ?? false,
        ];

        $attributes = $this->integrityKey !== false
            ? array_merge($attributes, ['integrity' => $chunk[$this->integrityKey] ?? false])
            : $attributes;

        foreach ($this->preloadTagAttributesResolvers as $resolver) {
            if (false === ($resolvedAttributes = $resolver($src, $url, $chunk, $manifest))) {
                return false;
            }

            $attributes = array_merge($attributes, $resolvedAttributes);
        }

        return $attributes;
    }

    /**
     * Generate an appropriate tag for the given URL in HMR mode.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @param  string  $url
     * @return string
     */
    protected function makeTag($url)
    {
        if ($this->isCssPath($url)) {
            return $this->makeStylesheetTag($url);
        }

        return $this->makeScriptTag($url);
    }

    /**
     * Generate a script tag for the given URL.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @param  string  $url
     * @return string
     */
    protected function makeScriptTag($url)
    {
        return $this->makeScriptTagWithAttributes($url, []);
    }

    /**
     * Generate a stylesheet tag for the given URL in HMR mode.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @param  string  $url
     * @return string
     */
    protected function makeStylesheetTag($url)
    {
        return $this->makeStylesheetTagWithAttributes($url, []);
    }

    /**
     * Generate a script tag with attributes for the given URL.
     *
     * @param  string  $url
     * @param  array  $attributes
     * @return string
     */
    protected function makeScriptTagWithAttributes($url, $attributes)
    {
        $attributes = $this->parseAttributes(array_merge([
            'type' => 'module',
            'src' => $url,
            'nonce' => $this->nonce ?? false,
        ], $attributes));

        return '<script '.implode(' ', $attributes).'></script>';
    }

    /**
     * Generate a link tag with attributes for the given URL.
     *
     * @param  string  $url
     * @param  array  $attributes
     * @return string
     */
    protected function makeStylesheetTagWithAttributes($url, $attributes)
    {
        $attributes = $this->parseAttributes(array_merge([
            'rel' => 'stylesheet',
            'href' => $url,
            'nonce' => $this->nonce ?? false,
        ], $attributes));

        return '<link '.implode(' ', $attributes).' />';
    }

    /**
     * Determine whether the given path is a CSS file.
     *
     * @param  string  $path
     * @return bool
     */
    protected function isCssPath($path)
    {
        return preg_match('/\.(css|less|sass|scss|styl|stylus|pcss|postcss)(\?[^\.]*)?$/', $path) === 1;
    }

    /**
     * Parse the attributes into key="value" strings.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function parseAttributes($attributes)
    {
        return (new Collection($attributes))
            ->reject(fn ($value, $key) => in_array($value, [false, null], true))
            ->flatMap(fn ($value, $key) => $value === true ? [$key] : [$key => $value])
            ->map(fn ($value, $key) => is_int($key) ? $value : $key.'="'.$value.'"')
            ->values()
            ->all();
    }

    /**
     * Generate React refresh runtime script.
     *
     * @return \Illuminate\Support\HtmlString|void
     */
    public function reactRefresh()
    {
        if (! $this->isRunningHot()) {
            return;
        }

        $attributes = $this->parseAttributes([
            'nonce' => $this->cspNonce(),
        ]);

        return new HtmlString(
            sprintf(
                <<<'HTML'
                <script type="module" %s>
                    import RefreshRuntime from '%s'
                    RefreshRuntime.injectIntoGlobalHook(window)
                    window.$RefreshReg$ = () => {}
                    window.$RefreshSig$ = () => (type) => type
                    window.__vite_plugin_react_preamble_installed__ = true
                </script>
                HTML,
                implode(' ', $attributes),
                $this->hotAsset('@react-refresh')
            )
        );
    }

    /**
     * Get the path to a given asset when running in HMR mode.
     *
     * @return string
     */
    protected function hotAsset($asset)
    {
        return rtrim(file_get_contents($this->hotFile())).'/'.$asset;
    }

    /**
     * Get the URL for an asset.
     *
     * @param  string  $asset
     * @param  string|null  $buildDirectory
     * @return string
     */
    public function asset($asset, $buildDirectory = null)
    {
        $buildDirectory ??= $this->buildDirectory;

        if ($this->isRunningHot()) {
            return $this->hotAsset($asset);
        }

        $chunk = $this->chunk($this->manifest($buildDirectory), $asset);

        return $this->assetPath($buildDirectory.'/'.$chunk['file']);
    }

    /**
     * Get the content of a given asset.
     *
     * @param  string  $asset
     * @param  string|null  $buildDirectory
     * @return string
     *
     * @throws \Illuminate\Foundation\ViteException
     */
    public function content($asset, $buildDirectory = null)
    {
        $buildDirectory ??= $this->buildDirectory;

        $chunk = $this->chunk($this->manifest($buildDirectory), $asset);

        $path = $this->publicPath($buildDirectory.'/'.$chunk['file']);

        if (! is_file($path) || ! file_exists($path)) {
            throw new ViteException("Unable to locate file from Vite manifest: {$path}.");
        }

        return file_get_contents($path);
    }

    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    protected function assetPath($path, $secure = null)
    {
        return ($this->assetPathResolver ?? asset(...))($path, $secure);
    }

    /**
     * Generate a public path for an asset.
     *
     * @param  string  $path
     * @return string
     */
    protected function publicPath($path)
    {
        return public_path($path);
    }

    /**
     * Get the manifest file for the given build directory.
     *
     * @param  string  $buildDirectory
     * @return array
     *
     * @throws \Illuminate\Foundation\ViteManifestNotFoundException
     */
    protected function manifest($buildDirectory)
    {
        $path = $this->manifestPath($buildDirectory);

        if (! isset(static::$manifests[$path])) {
            if (! is_file($path)) {
                throw new ViteManifestNotFoundException("Vite manifest not found at: $path");
            }

            static::$manifests[$path] = json_decode(file_get_contents($path), true);
        }

        return static::$manifests[$path];
    }

    /**
     * Get the path to the manifest file for the given build directory.
     *
     * @param  string  $buildDirectory
     * @return string
     */
    protected function manifestPath($buildDirectory)
    {
        return public_path($buildDirectory.'/'.$this->manifestFilename);
    }

    /**
     * Get a unique hash representing the current manifest, or null if there is no manifest.
     *
     * @param  string|null  $buildDirectory
     * @return string|null
     */
    public function manifestHash($buildDirectory = null)
    {
        $buildDirectory ??= $this->buildDirectory;

        if ($this->isRunningHot()) {
            return null;
        }

        if (! is_file($path = $this->manifestPath($buildDirectory))) {
            return null;
        }

        return md5_file($path) ?: null;
    }

    /**
     * Get the chunk for the given entry point / asset.
     *
     * @param  array  $manifest
     * @param  string  $file
     * @return array
     *
     * @throws \Illuminate\Foundation\ViteException
     */
    protected function chunk($manifest, $file)
    {
        if (! isset($manifest[$file])) {
            throw new ViteException("Unable to locate file in Vite manifest: {$file}.");
        }

        return $manifest[$file];
    }

    /**
     * Get the nonce attribute for the prefetch script tags.
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function nonceAttribute()
    {
        if ($this->cspNonce() === null) {
            return new HtmlString('');
        }

        return new HtmlString(' nonce="'.$this->cspNonce().'"');
    }

    /**
     * Determine if the HMR server is running.
     *
     * @return bool
     */
    public function isRunningHot()
    {
        return is_file($this->hotFile());
    }

    /**
     * Get the Vite tag content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->__invoke($this->entryPoints)->toHtml();
    }

    /**
     * Flush state.
     *
     * @return void
     */
    public function flush()
    {
        $this->preloadedAssets = [];
    }
}
