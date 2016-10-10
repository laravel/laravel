<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\BrowserKit;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Process\PhpProcess;

/**
 * Client simulates a browser.
 *
 * To make the actual request, you need to implement the doRequest() method.
 *
 * If you want to be able to run requests in their own process (insulated flag),
 * you need to also implement the getScript() method.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
abstract class Client
{
    protected $history;
    protected $cookieJar;
    protected $server = array();
    protected $internalRequest;
    protected $request;
    protected $internalResponse;
    protected $response;
    protected $crawler;
    protected $insulated = false;
    protected $redirect;
    protected $followRedirects = true;

    private $maxRedirects = -1;
    private $redirectCount = 0;
    private $isMainRequest = true;

    /**
     * Constructor.
     *
     * @param array     $server    The server parameters (equivalent of $_SERVER)
     * @param History   $history   A History instance to store the browser history
     * @param CookieJar $cookieJar A CookieJar instance to store the cookies
     *
     * @api
     */
    public function __construct(array $server = array(), History $history = null, CookieJar $cookieJar = null)
    {
        $this->setServerParameters($server);
        $this->history = $history ?: new History();
        $this->cookieJar = $cookieJar ?: new CookieJar();
    }

    /**
     * Sets whether to automatically follow redirects or not.
     *
     * @param bool    $followRedirect Whether to follow redirects
     *
     * @api
     */
    public function followRedirects($followRedirect = true)
    {
        $this->followRedirects = (bool) $followRedirect;
    }

    /**
     * Sets the maximum number of requests that crawler can follow.
     *
     * @param int     $maxRedirects
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $maxRedirects < 0 ? -1 : $maxRedirects;
        $this->followRedirects = -1 != $this->maxRedirects;
    }

    /**
     * Sets the insulated flag.
     *
     * @param bool    $insulated Whether to insulate the requests or not
     *
     * @throws \RuntimeException When Symfony Process Component is not installed
     *
     * @api
     */
    public function insulate($insulated = true)
    {
        if ($insulated && !class_exists('Symfony\\Component\\Process\\Process')) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('Unable to isolate requests as the Symfony Process Component is not installed.');
            // @codeCoverageIgnoreEnd
        }

        $this->insulated = (bool) $insulated;
    }

    /**
     * Sets server parameters.
     *
     * @param array $server An array of server parameters
     *
     * @api
     */
    public function setServerParameters(array $server)
    {
        $this->server = array_merge(array(
            'HTTP_HOST'       => 'localhost',
            'HTTP_USER_AGENT' => 'Symfony2 BrowserKit',
        ), $server);
    }

    /**
     * Sets single server parameter.
     *
     * @param string $key   A key of the parameter
     * @param string $value A value of the parameter
     */
    public function setServerParameter($key, $value)
    {
        $this->server[$key] = $value;
    }

    /**
     * Gets single server parameter for specified key.
     *
     * @param string $key     A key of the parameter to get
     * @param string $default A default value when key is undefined
     *
     * @return string A value of the parameter
     */
    public function getServerParameter($key, $default = '')
    {
        return (isset($this->server[$key])) ? $this->server[$key] : $default;
    }

    /**
     * Returns the History instance.
     *
     * @return History A History instance
     *
     * @api
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Returns the CookieJar instance.
     *
     * @return CookieJar A CookieJar instance
     *
     * @api
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    /**
     * Returns the current Crawler instance.
     *
     * @return Crawler|null A Crawler instance
     *
     * @api
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * Returns the current BrowserKit Response instance.
     *
     * @return Response|null A BrowserKit Response instance
     *
     * @api
     */
    public function getInternalResponse()
    {
        return $this->internalResponse;
    }

    /**
     * Returns the current origin response instance.
     *
     * The origin response is the response instance that is returned
     * by the code that handles requests.
     *
     * @return object|null A response instance
     *
     * @see doRequest
     *
     * @api
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the current BrowserKit Request instance.
     *
     * @return Request|null A BrowserKit Request instance
     *
     * @api
     */
    public function getInternalRequest()
    {
        return $this->internalRequest;
    }

    /**
     * Returns the current origin Request instance.
     *
     * The origin request is the request instance that is sent
     * to the code that handles requests.
     *
     * @return object|null A Request instance
     *
     * @see doRequest
     *
     * @api
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Clicks on a given link.
     *
     * @param Link $link A Link instance
     *
     * @return Crawler
     *
     * @api
     */
    public function click(Link $link)
    {
        if ($link instanceof Form) {
            return $this->submit($link);
        }

        return $this->request($link->getMethod(), $link->getUri());
    }

    /**
     * Submits a form.
     *
     * @param Form  $form   A Form instance
     * @param array $values An array of form field values
     *
     * @return Crawler
     *
     * @api
     */
    public function submit(Form $form, array $values = array())
    {
        $form->setValues($values);

        return $this->request($form->getMethod(), $form->getUri(), $form->getPhpValues(), $form->getPhpFiles());
    }

    /**
     * Calls a URI.
     *
     * @param string  $method        The request method
     * @param string  $uri           The URI to fetch
     * @param array   $parameters    The Request parameters
     * @param array   $files         The files
     * @param array   $server        The server parameters (HTTP headers are referenced with a HTTP_ prefix as PHP does)
     * @param string  $content       The raw body data
     * @param bool    $changeHistory Whether to update the history or not (only used internally for back(), forward(), and reload())
     *
     * @return Crawler
     *
     * @api
     */
    public function request($method, $uri, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
    {
        if ($this->isMainRequest) {
            $this->redirectCount = 0;
        } else {
            ++$this->redirectCount;
        }

        $uri = $this->getAbsoluteUri($uri);

        if (!empty($server['HTTP_HOST'])) {
            $uri = preg_replace('{^(https?\://)'.preg_quote($this->extractHost($uri)).'}', '${1}'.$server['HTTP_HOST'], $uri);
        }

        if (isset($server['HTTPS'])) {
            $uri = preg_replace('{^'.parse_url($uri, PHP_URL_SCHEME).'}', $server['HTTPS'] ? 'https' : 'http', $uri);
        }

        $server = array_merge($this->server, $server);

        if (!$this->history->isEmpty()) {
            $server['HTTP_REFERER'] = $this->history->current()->getUri();
        }

        $server['HTTP_HOST'] = $this->extractHost($uri);
        $server['HTTPS'] = 'https' == parse_url($uri, PHP_URL_SCHEME);

        $this->internalRequest = new Request($uri, $method, $parameters, $files, $this->cookieJar->allValues($uri), $server, $content);

        $this->request = $this->filterRequest($this->internalRequest);

        if (true === $changeHistory) {
            $this->history->add($this->internalRequest);
        }

        if ($this->insulated) {
            $this->response = $this->doRequestInProcess($this->request);
        } else {
            $this->response = $this->doRequest($this->request);
        }

        $this->internalResponse = $this->filterResponse($this->response);

        $this->cookieJar->updateFromResponse($this->internalResponse, $uri);

        $status = $this->internalResponse->getStatus();

        if ($status >= 300 && $status < 400) {
            $this->redirect = $this->internalResponse->getHeader('Location');
        } else {
            $this->redirect = null;
        }

        if ($this->followRedirects && $this->redirect) {
            return $this->crawler = $this->followRedirect();
        }

        return $this->crawler = $this->createCrawlerFromContent($this->internalRequest->getUri(), $this->internalResponse->getContent(), $this->internalResponse->getHeader('Content-Type'));
    }

    /**
     * Makes a request in another process.
     *
     * @param object $request An origin request instance
     *
     * @return object An origin response instance
     *
     * @throws \RuntimeException When processing returns exit code
     */
    protected function doRequestInProcess($request)
    {
        // We set the TMPDIR (for Macs) and TEMP (for Windows), because on these platforms the temp directory changes based on the user.
        $process = new PhpProcess($this->getScript($request), null, array('TMPDIR' => sys_get_temp_dir(), 'TEMP' => sys_get_temp_dir()));
        $process->run();

        if (!$process->isSuccessful() || !preg_match('/^O\:\d+\:/', $process->getOutput())) {
            throw new \RuntimeException(sprintf('OUTPUT: %s ERROR OUTPUT: %s', $process->getOutput(), $process->getErrorOutput()));
        }

        return unserialize($process->getOutput());
    }

    /**
     * Makes a request.
     *
     * @param object $request An origin request instance
     *
     * @return object An origin response instance
     */
    abstract protected function doRequest($request);

    /**
     * Returns the script to execute when the request must be insulated.
     *
     * @param object $request An origin request instance
     *
     * @throws \LogicException When this abstract class is not implemented
     */
    protected function getScript($request)
    {
        // @codeCoverageIgnoreStart
        throw new \LogicException('To insulate requests, you need to override the getScript() method.');
        // @codeCoverageIgnoreEnd
    }

    /**
     * Filters the BrowserKit request to the origin one.
     *
     * @param Request $request The BrowserKit Request to filter
     *
     * @return object An origin request instance
     */
    protected function filterRequest(Request $request)
    {
        return $request;
    }

    /**
     * Filters the origin response to the BrowserKit one.
     *
     * @param object $response The origin response to filter
     *
     * @return Response An BrowserKit Response instance
     */
    protected function filterResponse($response)
    {
        return $response;
    }

    /**
     * Creates a crawler.
     *
     * This method returns null if the DomCrawler component is not available.
     *
     * @param string $uri     A URI
     * @param string $content Content for the crawler to use
     * @param string $type    Content type
     *
     * @return Crawler|null
     */
    protected function createCrawlerFromContent($uri, $content, $type)
    {
        if (!class_exists('Symfony\Component\DomCrawler\Crawler')) {
            return;
        }

        $crawler = new Crawler(null, $uri);
        $crawler->addContent($content, $type);

        return $crawler;
    }

    /**
     * Goes back in the browser history.
     *
     * @return Crawler
     *
     * @api
     */
    public function back()
    {
        return $this->requestFromRequest($this->history->back(), false);
    }

    /**
     * Goes forward in the browser history.
     *
     * @return Crawler
     *
     * @api
     */
    public function forward()
    {
        return $this->requestFromRequest($this->history->forward(), false);
    }

    /**
     * Reloads the current browser.
     *
     * @return Crawler
     *
     * @api
     */
    public function reload()
    {
        return $this->requestFromRequest($this->history->current(), false);
    }

    /**
     * Follow redirects?
     *
     * @return Crawler
     *
     * @throws \LogicException If request was not a redirect
     *
     * @api
     */
    public function followRedirect()
    {
        if (empty($this->redirect)) {
            throw new \LogicException('The request was not redirected.');
        }

        if (-1 !== $this->maxRedirects) {
            if ($this->redirectCount > $this->maxRedirects) {
                throw new \LogicException(sprintf('The maximum number (%d) of redirections was reached.', $this->maxRedirects));
            }
        }

        $request = $this->internalRequest;

        if (in_array($this->internalResponse->getStatus(), array(302, 303))) {
            $method = 'get';
            $files = array();
            $content = null;
        } else {
            $method = $request->getMethod();
            $files = $request->getFiles();
            $content = $request->getContent();
        }

        if ('get' === strtolower($method)) {
            // Don't forward parameters for GET request as it should reach the redirection URI
            $parameters = array();
        } else {
            $parameters = $request->getParameters();
        }

        $server = $request->getServer();
        $server = $this->updateServerFromUri($server, $this->redirect);

        $this->isMainRequest = false;

        $response = $this->request($method, $this->redirect, $parameters, $files, $server, $content);

        $this->isMainRequest = true;

        return $response;
    }

    /**
     * Restarts the client.
     *
     * It flushes history and all cookies.
     *
     * @api
     */
    public function restart()
    {
        $this->cookieJar->clear();
        $this->history->clear();
    }

    /**
     * Takes a URI and converts it to absolute if it is not already absolute.
     *
     * @param string $uri A URI
     *
     * @return string An absolute URI
     */
    protected function getAbsoluteUri($uri)
    {
        // already absolute?
        if (0 === strpos($uri, 'http')) {
            return $uri;
        }

        if (!$this->history->isEmpty()) {
            $currentUri = $this->history->current()->getUri();
        } else {
            $currentUri = sprintf('http%s://%s/',
                isset($this->server['HTTPS']) ? 's' : '',
                isset($this->server['HTTP_HOST']) ? $this->server['HTTP_HOST'] : 'localhost'
            );
        }

        // protocol relative URL
        if (0 === strpos($uri, '//')) {
            return parse_url($currentUri, PHP_URL_SCHEME).':'.$uri;
        }

        // anchor?
        if (!$uri || '#' == $uri[0]) {
            return preg_replace('/#.*?$/', '', $currentUri).$uri;
        }

        if ('/' !== $uri[0]) {
            $path = parse_url($currentUri, PHP_URL_PATH);

            if ('/' !== substr($path, -1)) {
                $path = substr($path, 0, strrpos($path, '/') + 1);
            }

            $uri = $path.$uri;
        }

        return preg_replace('#^(.*?//[^/]+)\/.*$#', '$1', $currentUri).$uri;
    }

    /**
     * Makes a request from a Request object directly.
     *
     * @param Request $request       A Request instance
     * @param bool    $changeHistory Whether to update the history or not (only used internally for back(), forward(), and reload())
     *
     * @return Crawler
     */
    protected function requestFromRequest(Request $request, $changeHistory = true)
    {
        return $this->request($request->getMethod(), $request->getUri(), $request->getParameters(), $request->getFiles(), $request->getServer(), $request->getContent(), $changeHistory);
    }

    private function updateServerFromUri($server, $uri)
    {
        $server['HTTP_HOST'] = $this->extractHost($uri);
        $scheme = parse_url($uri, PHP_URL_SCHEME);
        $server['HTTPS'] = null === $scheme ? $server['HTTPS'] : 'https' == $scheme;
        unset($server['HTTP_IF_NONE_MATCH'], $server['HTTP_IF_MODIFIED_SINCE']);

        return $server;
    }

    private function extractHost($uri)
    {
        $host = parse_url($uri, PHP_URL_HOST);

        if ($port = parse_url($uri, PHP_URL_PORT)) {
            return $host.':'.$port;
        }

        return $host;
    }
}
