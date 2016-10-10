<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Esi implements the ESI capabilities to Request and Response instances.
 *
 * For more information, read the following W3C notes:
 *
 *  * ESI Language Specification 1.0 (http://www.w3.org/TR/esi-lang)
 *
 *  * Edge Architecture Specification (http://www.w3.org/TR/edge-arch)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Esi
{
    private $contentTypes;

    /**
     * Constructor.
     *
     * @param array $contentTypes An array of content-type that should be parsed for ESI information.
     *                           (default: text/html, text/xml, application/xhtml+xml, and application/xml)
     */
    public function __construct(array $contentTypes = array('text/html', 'text/xml', 'application/xhtml+xml', 'application/xml'))
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * Returns a new cache strategy instance.
     *
     * @return EsiResponseCacheStrategyInterface A EsiResponseCacheStrategyInterface instance
     */
    public function createCacheStrategy()
    {
        return new EsiResponseCacheStrategy();
    }

    /**
     * Checks that at least one surrogate has ESI/1.0 capability.
     *
     * @param Request $request A Request instance
     *
     * @return bool    true if one surrogate has ESI/1.0 capability, false otherwise
     */
    public function hasSurrogateEsiCapability(Request $request)
    {
        if (null === $value = $request->headers->get('Surrogate-Capability')) {
            return false;
        }

        return false !== strpos($value, 'ESI/1.0');
    }

    /**
     * Adds ESI/1.0 capability to the given Request.
     *
     * @param Request $request A Request instance
     */
    public function addSurrogateEsiCapability(Request $request)
    {
        $current = $request->headers->get('Surrogate-Capability');
        $new = 'symfony2="ESI/1.0"';

        $request->headers->set('Surrogate-Capability', $current ? $current.', '.$new : $new);
    }

    /**
     * Adds HTTP headers to specify that the Response needs to be parsed for ESI.
     *
     * This method only adds an ESI HTTP header if the Response has some ESI tags.
     *
     * @param Response $response A Response instance
     */
    public function addSurrogateControl(Response $response)
    {
        if (false !== strpos($response->getContent(), '<esi:include')) {
            $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');
        }
    }

    /**
     * Checks that the Response needs to be parsed for ESI tags.
     *
     * @param Response $response A Response instance
     *
     * @return bool    true if the Response needs to be parsed, false otherwise
     */
    public function needsEsiParsing(Response $response)
    {
        if (!$control = $response->headers->get('Surrogate-Control')) {
            return false;
        }

        return (bool) preg_match('#content="[^"]*ESI/1.0[^"]*"#', $control);
    }

    /**
     * Renders an ESI tag.
     *
     * @param string  $uri          A URI
     * @param string  $alt          An alternate URI
     * @param bool    $ignoreErrors Whether to ignore errors or not
     * @param string  $comment      A comment to add as an esi:include tag
     *
     * @return string
     */
    public function renderIncludeTag($uri, $alt = null, $ignoreErrors = true, $comment = '')
    {
        $html = sprintf('<esi:include src="%s"%s%s />',
            $uri,
            $ignoreErrors ? ' onerror="continue"' : '',
            $alt ? sprintf(' alt="%s"', $alt) : ''
        );

        if (!empty($comment)) {
            return sprintf("<esi:comment text=\"%s\" />\n%s", $comment, $html);
        }

        return $html;
    }

    /**
     * Replaces a Response ESI tags with the included resource content.
     *
     * @param Request  $request  A Request instance
     * @param Response $response A Response instance
     *
     * @return Response
     */
    public function process(Request $request, Response $response)
    {
        $this->request = $request;
        $type = $response->headers->get('Content-Type');
        if (empty($type)) {
            $type = 'text/html';
        }

        $parts = explode(';', $type);
        if (!in_array($parts[0], $this->contentTypes)) {
            return $response;
        }

        // we don't use a proper XML parser here as we can have ESI tags in a plain text response
        $content = $response->getContent();
        $content = str_replace(array('<?', '<%'), array('<?php echo "<?"; ?>', '<?php echo "<%"; ?>'), $content);
        $content = preg_replace_callback('#<esi\:include\s+(.*?)\s*(?:/|</esi\:include)>#', array($this, 'handleEsiIncludeTag'), $content);
        $content = preg_replace('#<esi\:comment[^>]*(?:/|</esi\:comment)>#', '', $content);
        $content = preg_replace('#<esi\:remove>.*?</esi\:remove>#', '', $content);

        $response->setContent($content);
        $response->headers->set('X-Body-Eval', 'ESI');

        // remove ESI/1.0 from the Surrogate-Control header
        if ($response->headers->has('Surrogate-Control')) {
            $value = $response->headers->get('Surrogate-Control');
            if ('content="ESI/1.0"' == $value) {
                $response->headers->remove('Surrogate-Control');
            } elseif (preg_match('#,\s*content="ESI/1.0"#', $value)) {
                $response->headers->set('Surrogate-Control', preg_replace('#,\s*content="ESI/1.0"#', '', $value));
            } elseif (preg_match('#content="ESI/1.0",\s*#', $value)) {
                $response->headers->set('Surrogate-Control', preg_replace('#content="ESI/1.0",\s*#', '', $value));
            }
        }
    }

    /**
     * Handles an ESI from the cache.
     *
     * @param HttpCache $cache        An HttpCache instance
     * @param string    $uri          The main URI
     * @param string    $alt          An alternative URI
     * @param bool      $ignoreErrors Whether to ignore errors or not
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function handle(HttpCache $cache, $uri, $alt, $ignoreErrors)
    {
        $subRequest = Request::create($uri, 'get', array(), $cache->getRequest()->cookies->all(), array(), $cache->getRequest()->server->all());

        try {
            $response = $cache->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true);

            if (!$response->isSuccessful()) {
                throw new \RuntimeException(sprintf('Error when rendering "%s" (Status code is %s).', $subRequest->getUri(), $response->getStatusCode()));
            }

            return $response->getContent();
        } catch (\Exception $e) {
            if ($alt) {
                return $this->handle($cache, $alt, '', $ignoreErrors);
            }

            if (!$ignoreErrors) {
                throw $e;
            }
        }
    }

    /**
     * Handles an ESI include tag (called internally).
     *
     * @param array $attributes An array containing the attributes.
     *
     * @return string The response content for the include.
     *
     * @throws \RuntimeException
     */
    private function handleEsiIncludeTag($attributes)
    {
        $options = array();
        preg_match_all('/(src|onerror|alt)="([^"]*?)"/', $attributes[1], $matches, PREG_SET_ORDER);
        foreach ($matches as $set) {
            $options[$set[1]] = $set[2];
        }

        if (!isset($options['src'])) {
            throw new \RuntimeException('Unable to process an ESI tag without a "src" attribute.');
        }

        return sprintf('<?php echo $this->esi->handle($this, %s, %s, %s) ?>'."\n",
            var_export($options['src'], true),
            var_export(isset($options['alt']) ? $options['alt'] : '', true),
            isset($options['onerror']) && 'continue' == $options['onerror'] ? 'true' : 'false'
        );
    }
}
