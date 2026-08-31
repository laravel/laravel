<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This code is partially based on the Rack-Cache library by Ryan Tomayko,
 * which is released under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Store implements all the logic for storing cache metadata (Request and Response headers).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Store implements StoreInterface
{
    /** @var \SplObjectStorage<Request, string> */
    private \SplObjectStorage $keyCache;
    /** @var array<string, resource> */
    private array $locks = [];

    /**
     * Constructor.
     *
     * The available options are:
     *
     *   * private_headers  Set of response headers that should not be stored
     *                      when a response is cached. (default: Set-Cookie)
     *
     * @throws \RuntimeException
     */
    public function __construct(
        protected string $root,
        private array $options = [],
    ) {
        if (!is_dir($this->root) && !@mkdir($this->root, 0o777, true) && !is_dir($this->root)) {
            throw new \RuntimeException(\sprintf('Unable to create the store directory (%s).', $this->root));
        }
        $this->keyCache = new \SplObjectStorage();
        $this->options['private_headers'] ??= ['Set-Cookie'];
    }

    /**
     * Cleanups storage.
     */
    public function cleanup(): void
    {
        // unlock everything
        foreach ($this->locks as $lock) {
            flock($lock, \LOCK_UN);
            fclose($lock);
        }

        $this->locks = [];
    }

    /**
     * Tries to lock the cache for a given Request, without blocking.
     *
     * @return bool|string true if the lock is acquired, the path to the current lock otherwise
     */
    public function lock(Request $request): bool|string
    {
        $key = $this->getCacheKey($request);

        if (!isset($this->locks[$key])) {
            $path = $this->getPath($key);
            if (!is_dir(\dirname($path)) && false === @mkdir(\dirname($path), 0o777, true) && !is_dir(\dirname($path))) {
                return $path;
            }
            $h = fopen($path, 'c');
            if (!flock($h, \LOCK_EX | \LOCK_NB)) {
                fclose($h);

                return $path;
            }

            $this->locks[$key] = $h;
        }

        return true;
    }

    /**
     * Releases the lock for the given Request.
     *
     * @return bool False if the lock file does not exist or cannot be unlocked, true otherwise
     */
    public function unlock(Request $request): bool
    {
        $key = $this->getCacheKey($request);

        if (isset($this->locks[$key])) {
            flock($this->locks[$key], \LOCK_UN);
            fclose($this->locks[$key]);
            unset($this->locks[$key]);

            return true;
        }

        return false;
    }

    public function isLocked(Request $request): bool
    {
        $key = $this->getCacheKey($request);

        if (isset($this->locks[$key])) {
            return true; // shortcut if lock held by this process
        }

        if (!is_file($path = $this->getPath($key))) {
            return false;
        }

        $h = fopen($path, 'r');
        flock($h, \LOCK_EX | \LOCK_NB, $wouldBlock);
        flock($h, \LOCK_UN); // release the lock we just acquired
        fclose($h);

        return (bool) $wouldBlock;
    }

    /**
     * Locates a cached Response for the Request provided.
     */
    public function lookup(Request $request): ?Response
    {
        $key = $this->getCacheKey($request);

        if (!$entries = $this->getMetadata($key)) {
            return null;
        }

        // find a cached entry that matches the request.
        $match = null;
        foreach ($entries as $entry) {
            if ($this->requestsMatch(isset($entry[1]['vary'][0]) ? implode(', ', $entry[1]['vary']) : '', $request->headers->all(), $entry[0])) {
                $match = $entry;

                break;
            }
        }

        if (null === $match) {
            return null;
        }

        $headers = $match[1];
        if (file_exists($path = $this->getPath($headers['x-content-digest'][0]))) {
            return $this->restoreResponse($headers, $path);
        }

        // TODO the metaStore referenced an entity that doesn't exist in
        // the entityStore. We definitely want to return nil but we should
        // also purge the entry from the meta-store when this is detected.
        return null;
    }

    /**
     * Writes a cache entry to the store for the given Request and Response.
     *
     * Existing entries are read and any that match the response are removed. This
     * method calls write with the new list of cache entries.
     *
     * @throws \RuntimeException
     */
    public function write(Request $request, Response $response): string
    {
        $key = $this->getCacheKey($request);
        $storedEnv = $this->persistRequest($request);

        if ($response->headers->has('X-Body-File')) {
            // Assume the response came from disk, but at least perform some safeguard checks
            if (!$response->headers->has('X-Content-Digest')) {
                throw new \RuntimeException('A restored response must have the X-Content-Digest header.');
            }

            $digest = $response->headers->get('X-Content-Digest');
            if ($this->getPath($digest) !== $response->headers->get('X-Body-File')) {
                throw new \RuntimeException('X-Body-File and X-Content-Digest do not match.');
            }
        // Everything seems ok, omit writing content to disk
        } else {
            $digest = $this->generateContentDigest($response);
            $response->headers->set('X-Content-Digest', $digest);

            if (!$this->save($digest, $response->getContent(), false)) {
                throw new \RuntimeException('Unable to store the entity.');
            }

            if (!$response->headers->has('Transfer-Encoding')) {
                $response->headers->set('Content-Length', \strlen($response->getContent()));
            }
        }

        // read existing cache entries, remove non-varying, and add this one to the list
        $entries = [];
        $vary = implode(', ', $response->headers->all('vary'));
        foreach ($this->getMetadata($key) as $entry) {
            if (!$this->requestsMatch($vary ?? '', $entry[0], $storedEnv)) {
                $entries[] = $entry;
            }
        }

        $headers = $this->persistResponse($response);
        unset($headers['age']);

        foreach ($this->options['private_headers'] as $h) {
            unset($headers[strtolower($h)]);
        }

        array_unshift($entries, [$storedEnv, $headers]);

        if (!$this->save($key, serialize($entries))) {
            throw new \RuntimeException('Unable to store the metadata.');
        }

        return $key;
    }

    /**
     * Returns content digest for $response.
     */
    protected function generateContentDigest(Response $response): string
    {
        return 'en'.hash('xxh128', $response->getContent());
    }

    /**
     * Invalidates all cache entries that match the request.
     *
     * @throws \RuntimeException
     */
    public function invalidate(Request $request): void
    {
        $modified = false;
        $key = $this->getCacheKey($request);

        $entries = [];
        foreach ($this->getMetadata($key) as $entry) {
            $response = $this->restoreResponse($entry[1]);
            if ($response->isFresh()) {
                $response->expire();
                $modified = true;
                $entries[] = [$entry[0], $this->persistResponse($response)];
            } else {
                $entries[] = $entry;
            }
        }

        if ($modified && !$this->save($key, serialize($entries))) {
            throw new \RuntimeException('Unable to store the metadata.');
        }
    }

    /**
     * Determines whether two Request HTTP header sets are non-varying based on
     * the vary response header value provided.
     *
     * @param string|null $vary A Response vary header
     * @param array       $env1 A Request HTTP header array
     * @param array       $env2 A Request HTTP header array
     */
    private function requestsMatch(?string $vary, array $env1, array $env2): bool
    {
        if ('' === ($vary ?? '')) {
            return true;
        }

        foreach (preg_split('/[\s,]+/', $vary) as $header) {
            $key = str_replace('_', '-', strtolower($header));
            $v1 = $env1[$key] ?? null;
            $v2 = $env2[$key] ?? null;
            if ($v1 !== $v2) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets all data associated with the given key.
     *
     * Use this method only if you know what you are doing.
     */
    private function getMetadata(string $key): array
    {
        if (!$entries = $this->load($key)) {
            return [];
        }

        return unserialize($entries, ['allowed_classes' => false]) ?: [];
    }

    /**
     * Purges data for the given URL.
     *
     * This method purges both the HTTP and the HTTPS version of the cache entry.
     *
     * @return bool true if the URL exists with either HTTP or HTTPS scheme and has been purged, false otherwise
     */
    public function purge(string $url): bool
    {
        $http = preg_replace('#^https:#', 'http:', $url);
        $https = preg_replace('#^http:#', 'https:', $url);

        $purgedHttp = $this->doPurge($http);
        $purgedHttps = $this->doPurge($https);

        return $purgedHttp || $purgedHttps;
    }

    /**
     * Purges data for the given URL.
     */
    private function doPurge(string $url): bool
    {
        $key = $this->getCacheKey(Request::create($url));
        if (isset($this->locks[$key])) {
            flock($this->locks[$key], \LOCK_UN);
            fclose($this->locks[$key]);
            unset($this->locks[$key]);
        }

        if (is_file($path = $this->getPath($key))) {
            unlink($path);

            return true;
        }

        return false;
    }

    /**
     * Loads data for the given key.
     */
    private function load(string $key): ?string
    {
        $path = $this->getPath($key);

        return is_file($path) && false !== ($contents = @file_get_contents($path)) ? $contents : null;
    }

    /**
     * Save data for the given key.
     */
    private function save(string $key, string $data, bool $overwrite = true): bool
    {
        $path = $this->getPath($key);

        if (!$overwrite && file_exists($path)) {
            return true;
        }

        if (isset($this->locks[$key])) {
            $fp = $this->locks[$key];
            @ftruncate($fp, 0);
            @fseek($fp, 0);
            $len = @fwrite($fp, $data);
            if (\strlen($data) !== $len) {
                @ftruncate($fp, 0);

                return false;
            }
        } else {
            if (!is_dir(\dirname($path)) && false === @mkdir(\dirname($path), 0o777, true) && !is_dir(\dirname($path))) {
                return false;
            }

            $tmpFile = tempnam(\dirname($path), basename($path));
            if (false === $fp = @fopen($tmpFile, 'w')) {
                @unlink($tmpFile);

                return false;
            }
            @fwrite($fp, $data);
            @fclose($fp);

            if ($data != file_get_contents($tmpFile)) {
                @unlink($tmpFile);

                return false;
            }

            if (false === @rename($tmpFile, $path)) {
                @unlink($tmpFile);

                return false;
            }
        }

        @chmod($path, 0o666 & ~umask());

        return true;
    }

    public function getPath(string $key): string
    {
        return $this->root.\DIRECTORY_SEPARATOR.substr($key, 0, 2).\DIRECTORY_SEPARATOR.substr($key, 2, 2).\DIRECTORY_SEPARATOR.substr($key, 4, 2).\DIRECTORY_SEPARATOR.substr($key, 6);
    }

    /**
     * Generates a cache key for the given Request.
     *
     * This method should return a key that must only depend on a
     * normalized version of the request URI.
     *
     * If the same URI can have more than one representation, based on some
     * headers, use a Vary header to indicate them, and each representation will
     * be stored independently under the same cache key.
     */
    protected function generateCacheKey(Request $request): string
    {
        $key = $request->getUri();

        if ('QUERY' === $request->getMethod()) {
            // add null byte to separate the URI from the body and avoid boundary collisions
            // which could lead to cache poisoning
            $key .= "\0".$request->getContent();
        }

        return 'md'.hash('sha256', $key);
    }

    /**
     * Returns a cache key for the given Request.
     */
    private function getCacheKey(Request $request): string
    {
        if (isset($this->keyCache[$request])) {
            return $this->keyCache[$request];
        }

        return $this->keyCache[$request] = $this->generateCacheKey($request);
    }

    /**
     * Persists the Request HTTP headers.
     */
    private function persistRequest(Request $request): array
    {
        return $request->headers->all();
    }

    /**
     * Persists the Response HTTP headers.
     */
    private function persistResponse(Response $response): array
    {
        $headers = $response->headers->all();
        $headers['X-Status'] = [$response->getStatusCode()];

        return $headers;
    }

    /**
     * Restores a Response from the HTTP headers and body.
     */
    private function restoreResponse(array $headers, ?string $path = null): ?Response
    {
        $status = $headers['X-Status'][0];
        unset($headers['X-Status']);
        $content = null;

        if (null !== $path) {
            $headers['X-Body-File'] = [$path];
            unset($headers['x-body-file']);

            if ($headers['X-Body-Eval'] ?? $headers['x-body-eval'] ?? false) {
                $content = file_get_contents($path);
                \assert(HttpCache::BODY_EVAL_BOUNDARY_LENGTH === 24);
                if (48 > \strlen($content) || substr($content, -24) !== substr($content, 0, 24)) {
                    return null;
                }
            }
        }

        return new Response($content, $status, $headers);
    }
}
