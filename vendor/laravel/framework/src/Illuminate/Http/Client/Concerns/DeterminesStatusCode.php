<?php

namespace Illuminate\Http\Client\Concerns;

trait DeterminesStatusCode
{
    /**
     * Determine if the response code was 200 "OK" response.
     *
     * @return bool
     */
    public function ok()
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response code was 201 "Created" response.
     *
     * @return bool
     */
    public function created()
    {
        return $this->status() === 201;
    }

    /**
     * Determine if the response code was 202 "Accepted" response.
     *
     * @return bool
     */
    public function accepted()
    {
        return $this->status() === 202;
    }

    /**
     * Determine if the response code was the given status code and the body has no content.
     *
     * @param  int  $status
     * @return bool
     */
    public function noContent($status = 204)
    {
        return $this->status() === $status && $this->body() === '';
    }

    /**
     * Determine if the response code was a 301 "Moved Permanently".
     *
     * @return bool
     */
    public function movedPermanently()
    {
        return $this->status() === 301;
    }

    /**
     * Determine if the response code was a 302 "Found" response.
     *
     * @return bool
     */
    public function found()
    {
        return $this->status() === 302;
    }

    /**
     * Determine if the response code was a 304 "Not Modified" response.
     *
     * @return bool
     */
    public function notModified()
    {
        return $this->status() === 304;
    }

    /**
     * Determine if the response was a 400 "Bad Request" response.
     *
     * @return bool
     */
    public function badRequest()
    {
        return $this->status() === 400;
    }

    /**
     * Determine if the response was a 401 "Unauthorized" response.
     *
     * @return bool
     */
    public function unauthorized()
    {
        return $this->status() === 401;
    }

    /**
     * Determine if the response was a 402 "Payment Required" response.
     *
     * @return bool
     */
    public function paymentRequired()
    {
        return $this->status() === 402;
    }

    /**
     * Determine if the response was a 403 "Forbidden" response.
     *
     * @return bool
     */
    public function forbidden()
    {
        return $this->status() === 403;
    }

    /**
     * Determine if the response was a 404 "Not Found" response.
     *
     * @return bool
     */
    public function notFound()
    {
        return $this->status() === 404;
    }

    /**
     * Determine if the response was a 408 "Request Timeout" response.
     *
     * @return bool
     */
    public function requestTimeout()
    {
        return $this->status() === 408;
    }

    /**
     * Determine if the response was a 409 "Conflict" response.
     *
     * @return bool
     */
    public function conflict()
    {
        return $this->status() === 409;
    }

    /**
     * Determine if the response was a 422 "Unprocessable Content" response.
     *
     * @return bool
     */
    public function unprocessableContent()
    {
        return $this->status() === 422;
    }

    /**
     * Determine if the response was a 422 "Unprocessable Content" response.
     *
     * @return bool
     */
    public function unprocessableEntity()
    {
        return $this->unprocessableContent();
    }

    /**
     * Determine if the response was a 429 "Too Many Requests" response.
     *
     * @return bool
     */
    public function tooManyRequests()
    {
        return $this->status() === 429;
    }
}
