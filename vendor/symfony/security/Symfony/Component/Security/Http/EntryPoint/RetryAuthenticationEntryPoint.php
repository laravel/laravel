<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\EntryPoint;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * RetryAuthenticationEntryPoint redirects URL based on the configured scheme.
 *
 * This entry point is not intended to work with HTTP post requests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RetryAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $httpPort;
    private $httpsPort;

    public function __construct($httpPort = 80, $httpsPort = 443)
    {
        $this->httpPort = $httpPort;
        $this->httpsPort = $httpsPort;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $scheme = $request->isSecure() ? 'http' : 'https';
        if ('http' === $scheme && 80 != $this->httpPort) {
            $port = ':'.$this->httpPort;
        } elseif ('https' === $scheme && 443 != $this->httpsPort) {
            $port = ':'.$this->httpsPort;
        } else {
            $port = '';
        }

        $qs = $request->getQueryString();
        if (null !== $qs) {
            $qs = '?'.$qs;
        }

        $url = $scheme.'://'.$request->getHost().$port.$request->getBaseUrl().$request->getPathInfo().$qs;

        return new RedirectResponse($url, 301);
    }
}
