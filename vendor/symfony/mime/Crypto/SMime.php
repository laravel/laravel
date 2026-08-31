<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Crypto;

use Symfony\Component\Mime\Exception\RuntimeException;
use Symfony\Component\Mime\Part\SMimePart;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * @internal
 */
abstract class SMime
{
    protected function normalizeFilePath(string $path): string
    {
        if (!file_exists($path)) {
            throw new RuntimeException(\sprintf('File does not exist: "%s".', $path));
        }

        return 'file://'.str_replace('\\', '/', realpath($path));
    }

    protected function iteratorToFile(iterable $iterator, $stream): void
    {
        foreach ($iterator as $chunk) {
            fwrite($stream, $chunk);
        }
    }

    protected function convertMessageToSMimePart($stream, string $type, string $subtype): SMimePart
    {
        rewind($stream);

        $headers = '';

        while (!feof($stream)) {
            $buffer = fread($stream, 78);
            $headers .= $buffer;

            // Detect ending of header list
            if (preg_match('/(\r\n\r\n|\n\n)/', $headers, $match)) {
                $headersPosEnd = strpos($headers, $headerBodySeparator = $match[0]);

                break;
            }
        }

        $headers = $this->getMessageHeaders(trim(substr($headers, 0, $headersPosEnd)));

        fseek($stream, $headersPosEnd + \strlen($headerBodySeparator));

        return new SMimePart($this->getStreamIterator($stream), $type, $subtype, $this->getParametersFromHeader($headers['content-type']));
    }

    protected function getStreamIterator($stream): iterable
    {
        while (!feof($stream)) {
            yield str_replace("\n", "\r\n", str_replace("\r\n", "\n", fread($stream, 16372)));
        }
    }

    private function getMessageHeaders(string $headerData): array
    {
        $headers = [];
        $headerLines = explode("\r\n", str_replace("\n", "\r\n", str_replace("\r\n", "\n", $headerData)));
        $currentHeaderName = '';

        // Transform header lines into an associative array
        foreach ($headerLines as $headerLine) {
            // Empty lines between headers indicate a new mime-entity
            if ('' === $headerLine) {
                break;
            }

            // Handle headers that span multiple lines
            if (!str_contains($headerLine, ':')) {
                $headers[$currentHeaderName] .= ' '.trim($headerLine);
                continue;
            }

            $header = explode(':', $headerLine, 2);
            $currentHeaderName = strtolower($header[0]);
            $headers[$currentHeaderName] = trim($header[1]);
        }

        return $headers;
    }

    private function getParametersFromHeader(string $header): array
    {
        $params = [];

        preg_match_all('/(?P<name>[a-z-0-9]+)=(?P<value>"[^"]+"|(?:[^\s;]+|$))(?:\s+;)?/i', $header, $matches);

        foreach ($matches['value'] as $pos => $paramValue) {
            $params[$matches['name'][$pos]] = trim($paramValue, '"');
        }

        return $params;
    }
}
