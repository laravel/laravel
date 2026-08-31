<?php

namespace GuzzleHttp\Handler;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Represents a cURL easy handle and the data it populates.
 *
 * @internal
 */
final class EasyHandle
{
    /**
     * @var resource|\CurlHandle cURL resource
     */
    public $handle;

    /**
     * @var StreamInterface Where data is being written
     */
    public $sink;

    /**
     * @var array Received HTTP headers so far
     */
    public $headers = [];

    /**
     * @var array Valid trailer lines, retained only when an on_trailers
     *            callback is configured
     */
    public $trailers = [];

    /**
     * @var bool Whether this handle was configured with CURLOPT_PIPEWAIT
     */
    public $usesPipewait = false;

    /**
     * @var ResponseInterface|null Received response (if any)
     */
    public $response;

    /**
     * @var RequestInterface Request being sent
     */
    public $request;

    /**
     * @var array Request options
     */
    public $options = [];

    /**
     * @var int cURL error number (if any)
     */
    public $errno = 0;

    /**
     * @var string|null Effective CURLOPT_PROXY value the handle was created with (if any)
     */
    public $effectiveProxy;

    /**
     * Proxy tunnel or SOCKS proxy section signature for connection-reuse
     * isolation, or null when the request does not require sectioning.
     *
     * @var string|null
     */
    public $proxyTunnelSignature;

    /**
     * @var \Throwable|null Exception during on_headers (if any)
     */
    public $onHeadersException;

    /**
     * @var \Throwable|null Exception during createResponse (if any)
     */
    public $createResponseException;

    /**
     * Attach a response to the easy handle based on the received headers.
     *
     * @throws \RuntimeException if no headers have been received or the first
     *                           header line is invalid.
     */
    public function createResponse(): void
    {
        $this->response = null;

        [$ver, $status, $reason, $headers] = HeaderProcessor::parseHeaders($this->headers);

        $normalizedKeys = Utils::normalizeHeaderKeys($headers);

        if (isset($this->options['decode_content']) && $this->options['decode_content'] !== false && isset($normalizedKeys['content-encoding'])) {
            $headers['x-encoded-content-encoding'] = $headers[$normalizedKeys['content-encoding']];
            unset($headers[$normalizedKeys['content-encoding']]);
            if (isset($normalizedKeys['content-length'])) {
                $headers['x-encoded-content-length'] = $headers[$normalizedKeys['content-length']];

                $bodyLength = (int) $this->sink->getSize();
                if ($bodyLength) {
                    $headers[$normalizedKeys['content-length']] = [(string) $bodyLength];
                } else {
                    unset($headers[$normalizedKeys['content-length']]);
                }
            }
        }

        // Attach a response to the easy handle with the parsed headers.
        $this->response = new Response(
            $status,
            $headers,
            $this->sink,
            $ver,
            $reason
        );
    }

    /**
     * @param string $name
     *
     * @return void
     *
     * @throws \BadMethodCallException
     */
    public function __get($name)
    {
        $msg = $name === 'handle' ? 'The EasyHandle has been released' : 'Invalid property: '.$name;
        throw new \BadMethodCallException($msg);
    }
}
