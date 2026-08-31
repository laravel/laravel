<?php

namespace Illuminate\Auth\Access;

use Illuminate\Contracts\Support\Arrayable;
use Stringable;

class Response implements Arrayable, Stringable
{
    /**
     * Indicates whether the response was allowed.
     *
     * @var bool
     */
    protected $allowed;

    /**
     * The response message.
     *
     * @var string|null
     */
    protected $message;

    /**
     * The response code.
     *
     * @var mixed
     */
    protected $code;

    /**
     * The HTTP response status code.
     *
     * @var int|null
     */
    protected $status;

    /**
     * Create a new response.
     *
     * @param  bool  $allowed
     * @param  string|null  $message
     * @param  mixed  $code
     */
    public function __construct($allowed, $message = '', $code = null)
    {
        $this->code = $code;
        $this->allowed = $allowed;
        $this->message = $message;
    }

    /**
     * Create a new "allow" Response.
     *
     * @param  string|null  $message
     * @param  mixed  $code
     * @return \Illuminate\Auth\Access\Response
     */
    public static function allow($message = null, $code = null)
    {
        return new static(true, $message, $code);
    }

    /**
     * Create a new "deny" Response.
     *
     * @param  string|null  $message
     * @param  mixed  $code
     * @return \Illuminate\Auth\Access\Response
     */
    public static function deny($message = null, $code = null)
    {
        return new static(false, $message, $code);
    }

    /**
     * Create a new "deny" Response with a HTTP status code.
     *
     * @param  int  $status
     * @param  string|null  $message
     * @param  mixed  $code
     * @return \Illuminate\Auth\Access\Response
     */
    public static function denyWithStatus($status, $message = null, $code = null)
    {
        return static::deny($message, $code)->withStatus($status);
    }

    /**
     * Create a new "deny" Response with a 404 HTTP status code.
     *
     * @param  string|null  $message
     * @param  mixed  $code
     * @return \Illuminate\Auth\Access\Response
     */
    public static function denyAsNotFound($message = null, $code = null)
    {
        return static::denyWithStatus(404, $message, $code);
    }

    /**
     * Determine if the response was allowed.
     *
     * @return bool
     */
    public function allowed()
    {
        return $this->allowed;
    }

    /**
     * Determine if the response was denied.
     *
     * @return bool
     */
    public function denied()
    {
        return ! $this->allowed();
    }

    /**
     * Get the response message.
     *
     * @return string|null
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Get the response code / reason.
     *
     * @return mixed
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Throw authorization exception if response was denied.
     *
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize()
    {
        if ($this->denied()) {
            throw (new AuthorizationException($this->message(), $this->code()))
                ->setResponse($this)
                ->withStatus($this->status);
        }

        return $this;
    }

    /**
     * Set the HTTP response status code.
     *
     * @param  null|int  $status
     * @return $this
     */
    public function withStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the HTTP response status code to 404.
     *
     * @return $this
     */
    public function asNotFound()
    {
        return $this->withStatus(404);
    }

    /**
     * Get the HTTP status code.
     *
     * @return int|null
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Convert the response to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'allowed' => $this->allowed(),
            'message' => $this->message(),
            'code' => $this->code(),
        ];
    }

    /**
     * Get the string representation of the message.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->message();
    }
}
