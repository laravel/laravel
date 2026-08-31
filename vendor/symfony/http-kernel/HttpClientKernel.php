<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Help opcache.preload discover always-needed symbols
class_exists(ResponseHeaderBag::class);

/**
 * An implementation of a Symfony HTTP kernel using a "real" HTTP client.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class HttpClientKernel implements HttpKernelInterface
{
    private HttpClientInterface $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        if (null === $client && !class_exists(HttpClient::class)) {
            throw new \LogicException(\sprintf('You cannot use "%s" as the HttpClient component is not installed. Try running "composer require symfony/http-client".', __CLASS__));
        }

        $this->client = $client ?? HttpClient::create();
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        $headers = $this->getHeaders($request);
        $body = '';
        if (null !== $part = $this->getBody($request)) {
            $headers = array_merge($headers, $part->getPreparedHeaders()->toArray());
            $body = $part->bodyToIterable();
        }
        $response = $this->client->request($request->getMethod(), $request->getUri(), [
            'headers' => $headers,
            'body' => $body,
        ] + $request->attributes->get('http_client_options', []));

        $response = new Response($response->getContent(!$catch), $response->getStatusCode(), $response->getHeaders(!$catch));

        $response->headers->remove('X-Body-File');
        $response->headers->remove('X-Body-Eval');
        $response->headers->remove('X-Content-Digest');

        $response->headers = new class($response->headers->all()) extends ResponseHeaderBag {
            protected function computeCacheControlValue(): string
            {
                return $this->getCacheControlHeader(); // preserve the original value
            }
        };

        return $response;
    }

    private function getBody(Request $request): ?AbstractPart
    {
        if (\in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return null;
        }

        if (!class_exists(AbstractPart::class)) {
            throw new \LogicException('You cannot pass non-empty bodies as the Mime component is not installed. Try running "composer require symfony/mime".');
        }

        if ($content = $request->getContent()) {
            return new TextPart($content, 'utf-8', 'plain', '8bit');
        }

        $fields = $request->request->all();
        foreach ($request->files->all() as $name => $file) {
            $fields[$name] = DataPart::fromPath($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType());
        }

        return new FormDataPart($fields);
    }

    private function getHeaders(Request $request): array
    {
        $headers = [];
        foreach ($request->headers as $key => $value) {
            $headers[$key] = $value;
        }
        $cookies = [];
        foreach ($request->cookies->all() as $name => $value) {
            $cookies[] = $name.'='.$value;
        }
        if ($cookies) {
            $headers['cookie'] = implode('; ', $cookies);
        }

        return $headers;
    }
}
