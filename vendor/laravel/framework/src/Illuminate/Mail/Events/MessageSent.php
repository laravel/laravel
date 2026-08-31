<?php

namespace Illuminate\Mail\Events;

use Exception;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Collection;

/**
 * @property \Symfony\Component\Mime\Email $message
 */
class MessageSent
{
    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Mail\SentMessage  $sent  The message that was sent.
     * @param  array  $data  The message data.
     */
    public function __construct(
        public SentMessage $sent,
        public array $data = [],
    ) {
    }

    /**
     * Get the serializable representation of the object.
     *
     * @return array
     */
    public function __serialize()
    {
        $hasAttachments = (new Collection($this->message->getAttachments()))->isNotEmpty();

        return [
            'sent' => $this->sent,
            'data' => $hasAttachments ? base64_encode(serialize($this->data)) : $this->data,
            'hasAttachments' => $hasAttachments,
        ];
    }

    /**
     * Marshal the object from its serialized data.
     *
     * @param  array  $data
     * @return void
     */
    public function __unserialize(array $data)
    {
        $this->sent = $data['sent'];

        $this->data = (($data['hasAttachments'] ?? false) === true)
            ? unserialize(base64_decode($data['data']))
            : $data['data'];
    }

    /**
     * Dynamically get the original message.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($key)
    {
        if ($key === 'message') {
            return $this->sent->getOriginalMessage();
        }

        throw new Exception('Unable to access undefined property on '.__CLASS__.': '.$key);
    }
}
