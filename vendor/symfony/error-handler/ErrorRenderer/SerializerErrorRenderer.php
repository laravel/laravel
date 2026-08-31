<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\ErrorRenderer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Formats an exception using Serializer for rendering.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SerializerErrorRenderer implements ErrorRendererInterface
{
    private string|\Closure $format;
    private ErrorRendererInterface $fallbackErrorRenderer;
    private bool|\Closure $debug;

    /**
     * @param string|callable(FlattenException): string $format The format as a string or a callable that should return it
     *                                                          formats not supported by Request::getMimeTypes() should be given as mime types
     * @param bool|callable                             $debug  The debugging mode as a boolean or a callable that should return it
     */
    public function __construct(
        private SerializerInterface $serializer,
        string|callable $format,
        ?ErrorRendererInterface $fallbackErrorRenderer = null,
        bool|callable $debug = false,
    ) {
        $this->format = \is_string($format) ? $format : $format(...);
        $this->fallbackErrorRenderer = $fallbackErrorRenderer ?? new HtmlErrorRenderer();
        $this->debug = \is_bool($debug) ? $debug : $debug(...);
    }

    public function render(\Throwable $exception): FlattenException
    {
        $headers = ['Vary' => 'Accept'];
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($exception);
        if ($debug) {
            $headers['X-Debug-Exception'] = rawurlencode(substr($exception->getMessage(), 0, 2000));
            $headers['X-Debug-Exception-File'] = rawurlencode($exception->getFile()).':'.$exception->getLine();
        }

        $flattenException = FlattenException::createFromThrowable($exception, null, $headers);

        try {
            $format = \is_string($this->format) ? $this->format : ($this->format)($flattenException);
            $headers['Content-Type'] = Request::getMimeTypes($format)[0] ?? $format;

            $flattenException->setAsString($this->serializer->serialize($flattenException, $format, [
                'exception' => $exception,
                'debug' => $debug,
            ]));
        } catch (NotEncodableValueException) {
            $flattenException = $this->fallbackErrorRenderer->render($exception);
        }

        return $flattenException->setHeaders($flattenException->getHeaders() + $headers);
    }

    public static function getPreferredFormat(RequestStack $requestStack): \Closure
    {
        return static function () use ($requestStack) {
            if (!$request = $requestStack->getCurrentRequest()) {
                throw new NotEncodableValueException();
            }

            return $request->getPreferredFormat();
        };
    }
}
