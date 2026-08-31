<?php

namespace GuzzleHttp;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Represents data at the point after it was transferred either successfully
 * or after a network error.
 */
final class TransferStats
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var float|null
     */
    private $transferTime;

    /**
     * @var array
     */
    private $handlerStats;

    /**
     * @var mixed|null
     */
    private $handlerErrorData;

    /**
     * @param RequestInterface       $request          Request that was sent.
     * @param ResponseInterface|null $response         Response received (if any)
     * @param float|null             $transferTime     Total handler transfer time.
     * @param mixed                  $handlerErrorData Handler error data.
     * @param array                  $handlerStats     Handler specific stats.
     */
    public function __construct(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?float $transferTime = null,
        $handlerErrorData = null,
        array $handlerStats = []
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->transferTime = $transferTime;
        $this->handlerErrorData = $handlerErrorData;
        $this->handlerStats = $handlerStats;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Returns the response that was received (if any).
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Returns true if a response was received.
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * Gets handler specific error data.
     *
     * This might be an exception, a integer representing an error code, or
     * anything else. Relying on this value assumes that you know what handler
     * you are using.
     *
     * @return mixed
     */
    public function getHandlerErrorData()
    {
        return $this->handlerErrorData;
    }

    /**
     * Get the effective URI the request was sent to.
     */
    public function getEffectiveUri(): UriInterface
    {
        return $this->request->getUri();
    }

    /**
     * Get the estimated time the request was being transferred by the handler.
     *
     * @return float|null Time in seconds.
     */
    public function getTransferTime(): ?float
    {
        return $this->transferTime;
    }

    /**
     * Gets an array of all of the handler specific transfer data.
     */
    public function getHandlerStats(): array
    {
        return $this->handlerStats;
    }

    /**
     * Get a specific handler statistic from the handler by name.
     *
     * @param string $stat Handler specific transfer stat to retrieve.
     *
     * @return mixed|null
     */
    public function getHandlerStat(string $stat)
    {
        return $this->handlerStats[$stat] ?? null;
    }
}
