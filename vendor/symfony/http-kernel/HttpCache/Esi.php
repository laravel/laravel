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
class Esi extends AbstractSurrogate
{
    public function getName(): string
    {
        return 'esi';
    }

    public function addSurrogateControl(Response $response): void
    {
        if (str_contains($response->getContent(), '<esi:include')) {
            $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');
        }
    }

    public function renderIncludeTag(string $uri, ?string $alt = null, bool $ignoreErrors = true, string $comment = ''): string
    {
        $html = \sprintf('<esi:include src="%s"%s%s />',
            $uri,
            $ignoreErrors ? ' onerror="continue"' : '',
            $alt ? \sprintf(' alt="%s"', $alt) : ''
        );

        if ($comment) {
            return \sprintf("<esi:comment text=\"%s\" />\n%s", $comment, $html);
        }

        return $html;
    }

    public function process(Request $request, Response $response): Response
    {
        $type = $response->headers->get('Content-Type');
        if (!$type) {
            $type = 'text/html';
        }

        $parts = explode(';', $type);
        if (!\in_array($parts[0], $this->contentTypes, true)) {
            return $response;
        }

        // we don't use a proper XML parser here as we can have ESI tags in a plain text response
        $content = $response->getContent();
        $content = preg_replace('#<esi\:remove>.*?</esi\:remove>#s', '', $content);
        $content = preg_replace('#<esi\:comment[^>]+>#s', '', $content);

        $boundary = self::generateBodyEvalBoundary();
        $chunks = preg_split('#<esi\:include\s+(.*?)\s*(?:/|</esi\:include)>#', $content, -1, \PREG_SPLIT_DELIM_CAPTURE);

        $i = 1;
        while (isset($chunks[$i])) {
            $options = [];
            preg_match_all('/(src|onerror|alt)="([^"]*?)"/', $chunks[$i], $matches, \PREG_SET_ORDER);
            foreach ($matches as $set) {
                $options[$set[1]] = $set[2];
            }

            if (!isset($options['src'])) {
                throw new \RuntimeException('Unable to process an ESI tag without a "src" attribute.');
            }

            $chunks[$i] = $boundary.$options['src']."\n".($options['alt'] ?? '')."\n".('continue' === ($options['onerror'] ?? ''))."\n";
            $i += 2;
        }
        $content = $boundary.implode('', $chunks).$boundary;

        $response->setContent($content);
        $response->headers->set('X-Body-Eval', 'ESI');

        // remove ESI/1.0 from the Surrogate-Control header
        $this->removeFromControl($response);

        return $response;
    }
}
