<?php

namespace Illuminate\Http\Client;

use GuzzleHttp\Psr7\Message;

class RequestException extends HttpClientException
{
    /**
     * The response instance.
     *
     * @var \Illuminate\Http\Client\Response
     */
    public $response;

    /**
     * The current truncation length for the exception message.
     *
     * @var int|false|null
     */
    public $truncateExceptionsAt;

    /**
     * The global truncation length for the exception message.
     *
     * @var int|false
     */
    public static $truncateAt = 120;

    /**
     * Whether the response has been summarized in the message.
     *
     * @var bool
     */
    public $hasBeenSummarized = false;

    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @param  int|false|null  $truncateExceptionsAt
     */
    public function __construct(Response $response, $truncateExceptionsAt = null)
    {
        $this->truncateExceptionsAt = $truncateExceptionsAt;

        $this->response = $response;

        parent::__construct($this->prepareMessage($response), $response->status());
    }

    /**
     * Enable truncation of request exception messages.
     *
     * @return void
     */
    public static function truncate()
    {
        static::$truncateAt = 120;
    }

    /**
     * Set the truncation length for request exception messages.
     *
     * @param  int  $length
     * @return void
     */
    public static function truncateAt(int $length)
    {
        static::$truncateAt = $length;
    }

    /**
     * Disable truncation of request exception messages.
     *
     * @return void
     */
    public static function dontTruncate()
    {
        static::$truncateAt = false;
    }

    /**
     * Prepare the exception message.
     *
     * @return bool
     */
    public function report()
    {
        if (! $this->hasBeenSummarized) {
            $this->message = $this->prepareMessage($this->response);

            $this->hasBeenSummarized = true;
        }

        return false;
    }

    /**
     * Prepare the exception message.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @return string
     */
    protected function prepareMessage(Response $response)
    {
        $message = "HTTP request returned status code {$response->status()}";

        $truncateExceptionsAt = $this->truncateExceptionsAt ?? static::$truncateAt;

        $psrResponse = $response->toPsrResponse();

        $summary = null;

        if (is_int($truncateExceptionsAt)) {
            $summary = Message::bodySummary($psrResponse, $truncateExceptionsAt);
        } elseif (($body = $psrResponse->getBody())->isSeekable() && $body->isReadable()) {
            $summary = Message::toString($psrResponse);
        }

        return is_null($summary) ? $message : $message.":\n{$summary}\n";
    }
}
