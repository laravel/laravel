<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Generator;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * UrlGenerator can generate a URL or a path for any route in the RouteCollection
 * based on the passed parameters.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class UrlGenerator implements UrlGeneratorInterface, ConfigurableRequirementsInterface
{
    private const QUERY_FRAGMENT_DECODED = [
        // RFC 3986 explicitly allows those in the query/fragment to reference other URIs unencoded
        '%2F' => '/',
        '%252F' => '%2F',
        '%3F' => '?',
        // reserved chars that have no special meaning for HTTP URIs in a query or fragment
        // this excludes esp. "&", "=" and also "+" because PHP would treat it as a space (form-encoded)
        '%40' => '@',
        '%3A' => ':',
        '%21' => '!',
        '%3B' => ';',
        '%2C' => ',',
        '%2A' => '*',
    ];

    protected ?bool $strictRequirements = true;

    /**
     * This array defines the characters (besides alphanumeric ones) that will not be percent-encoded in the path segment of the generated URL.
     *
     * PHP's rawurlencode() encodes all chars except "a-zA-Z0-9-._~" according to RFC 3986. But we want to allow some chars
     * to be used in their literal form (reasons below). Other chars inside the path must of course be encoded, e.g.
     * "?" and "#" (would be interpreted wrongly as query and fragment identifier),
     * "'" and """ (are used as delimiters in HTML).
     */
    protected array $decodedChars = [
        // the slash can be used to designate a hierarchical structure and we want allow using it with this meaning
        // some webservers don't allow the slash in encoded form in the path for security reasons anyway
        // see http://stackoverflow.com/questions/4069002/http-400-if-2f-part-of-get-url-in-jboss
        '%2F' => '/',
        '%252F' => '%2F',
        // the following chars are general delimiters in the URI specification but have only special meaning in the authority component
        // so they can safely be used in the path in unencoded form
        '%40' => '@',
        '%3A' => ':',
        // these chars are only sub-delimiters that have no predefined meaning and can therefore be used literally
        // so URI producing applications can use these chars to delimit subcomponents in a path segment without being encoded for better readability
        '%3B' => ';',
        '%2C' => ',',
        '%3D' => '=',
        '%2B' => '+',
        '%21' => '!',
        '%2A' => '*',
        '%7C' => '|',
    ];

    public function __construct(
        protected RouteCollection $routes,
        protected RequestContext $context,
        protected ?LoggerInterface $logger = null,
        private ?string $defaultLocale = null,
    ) {
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function setStrictRequirements(?bool $enabled): void
    {
        $this->strictRequirements = $enabled;
    }

    public function isStrictRequirements(): ?bool
    {
        return $this->strictRequirements;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $route = null;
        $locale = $parameters['_locale'] ?? $this->context->getParameter('_locale') ?: $this->defaultLocale;

        if (null !== $locale) {
            do {
                $route = $this->routes->get($name.'.'.$locale);
                if ($route && ($route->getDefault('_canonical_route') === $name || $this->routes->getAlias($name.'.'.$locale))) {
                    break;
                }
            } while (false !== $locale = strstr($locale, '_', true));
        }

        if (null === $route ??= $this->routes->get($name)) {
            throw new RouteNotFoundException(\sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        // the Route has a cache of its own and is not recompiled as long as it does not get modified
        $compiledRoute = $route->compile();

        $defaults = $route->getDefaults();
        $variables = $compiledRoute->getVariables();

        if (isset($defaults['_canonical_route']) && isset($defaults['_locale'])) {
            if (!\in_array('_locale', $variables, true)) {
                unset($parameters['_locale']);
            } elseif (!isset($parameters['_locale'])) {
                $parameters['_locale'] = $defaults['_locale'];
            }
        }

        return $this->doGenerate($variables, $defaults, $route->getRequirements(), $compiledRoute->getTokens(), $parameters, $name, $referenceType, $compiledRoute->getHostTokens(), $route->getSchemes());
    }

    /**
     * @throws MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                             it does not match the requirement
     */
    protected function doGenerate(array $variables, array $defaults, array $requirements, array $tokens, array $parameters, string $name, int $referenceType, array $hostTokens, array $requiredSchemes = []): string
    {
        $queryParameters = [];

        if (isset($parameters['_query'])) {
            if (\is_array($parameters['_query'])) {
                $queryParameters = $parameters['_query'];
                unset($parameters['_query']);
            } else {
                trigger_deprecation('symfony/routing', '7.4', 'Parameter "_query" is reserved for passing an array of query parameters. Passing a scalar value is deprecated and will throw an exception in Symfony 8.0.');
                // throw new InvalidParameterException('Parameter "_query" must be an array of query parameters.');
            }
        }

        $variables = array_flip($variables);
        $mergedParams = array_replace($defaults, $this->context->getParameters(), $parameters);

        // all params must be given
        if ($diff = array_diff_key($variables, $mergedParams)) {
            throw new MissingMandatoryParametersException($name, array_keys($diff));
        }

        $url = '';
        $optional = true;
        $message = 'Parameter "{parameter}" for route "{route}" must match "{expected}" ("{given}" given) to generate a corresponding URL.';
        foreach ($tokens as $token) {
            if ('variable' === $token[0]) {
                $varName = $token[3];
                // variable is not important by default
                $important = $token[5] ?? false;

                if (!$optional || $important || !\array_key_exists($varName, $defaults) || (null !== $mergedParams[$varName] && (string) $mergedParams[$varName] !== (string) $defaults[$varName])) {
                    // check requirement (while ignoring look-around patterns)
                    if (null !== $this->strictRequirements && !preg_match('#^(?:'.preg_replace('/\(\?(?:=|<=|!|<!)((?:[^()\\\\]+|\\\\.|\((?1)\))*)\)/', '', $token[2]).')$#i'.(empty($token[4]) ? '' : 'u'), $mergedParams[$token[3]] ?? '')) {
                        if ($this->strictRequirements) {
                            throw new InvalidParameterException(strtr($message, ['{parameter}' => $varName, '{route}' => $name, '{expected}' => $token[2], '{given}' => $mergedParams[$varName]]));
                        }

                        $this->logger?->error($message, ['parameter' => $varName, 'route' => $name, 'expected' => $token[2], 'given' => $mergedParams[$varName]]);

                        return '';
                    }

                    $url = $token[1].$mergedParams[$varName].$url;
                    $optional = false;
                }
            } else {
                // static text
                $url = $token[1].$url;
                $optional = false;
            }
        }

        if ('' === $url) {
            $url = '/';
        }

        // the contexts base URL is already encoded (see Symfony\Component\HttpFoundation\Request)
        $url = strtr(rawurlencode($url), $this->decodedChars);

        // the path segments "." and ".." are interpreted as relative reference when resolving a URI; see http://tools.ietf.org/html/rfc3986#section-3.3
        // so we need to encode them as they are not used for this purpose here
        // otherwise we would generate a URI that, when followed by a user agent (e.g. browser), does not match this route
        if (str_contains($url, '/.')) {
            $segments = explode('/', $url);
            foreach ($segments as $i => $segment) {
                if ('.' === $segment) {
                    $segments[$i] = '%2E';
                } elseif ('..' === $segment) {
                    $segments[$i] = '%2E%2E';
                }
            }
            $url = implode('/', $segments);
        }

        $schemeAuthority = '';
        $host = $this->context->getHost();
        $scheme = $this->context->getScheme();

        if ($requiredSchemes) {
            if (!\in_array($scheme, $requiredSchemes, true)) {
                $referenceType = self::ABSOLUTE_URL;
                $scheme = current($requiredSchemes);
            }
        }

        if ($hostTokens) {
            $routeHost = '';
            foreach ($hostTokens as $token) {
                if ('variable' === $token[0]) {
                    // check requirement (while ignoring look-around patterns)
                    if (null !== $this->strictRequirements && !preg_match('#^(?:'.preg_replace('/\(\?(?:=|<=|!|<!)((?:[^()\\\\]+|\\\\.|\((?1)\))*)\)/', '', $token[2]).')$#i'.(empty($token[4]) ? '' : 'u'), $mergedParams[$token[3]])) {
                        if ($this->strictRequirements) {
                            throw new InvalidParameterException(strtr($message, ['{parameter}' => $token[3], '{route}' => $name, '{expected}' => $token[2], '{given}' => $mergedParams[$token[3]]]));
                        }

                        $this->logger?->error($message, ['parameter' => $token[3], 'route' => $name, 'expected' => $token[2], 'given' => $mergedParams[$token[3]]]);

                        return '';
                    }

                    $routeHost = $token[1].$mergedParams[$token[3]].$routeHost;
                } else {
                    $routeHost = $token[1].$routeHost;
                }
            }

            if ($routeHost !== $host) {
                $host = $routeHost;
                if (self::ABSOLUTE_URL !== $referenceType) {
                    $referenceType = self::NETWORK_PATH;
                }
            }
        }

        if (self::ABSOLUTE_URL === $referenceType || self::NETWORK_PATH === $referenceType) {
            if ('' !== $host || ('' !== $scheme && 'http' !== $scheme && 'https' !== $scheme)) {
                $port = '';
                if ('http' === $scheme && 80 !== $this->context->getHttpPort()) {
                    $port = ':'.$this->context->getHttpPort();
                } elseif ('https' === $scheme && 443 !== $this->context->getHttpsPort()) {
                    $port = ':'.$this->context->getHttpsPort();
                }

                $schemeAuthority = self::NETWORK_PATH === $referenceType || '' === $scheme ? '//' : "$scheme://";
                $schemeAuthority .= $host.$port;
            }
        }

        if (self::RELATIVE_PATH === $referenceType) {
            $url = self::getRelativePath($this->context->getPathInfo(), $url);
        } else {
            $url = $schemeAuthority.$this->context->getBaseUrl().$url;
        }

        // add a query string if needed
        $extra = array_udiff_assoc(array_diff_key($parameters, $variables), $defaults, static fn ($a, $b) => $a == $b ? 0 : 1);
        $extra = array_replace($extra, $queryParameters);

        array_walk_recursive($extra, $caster = static function (&$v) use (&$caster) {
            if (\is_object($v)) {
                if ($vars = get_object_vars($v)) {
                    array_walk_recursive($vars, $caster);
                    $v = $vars;
                } elseif ($v instanceof \Stringable) {
                    $v = (string) $v;
                }
            }
        });

        // extract fragment
        $fragment = $defaults['_fragment'] ?? '';

        if (isset($extra['_fragment'])) {
            $fragment = $extra['_fragment'];
            unset($extra['_fragment']);
        }

        if ($extra && $query = http_build_query($extra, '', '&', \PHP_QUERY_RFC3986)) {
            $url .= '?'.strtr($query, self::QUERY_FRAGMENT_DECODED);
        }

        if ('' !== $fragment) {
            $url .= '#'.strtr(rawurlencode($fragment), self::QUERY_FRAGMENT_DECODED);
        }

        return $url;
    }

    /**
     * Returns the target path as relative reference from the base path.
     *
     * Only the URIs path component (no schema, host etc.) is relevant and must be given, starting with a slash.
     * Both paths must be absolute and not contain relative parts.
     * Relative URLs from one resource to another are useful when generating self-contained downloadable document archives.
     * Furthermore, they can be used to reduce the link size in documents.
     *
     * Example target paths, given a base path of "/a/b/c/d":
     * - "/a/b/c/d"     -> ""
     * - "/a/b/c/"      -> "./"
     * - "/a/b/"        -> "../"
     * - "/a/b/c/other" -> "other"
     * - "/a/x/y"       -> "../../x/y"
     *
     * @param string $basePath   The base path
     * @param string $targetPath The target path
     */
    public static function getRelativePath(string $basePath, string $targetPath): string
    {
        if ($basePath === $targetPath) {
            return '';
        }

        $sourceDirs = explode('/', isset($basePath[0]) && '/' === $basePath[0] ? substr($basePath, 1) : $basePath);
        $targetDirs = explode('/', isset($targetPath[0]) && '/' === $targetPath[0] ? substr($targetPath, 1) : $targetPath);
        array_pop($sourceDirs);
        $targetFile = array_pop($targetDirs);

        foreach ($sourceDirs as $i => $dir) {
            if (isset($targetDirs[$i]) && $dir === $targetDirs[$i]) {
                unset($sourceDirs[$i], $targetDirs[$i]);
            } else {
                break;
            }
        }

        $targetDirs[] = $targetFile;
        $path = str_repeat('../', \count($sourceDirs)).implode('/', $targetDirs);

        // A reference to the same base directory or an empty subdirectory must be prefixed with "./".
        // This also applies to a segment with a colon character (e.g., "file:colon") that cannot be used
        // as the first segment of a relative-path reference, as it would be mistaken for a scheme name
        // (see http://tools.ietf.org/html/rfc3986#section-4.2).
        return '' === $path || '/' === $path[0]
            || false !== ($colonPos = strpos($path, ':')) && ($colonPos < ($slashPos = strpos($path, '/')) || false === $slashPos)
            ? "./$path" : $path;
    }
}
