<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * RedirectResponse represents an HTTP response doing a redirect.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class RedirectResponse extends Response
{
    protected $targetUrl;

    /**
     * Creates a redirect response so that it conforms to the rules defined for a redirect status code.
     *
     * @param string  $url     The URL to redirect to
     * @param integer $status  The status code (302 by default)
     * @param array   $headers The headers (Location is always set to the given url)
     *
     * @throws \InvalidArgumentException
     *
     * @see http://tools.ietf.org/html/rfc2616#section-10.3
     *
     * @api
     */
    public function __construct($url, $status = 302, $headers = array())
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);

        if (!$this->isRedirect()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function create($url = '', $status = 302, $headers = array())
    {
        return new static($url, $status, $headers);
    }

    /**
     * Returns the target URL.
     *
     * @return string target URL
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * Sets the redirect target of this response.
     *
     * @param string  $url     The URL to redirect to
     *
     * @return RedirectResponse The current response.
     *
     * @throws \InvalidArgumentException
     */
    public function setTargetUrl($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;

        $this->setContent(
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        $this->headers->set('Location', $url);

        return $this;
    }
}
