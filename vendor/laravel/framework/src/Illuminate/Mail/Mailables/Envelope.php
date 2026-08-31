<?php

namespace Illuminate\Mail\Mailables;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class Envelope
{
    use Conditionable;

    /**
     * The address sending the message.
     *
     * @var \Illuminate\Mail\Mailables\Address|string|null
     */
    public $from;

    /**
     * The recipients of the message.
     *
     * @var array
     */
    public $to;

    /**
     * The recipients receiving a copy of the message.
     *
     * @var array
     */
    public $cc;

    /**
     * The recipients receiving a blind copy of the message.
     *
     * @var array
     */
    public $bcc;

    /**
     * The recipients that should be replied to.
     *
     * @var array
     */
    public $replyTo;

    /**
     * The subject of the message.
     *
     * @var string|null
     */
    public $subject;

    /**
     * The message's tags.
     *
     * @var array
     */
    public $tags = [];

    /**
     * The message's metadata.
     *
     * @var array
     */
    public $metadata = [];

    /**
     * The message's Symfony Message customization callbacks.
     *
     * @var array
     */
    public $using = [];

    /**
     * Create a new message envelope instance.
     *
     * @param  \Illuminate\Mail\Mailables\Address|string|null  $from
     * @param  array<int, \Illuminate\Mail\Mailables\Address|string>  $to
     * @param  array<int, \Illuminate\Mail\Mailables\Address|string>  $cc
     * @param  array<int, \Illuminate\Mail\Mailables\Address|string>  $bcc
     * @param  array<int, \Illuminate\Mail\Mailables\Address|string>  $replyTo
     * @param  string|null  $subject
     * @param  array  $tags
     * @param  array  $metadata
     * @param  \Closure|array  $using
     *
     * @named-arguments-supported
     */
    public function __construct(Address|string|null $from = null, $to = [], $cc = [], $bcc = [], $replyTo = [], ?string $subject = null, array $tags = [], array $metadata = [], Closure|array $using = [])
    {
        $this->from = is_string($from) ? new Address($from) : $from;
        $this->to = $this->normalizeAddresses($to);
        $this->cc = $this->normalizeAddresses($cc);
        $this->bcc = $this->normalizeAddresses($bcc);
        $this->replyTo = $this->normalizeAddresses($replyTo);
        $this->subject = $subject;
        $this->tags = $tags;
        $this->metadata = $metadata;
        $this->using = Arr::wrap($using);
    }

    /**
     * Normalize the given array of addresses.
     *
     * @param  array<int, \Illuminate\Mail\Mailables\Address|string>  $addresses
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected function normalizeAddresses($addresses)
    {
        return (new Collection($addresses))
            ->map(fn ($address) => is_string($address) ? new Address($address) : $address)
            ->all();
    }

    /**
     * Specify who the message will be "from".
     *
     * @param  \Illuminate\Mail\Mailables\Address|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function from(Address|string $address, $name = null)
    {
        $this->from = is_string($address) ? new Address($address, $name) : $address;

        return $this;
    }

    /**
     * Add a "to" recipient to the message envelope.
     *
     * @param  \Illuminate\Mail\Mailables\Address|array<int, \Illuminate\Mail\Mailables\Address|string>|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function to(Address|array|string $address, $name = null)
    {
        $this->to = array_merge($this->to, $this->normalizeAddresses(
            is_string($name) ? [new Address($address, $name)] : Arr::wrap($address),
        ));

        return $this;
    }

    /**
     * Add a "cc" recipient to the message envelope.
     *
     * @param  \Illuminate\Mail\Mailables\Address|array<int, \Illuminate\Mail\Mailables\Address|string>|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function cc(Address|array|string $address, $name = null)
    {
        $this->cc = array_merge($this->cc, $this->normalizeAddresses(
            is_string($name) ? [new Address($address, $name)] : Arr::wrap($address),
        ));

        return $this;
    }

    /**
     * Add a "bcc" recipient to the message envelope.
     *
     * @param  \Illuminate\Mail\Mailables\Address|array<int, \Illuminate\Mail\Mailables\Address|string>|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function bcc(Address|array|string $address, $name = null)
    {
        $this->bcc = array_merge($this->bcc, $this->normalizeAddresses(
            is_string($name) ? [new Address($address, $name)] : Arr::wrap($address),
        ));

        return $this;
    }

    /**
     * Add a "reply to" recipient to the message envelope.
     *
     * @param  \Illuminate\Mail\Mailables\Address|array<int, \Illuminate\Mail\Mailables\Address|string>|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function replyTo(Address|array|string $address, $name = null)
    {
        $this->replyTo = array_merge($this->replyTo, $this->normalizeAddresses(
            is_string($name) ? [new Address($address, $name)] : Arr::wrap($address),
        ));

        return $this;
    }

    /**
     * Set the subject of the message.
     *
     * @param  string  $subject
     * @return $this
     */
    public function subject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Add "tags" to the message.
     *
     * @param  array  $tags
     * @return $this
     */
    public function tags(array $tags)
    {
        $this->tags = array_merge($this->tags, $tags);

        return $this;
    }

    /**
     * Add a "tag" to the message.
     *
     * @param  string  $tag
     * @return $this
     */
    public function tag(string $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Add metadata to the message.
     *
     * @param  string  $key
     * @param  string|int  $value
     * @return $this
     */
    public function metadata(string $key, string|int $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Add a Symfony Message customization callback to the message.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function using(Closure $callback)
    {
        $this->using[] = $callback;

        return $this;
    }

    /**
     * Determine if the message is from the given address.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function isFrom(string $address, ?string $name = null)
    {
        if (is_null($name)) {
            return $this->from->address === $address;
        }

        return $this->from->address === $address &&
               $this->from->name === $name;
    }

    /**
     * Determine if the message has the given address as a recipient.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasTo(string $address, ?string $name = null)
    {
        return $this->hasRecipient($this->to, $address, $name);
    }

    /**
     * Determine if the message has the given address as a "cc" recipient.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasCc(string $address, ?string $name = null)
    {
        return $this->hasRecipient($this->cc, $address, $name);
    }

    /**
     * Determine if the message has the given address as a "bcc" recipient.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasBcc(string $address, ?string $name = null)
    {
        return $this->hasRecipient($this->bcc, $address, $name);
    }

    /**
     * Determine if the message has the given address as a "reply to" recipient.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasReplyTo(string $address, ?string $name = null)
    {
        return $this->hasRecipient($this->replyTo, $address, $name);
    }

    /**
     * Determine if the message has the given recipient.
     *
     * @param  array  $recipients
     * @param  string  $address
     * @param  string|null  $name
     * @return bool
     */
    protected function hasRecipient(array $recipients, string $address, ?string $name = null)
    {
        return (new Collection($recipients))->contains(function ($recipient) use ($address, $name) {
            if (is_null($name)) {
                return $recipient->address === $address;
            }

            return $recipient->address === $address &&
                   $recipient->name === $name;
        });
    }

    /**
     * Determine if the message has the given subject.
     *
     * @param  string  $subject
     * @return bool
     */
    public function hasSubject(string $subject)
    {
        return $this->subject === $subject;
    }

    /**
     * Determine if the message has the given metadata.
     *
     * @param  string  $key
     * @param  string  $value
     * @return bool
     */
    public function hasMetadata(string $key, string $value)
    {
        return isset($this->metadata[$key]) && (string) $this->metadata[$key] === $value;
    }
}
