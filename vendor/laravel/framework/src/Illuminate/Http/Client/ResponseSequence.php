<?php

namespace Illuminate\Http\Client;

use Closure;
use Illuminate\Support\Traits\Macroable;
use OutOfBoundsException;

class ResponseSequence
{
    use Macroable;

    /**
     * The responses in the sequence.
     *
     * @var array
     */
    protected $responses;

    /**
     * Indicates that invoking this sequence when it is empty should throw an exception.
     *
     * @var bool
     */
    protected $failWhenEmpty = true;

    /**
     * The response that should be returned when the sequence is empty.
     *
     * @var \GuzzleHttp\Promise\PromiseInterface
     */
    protected $emptyResponse;

    /**
     * Create a new response sequence.
     *
     * @param  array  $responses
     */
    public function __construct(array $responses)
    {
        $this->responses = $responses;
    }

    /**
     * Push a response to the sequence.
     *
     * @param  string|array|null  $body
     * @param  int  $status
     * @param  array  $headers
     * @return $this
     */
    public function push($body = null, int $status = 200, array $headers = [])
    {
        return $this->pushResponse(
            Factory::response($body, $status, $headers)
        );
    }

    /**
     * Push a response with the given status code to the sequence.
     *
     * @param  int  $status
     * @param  array  $headers
     * @return $this
     */
    public function pushStatus(int $status, array $headers = [])
    {
        return $this->pushResponse(
            Factory::response('', $status, $headers)
        );
    }

    /**
     * Push a response with the contents of a file as the body to the sequence.
     *
     * @param  string  $filePath
     * @param  int  $status
     * @param  array  $headers
     * @return $this
     */
    public function pushFile(string $filePath, int $status = 200, array $headers = [])
    {
        $string = file_get_contents($filePath);

        return $this->pushResponse(
            Factory::response($string, $status, $headers)
        );
    }

    /**
     * Push a connection exception to the sequence.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function pushFailedConnection($message = null)
    {
        return $this->pushResponse(
            Factory::failedConnection($message)
        );
    }

    /**
     * Push a response to the sequence.
     *
     * @param  mixed  $response
     * @return $this
     */
    public function pushResponse($response)
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * Make the sequence return a default response when it is empty.
     *
     * @param  \GuzzleHttp\Promise\PromiseInterface|\Closure  $response
     * @return $this
     */
    public function whenEmpty($response)
    {
        $this->failWhenEmpty = false;
        $this->emptyResponse = $response;

        return $this;
    }

    /**
     * Make the sequence return a default response when it is empty.
     *
     * @return $this
     */
    public function dontFailWhenEmpty()
    {
        return $this->whenEmpty(Factory::response());
    }

    /**
     * Indicate that this sequence has depleted all of its responses.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->responses) === 0;
    }

    /**
     * Get the next response in the sequence.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     * @return mixed
     *
     * @throws \OutOfBoundsException
     */
    public function __invoke($request)
    {
        if ($this->failWhenEmpty && $this->isEmpty()) {
            throw new OutOfBoundsException('A request was made, but the response sequence is empty.');
        }

        if (! $this->failWhenEmpty && $this->isEmpty()) {
            return value($this->emptyResponse ?? Factory::response());
        }

        $response = array_shift($this->responses);

        return $response instanceof Closure ? $response($request) : $response;
    }
}
