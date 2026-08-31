<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\UriInterface;

/**
 * Resolves a URI reference in the context of a base URI and the opposite way.
 *
 * @author Tobias Schultze
 *
 * @see https://datatracker.ietf.org/doc/html/rfc3986#section-5
 */
final class UriResolver
{
    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-5.2.4
     */
    public static function removeDotSegments(string $path): string
    {
        if ($path === '' || $path === '/') {
            return $path;
        }

        $results = [];
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($results);
            } elseif ($segment !== '.') {
                $results[] = $segment;
            }
        }

        $newPath = implode('/', $results);

        if ($path[0] === '/' && (!isset($newPath[0]) || $newPath[0] !== '/')) {
            // Re-add the leading slash if necessary for cases like "/.."
            $newPath = '/'.$newPath;
        } elseif ($newPath !== '' && ($segment === '.' || $segment === '..')) {
            // Add the trailing slash if necessary
            // If newPath is not empty, then $segment must be set and is the last segment from the foreach
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * Converts the relative URI into a new URI that is resolved against the base URI.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-5.2
     */
    public static function resolve(UriInterface $base, UriInterface $rel): UriInterface
    {
        if ((string) $rel === '') {
            // we can simply return the same base URI instance for this same-document reference
            return $base;
        }

        if ($rel->getScheme() != '') {
            return $rel->withPath(self::removeDotSegments($rel->getPath()));
        }

        if ($rel->getAuthority() != '') {
            return $rel
                ->withScheme($base->getScheme())
                ->withPath(self::removeDotSegments($rel->getPath()));
        }

        if ($rel->getPath() === '') {
            $targetPath = $base->getPath();
            $targetQuery = $rel->getQuery() != '' ? $rel->getQuery() : $base->getQuery();
        } else {
            if ($rel->getPath()[0] === '/') {
                $targetPath = $rel->getPath();
            } else {
                if ($base->getAuthority() != '' && $base->getPath() === '') {
                    $targetPath = '/'.$rel->getPath();
                } else {
                    $lastSlashPos = strrpos($base->getPath(), '/');
                    if ($lastSlashPos === false) {
                        $targetPath = $rel->getPath();
                    } else {
                        $targetPath = substr($base->getPath(), 0, $lastSlashPos + 1).$rel->getPath();
                    }
                }
            }
            $targetPath = self::removeDotSegments($targetPath);
            $targetQuery = $rel->getQuery();
        }

        return $base
            ->withPath($targetPath)
            ->withQuery($targetQuery)
            ->withFragment($rel->getFragment());
    }

    /**
     * Returns the target URI as a relative reference from the base URI.
     *
     * This method is the counterpart to resolve():
     *
     *    (string) $target === (string) UriResolver::resolve($base, UriResolver::relativize($base, $target))
     *
     * One use-case is to use the current request URI as base URI and then generate relative links in your documents
     * to reduce the document size or offer self-contained downloadable document archives.
     *
     *    $base = new Uri('http://example.com/a/b/');
     *    echo UriResolver::relativize($base, new Uri('http://example.com/a/b/c'));  // prints 'c'.
     *    echo UriResolver::relativize($base, new Uri('http://example.com/a/x/y'));  // prints '../x/y'.
     *    echo UriResolver::relativize($base, new Uri('http://example.com/a/b/?q')); // prints '?q'.
     *    echo UriResolver::relativize($base, new Uri('http://example.org/a/b/'));   // prints '//example.org/a/b/'.
     *
     * This method also accepts a target that is already relative and will try to relativize it further. Only a
     * relative-path reference will be returned as-is.
     *
     *    echo UriResolver::relativize($base, new Uri('/a/b/c'));  // prints 'c' as well
     */
    public static function relativize(UriInterface $base, UriInterface $target): UriInterface
    {
        if ($target->getScheme() !== ''
            && ($base->getScheme() !== $target->getScheme() || $target->getAuthority() === '' && $base->getAuthority() !== '')
        ) {
            return $target;
        }

        if (Uri::isRelativePathReference($target)) {
            // As the target is already highly relative we return it as-is. It would be possible to resolve
            // the target with `$target = self::resolve($base, $target);` and then try make it more relative
            // by removing a duplicate query. But let's not do that automatically.
            return $target;
        }

        if ($target->getAuthority() !== '' && $base->getAuthority() !== $target->getAuthority()) {
            return $target->withScheme('');
        }

        // We must remove the path before removing the authority because if the path starts with two slashes, the URI
        // would turn invalid. And we also cannot set a relative path before removing the authority, as that is also
        // invalid.
        $emptyPathUri = $target->withScheme('')->withPath('')->withUserInfo('')->withPort(null)->withHost('');

        if ($base->getPath() !== $target->getPath()) {
            return $emptyPathUri->withPath(self::getRelativePath($base, $target));
        }

        if ($base->getQuery() === $target->getQuery()) {
            // Only the target fragment is left. And it must be returned even if base and target fragment are the same.
            return $emptyPathUri->withQuery('');
        }

        // If the base URI has a query but the target has none, we cannot return an empty path reference as it would
        // inherit the base query component when resolving.
        if ($target->getQuery() === '') {
            $segments = explode('/', $target->getPath());
            /** @var string $lastSegment */
            $lastSegment = end($segments);

            return $emptyPathUri->withPath($lastSegment === '' ? './' : $lastSegment);
        }

        return $emptyPathUri;
    }

    private static function getRelativePath(UriInterface $base, UriInterface $target): string
    {
        $sourceSegments = explode('/', $base->getPath());
        $targetSegments = explode('/', $target->getPath());
        array_pop($sourceSegments);
        $targetLastSegment = array_pop($targetSegments);
        foreach ($sourceSegments as $i => $segment) {
            if (isset($targetSegments[$i]) && $segment === $targetSegments[$i]) {
                unset($sourceSegments[$i], $targetSegments[$i]);
            } else {
                break;
            }
        }
        $targetSegments[] = $targetLastSegment;
        $relativePath = str_repeat('../', count($sourceSegments)).implode('/', $targetSegments);

        // A reference to am empty last segment or an empty first sub-segment must be prefixed with "./".
        // This also applies to a segment with a colon character (e.g., "file:colon") that cannot be used
        // as the first segment of a relative-path reference, as it would be mistaken for a scheme name.
        if ('' === $relativePath || false !== strpos(explode('/', $relativePath, 2)[0], ':')) {
            $relativePath = "./$relativePath";
        } elseif ('/' === $relativePath[0]) {
            if ($base->getAuthority() != '' && $base->getPath() === '') {
                // In this case an extra slash is added by resolve() automatically. So we must not add one here.
                $relativePath = ".$relativePath";
            } else {
                $relativePath = "./$relativePath";
            }
        }

        return $relativePath;
    }

    private function __construct()
    {
        // cannot be instantiated
    }
}
