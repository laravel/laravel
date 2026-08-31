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
 * Ssi implements the SSI capabilities to Request and Response instances.
 *
 * @author Sebastian Krebs <krebs.seb@gmail.com>
 */
class Ssi extends AbstractSurrogate
{
    public function getName(): string
    {
        return 'ssi';
    }

    public function addSurrogateControl(Response $response): void
    {
        if (str_contains($response->getContent(), '<!--#include')) {
            $response->headers->set('Surrogate-Control', 'content="SSI/1.0"');
        }
    }

    public function renderIncludeTag(string $uri, ?string $alt = null, bool $ignoreErrors = true, string $comment = ''): string
    {
        return \sprintf('<!--#include virtual="%s" -->', $uri);
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

        // we don't use a proper XML parser here as we can have SSI tags in a plain text response
        $content = $response->getContent();
        $boundary = self::generateBodyEvalBoundary();
        $chunks = preg_split('#<!--\#include\s+(.*?)\s*-->#', $content, -1, \PREG_SPLIT_DELIM_CAPTURE);

        $i = 1;
        while (isset($chunks[$i])) {
            $options = [];
            preg_match_all('/(virtual)="([^"]*?)"/', $chunks[$i], $matches, \PREG_SET_ORDER);
            foreach ($matches as $set) {
                $options[$set[1]] = $set[2];
            }

            if (!isset($options['virtual'])) {
                throw new \RuntimeException('Unable to process an SSI tag without a "virtual" attribute.');
            }

            $chunks[$i] = $boundary.$options['virtual']."\n\n\n";
            $i += 2;
        }
        $content = $boundary.implode('', $chunks).$boundary;

        $response->setContent($content);
        $response->headers->set('X-Body-Eval', 'SSI');

        // remove SSI/1.0 from the Surrogate-Control header
        $this->removeFromControl($response);

        return $response;
    }
}
