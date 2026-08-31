<?php

namespace Illuminate\Mail;

use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Mail\Message
 */
class TextMessage
{
    use ForwardsCalls;

    /**
     * The underlying message instance.
     *
     * @var \Illuminate\Mail\Message
     */
    protected $message;

    /**
     * Create a new text message instance.
     *
     * @param  \Illuminate\Mail\Message  $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Embed a file in the message and get the CID.
     *
     * @param  string|\Illuminate\Contracts\Mail\Attachable|\Illuminate\Mail\Attachment  $file
     * @return string
     */
    public function embed($file)
    {
        return '';
    }

    /**
     * Embed in-memory data in the message and get the CID.
     *
     * @param  string|resource  $data
     * @param  string  $name
     * @param  string|null  $contentType
     * @return string
     */
    public function embedData($data, $name, $contentType = null)
    {
        return '';
    }

    /**
     * Dynamically pass missing methods to the underlying message instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->message, $method, $parameters);
    }
}
