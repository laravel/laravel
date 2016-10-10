<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Encapsulates the logic needed to create sub-requests, redirect the user, and match URLs.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpUtils
{
    private $urlGenerator;
    private $urlMatcher;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface                       $urlGenerator A UrlGeneratorInterface instance
     * @param UrlMatcherInterface|RequestMatcherInterface $urlMatcher   The URL or Request matcher
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(UrlGeneratorInterface $urlGenerator = null, $urlMatcher = null)
    {
        $this->urlGenerator = $urlGenerator;
        if ($urlMatcher !== null && !$urlMatcher instanceof UrlMatcherInterface && !$urlMatcher instanceof RequestMatcherInterface) {
            throw new \InvalidArgumentException('Matcher must either implement UrlMatcherInterface or RequestMatcherInterface.');
        }
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * Creates a redirect Response.
     *
     * @param Request $request A Request instance
     * @param string  $path    A path (an absolute path (/foo), an absolute URL (http://...), or a route name (foo))
     * @param int     $status  The status code
     *
     * @return RedirectResponse A RedirectResponse instance
     */
    public function createRedirectResponse(Request $request, $path, $status = 302)
    {
        return new RedirectResponse($this->generateUri($request, $path), $status);
    }

    /**
     * Creates a Request.
     *
     * @param Request $request The current Request instance
     * @param string  $path    A path (an absolute path (/foo), an absolute URL (http://...), or a route name (foo))
     *
     * @return Request A Request instance
     */
    public function createRequest(Request $request, $path)
    {
        $newRequest = Request::create($this->generateUri($request, $path), 'get', array(), $request->cookies->all(), array(), $request->server->all());
        if ($request->hasSession()) {
            $newRequest->setSession($request->getSession());
        }

        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $newRequest->attributes->set(SecurityContextInterface::AUTHENTICATION_ERROR, $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR));
        }
        if ($request->attributes->has(SecurityContextInterface::ACCESS_DENIED_ERROR)) {
            $newRequest->attributes->set(SecurityContextInterface::ACCESS_DENIED_ERROR, $request->attributes->get(SecurityContextInterface::ACCESS_DENIED_ERROR));
        }
        if ($request->attributes->has(SecurityContextInterface::LAST_USERNAME)) {
            $newRequest->attributes->set(SecurityContextInterface::LAST_USERNAME, $request->attributes->get(SecurityContextInterface::LAST_USERNAME));
        }

        return $newRequest;
    }

    /**
     * Checks that a given path matches the Request.
     *
     * @param Request $request A Request instance
     * @param string  $path    A path (an absolute path (/foo), an absolute URL (http://...), or a route name (foo))
     *
     * @return bool    true if the path is the same as the one from the Request, false otherwise
     */
    public function checkRequestPath(Request $request, $path)
    {
        if ('/' !== $path[0]) {
            try {
                // matching a request is more powerful than matching a URL path + context, so try that first
                if ($this->urlMatcher instanceof RequestMatcherInterface) {
                    $parameters = $this->urlMatcher->matchRequest($request);
                } else {
                    $parameters = $this->urlMatcher->match($request->getPathInfo());
                }

                return $path === $parameters['_route'];
            } catch (MethodNotAllowedException $e) {
                return false;
            } catch (ResourceNotFoundException $e) {
                return false;
            }
        }

        return $path === rawurldecode($request->getPathInfo());
    }

    /**
     * Generates a URI, based on the given path or absolute URL.
     *
     * @param Request $request A Request instance
     * @param string  $path    A path (an absolute path (/foo), an absolute URL (http://...), or a route name (foo))
     *
     * @return string An absolute URL
     *
     * @throws \LogicException
     */
    public function generateUri($request, $path)
    {
        if (0 === strpos($path, 'http') || !$path) {
            return $path;
        }

        if ('/' === $path[0]) {
            return $request->getUriForPath($path);
        }

        if (null === $this->urlGenerator) {
            throw new \LogicException('You must provide a UrlGeneratorInterface instance to be able to use routes.');
        }

        $url = $this->urlGenerator->generate($path, $request->attributes->all(), UrlGeneratorInterface::ABSOLUTE_URL);

        // unnecessary query string parameters must be removed from URL
        // (ie. query parameters that are presents in $attributes)
        // fortunately, they all are, so we have to remove entire query string
        $position = strpos($url, '?');
        if (false !== $position) {
            $url = substr($url, 0, $position);
        }

        return $url;
    }
}
