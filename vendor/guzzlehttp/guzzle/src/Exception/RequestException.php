<?php

namespace GuzzleHttp\Exception;

use GuzzleHttp\BodySummarizer;
use GuzzleHttp\BodySummarizerInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Request exception
 */
class RequestException extends TransferException implements RequestExceptionInterface
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
     * @var array
     */
    private $handlerContext;

    public function __construct(
        string $message,
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?\Throwable $previous = null,
        array $handlerContext = []
    ) {
        // Set the code of the exception if the response is set and not future.
        $code = $response ? $response->getStatusCode() : 0;
        parent::__construct($message, $code, $previous);
        $this->request = $request;
        $this->response = $response;
        $this->handlerContext = $handlerContext;
    }

    /**
     * Wrap non-RequestExceptions with a RequestException
     *
     * @deprecated since 7.11. Create a RequestException directly instead.
     */
    public static function wrapException(RequestInterface $request, \Throwable $e): RequestException
    {
        \trigger_deprecation('guzzlehttp/guzzle', '7.11', '%s::wrapException() is deprecated and will be removed in 8.0. Create a %s directly instead.', self::class, self::class);

        return $e instanceof RequestException ? $e : new RequestException($e->getMessage(), $request, null, $e);
    }

    /**
     * Factory method to create a new exception with a normalized error message
     *
     * @param RequestInterface             $request        Request sent
     * @param ResponseInterface            $response       Response received
     * @param \Throwable|null              $previous       Previous exception
     * @param array                        $handlerContext Optional handler context
     * @param BodySummarizerInterface|null $bodySummarizer Optional body summarizer
     */
    public static function create(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?\Throwable $previous = null,
        array $handlerContext = [],
        ?BodySummarizerInterface $bodySummarizer = null
    ): self {
        if (!$response) {
            return new self(
                'Error completing request',
                $request,
                null,
                $previous,
                $handlerContext
            );
        }

        $level = (int) \floor($response->getStatusCode() / 100);
        if ($level === 4) {
            $label = 'Client error';
            $className = ClientException::class;
        } elseif ($level === 5) {
            $label = 'Server error';
            $className = ServerException::class;
        } else {
            $label = 'Unsuccessful request';
            $className = __CLASS__;
        }

        $uri = \GuzzleHttp\Psr7\Utils::redactUserInfo($request->getUri());

        // Client Error: `GET /` resulted in a `404 Not Found` response:
        // <html> ... (truncated)
        $message = \sprintf(
            '%s: `%s %s` resulted in a `%s %s` response',
            $label,
            $request->getMethod(),
            $uri->__toString(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        $summary = ($bodySummarizer ?? new BodySummarizer())->summarize($response);

        if ($summary !== null) {
            $message .= ":\n{$summary}\n";
        }

        return new $className($message, $request, $response, $previous, $handlerContext);
    }

    /**
     * Get the request that caused the exception
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the associated response
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Check if a response was received
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * Get contextual information about the error from the underlying handler.
     *
     * The contents of this array will vary depending on which handler you are
     * using. It may also be just an empty array. Relying on this data will
     * couple you to a specific handler, but can give more debug information
     * when needed.
     */
    public function getHandlerContext(): array
    {
        return $this->handlerContext;
    }
}
