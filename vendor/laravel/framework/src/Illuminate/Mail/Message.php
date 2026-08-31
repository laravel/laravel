<?php

namespace Illuminate\Mail;

use Illuminate\Contracts\Mail\Attachable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use InvalidArgumentException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

/**
 * @mixin \Symfony\Component\Mime\Email
 */
class Message
{
    use ForwardsCalls;

    /**
     * The Symfony Email instance.
     *
     * @var \Symfony\Component\Mime\Email
     */
    protected $message;

    /**
     * CIDs of files embedded in the message.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @var array
     */
    protected $embeddedFiles = [];

    /**
     * Create a new message instance.
     *
     * @param  \Symfony\Component\Mime\Email  $message
     */
    public function __construct(Email $message)
    {
        $this->message = $message;
    }

    /**
     * Add a "from" address to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        is_array($address)
            ? $this->message->from(...$this->ensureAddressesAreSafe($address))
            : $this->message->from($this->createAddress($address, (string) $name));

        return $this;
    }

    /**
     * Set the "sender" of the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @return $this
     */
    public function sender($address, $name = null)
    {
        is_array($address)
            ? $this->message->sender(...$this->ensureAddressesAreSafe($address))
            : $this->message->sender($this->createAddress($address, (string) $name));

        return $this;
    }

    /**
     * Set the "return path" of the message.
     *
     * @param  string  $address
     * @return $this
     */
    public function returnPath($address)
    {
        $this->ensureAddressIsSafe($address);

        $this->message->returnPath($address);

        return $this;
    }

    /**
     * Add a recipient to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @param  bool  $override
     * @return $this
     */
    public function to($address, $name = null, $override = false)
    {
        if ($override) {
            is_array($address)
                ? $this->message->to(...$this->ensureAddressesAreSafe($address))
                : $this->message->to($this->createAddress($address, (string) $name));

            return $this;
        }

        return $this->addAddresses($address, $name, 'To');
    }

    /**
     * Remove all "to" addresses from the message.
     *
     * @return $this
     */
    public function forgetTo()
    {
        if ($header = $this->message->getHeaders()->get('To')) {
            $this->addAddressDebugHeader('X-To', $this->message->getTo());

            $header->setAddresses([]);
        }

        return $this;
    }

    /**
     * Add a carbon copy to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @param  bool  $override
     * @return $this
     */
    public function cc($address, $name = null, $override = false)
    {
        if ($override) {
            is_array($address)
                ? $this->message->cc(...$this->ensureAddressesAreSafe($address))
                : $this->message->cc($this->createAddress($address, (string) $name));

            return $this;
        }

        return $this->addAddresses($address, $name, 'Cc');
    }

    /**
     * Remove all carbon copy addresses from the message.
     *
     * @return $this
     */
    public function forgetCc()
    {
        if ($header = $this->message->getHeaders()->get('Cc')) {
            $this->addAddressDebugHeader('X-Cc', $this->message->getCC());

            $header->setAddresses([]);
        }

        return $this;
    }

    /**
     * Add a blind carbon copy to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @param  bool  $override
     * @return $this
     */
    public function bcc($address, $name = null, $override = false)
    {
        if ($override) {
            is_array($address)
                ? $this->message->bcc(...$this->ensureAddressesAreSafe($address))
                : $this->message->bcc($this->createAddress($address, (string) $name));

            return $this;
        }

        return $this->addAddresses($address, $name, 'Bcc');
    }

    /**
     * Remove all of the blind carbon copy addresses from the message.
     *
     * @return $this
     */
    public function forgetBcc()
    {
        if ($header = $this->message->getHeaders()->get('Bcc')) {
            $this->addAddressDebugHeader('X-Bcc', $this->message->getBcc());

            $header->setAddresses([]);
        }

        return $this;
    }

    /**
     * Add a "reply to" address to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @return $this
     */
    public function replyTo($address, $name = null)
    {
        return $this->addAddresses($address, $name, 'ReplyTo');
    }

    /**
     * Add a recipient to the message.
     *
     * @param  string|array  $address
     * @param  string  $name
     * @param  string  $type
     * @return $this
     */
    protected function addAddresses($address, $name, $type)
    {
        if (is_array($address)) {
            $type = lcfirst($type);

            $addresses = (new Collection($address))->map(function ($address, $key) {
                if (is_string($key) && is_string($address)) {
                    return $this->createAddress($key, $address);
                }

                if (is_array($address)) {
                    return $this->createAddress($address['email'] ?? $address['address'], $address['name'] ?? null);
                }

                if (is_null($address)) {
                    return $this->createAddress($key);
                }

                return $this->ensureAddressIsSafe($address);
            })->all();

            $this->message->{"{$type}"}(...$addresses);
        } else {
            $this->message->{"add{$type}"}($this->createAddress($address, (string) $name));
        }

        return $this;
    }

    /**
     * Create a safe Symfony address instance.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return \Symfony\Component\Mime\Address
     */
    protected function createAddress($address, $name = null)
    {
        $this->ensureAddressIsSafe($address);

        return new Address($address, (string) $name);
    }

    /**
     * Ensure the given address cannot inject additional headers or commands.
     *
     * @param  mixed  $address
     * @return mixed
     */
    protected function ensureAddressIsSafe($address)
    {
        $addressString = $address instanceof Address ? $address->getAddress() : $address;

        if (is_string($addressString) && preg_match('/[\r\n]/', $addressString) > 0) {
            throw new InvalidArgumentException('Email addresses may not contain line break characters.');
        }

        return $address;
    }

    /**
     * Ensure the given addresses cannot inject additional headers or commands.
     *
     * @param  array  $addresses
     * @return array
     */
    protected function ensureAddressesAreSafe(array $addresses)
    {
        return array_map(fn ($address) => $this->ensureAddressIsSafe($address), $addresses);
    }

    /**
     * Add an address debug header for a list of recipients.
     *
     * @param  string  $header
     * @param  \Symfony\Component\Mime\Address[]  $addresses
     * @return $this
     */
    protected function addAddressDebugHeader(string $header, array $addresses)
    {
        $this->message->getHeaders()->addTextHeader(
            $header,
            implode(', ', array_map(fn ($a) => $a->toString(), $addresses)),
        );

        return $this;
    }

    /**
     * Set the subject of the message.
     *
     * @param  string  $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->message->subject($subject);

        return $this;
    }

    /**
     * Set the message priority level.
     *
     * @param  int  $level
     * @return $this
     */
    public function priority($level)
    {
        $this->message->priority($level);

        return $this;
    }

    /**
     * Attach a file to the message.
     *
     * @param  string|\Illuminate\Contracts\Mail\Attachable|\Illuminate\Mail\Attachment  $file
     * @param  array  $options
     * @return $this
     */
    public function attach($file, array $options = [])
    {
        if ($file instanceof Attachable) {
            $file = $file->toMailAttachment();
        }

        if ($file instanceof Attachment) {
            return $file->attachTo($this);
        }

        $this->message->attachFromPath($file, $options['as'] ?? null, $options['mime'] ?? null);

        return $this;
    }

    /**
     * Attach in-memory data as an attachment.
     *
     * @param  string|resource  $data
     * @param  string  $name
     * @param  array  $options
     * @return $this
     */
    public function attachData($data, $name, array $options = [])
    {
        $this->message->attach($data, $name, $options['mime'] ?? null);

        return $this;
    }

    /**
     * Embed a file in the message and get the CID.
     *
     * @param  string|\Illuminate\Contracts\Mail\Attachable|\Illuminate\Mail\Attachment  $file
     * @return string
     */
    public function embed($file)
    {
        if ($file instanceof Attachable) {
            $file = $file->toMailAttachment();
        }

        if ($file instanceof Attachment) {
            return $file->attachWith(
                function ($path) use ($file) {
                    $part = (new DataPart(new File($path), $file->as, $file->mime))->asInline();

                    $this->message->addPart($part);

                    return "cid:{$part->getContentId()}";
                },
                function ($data) use ($file) {
                    $this->message->addPart(
                        $part = $part = (new DataPart($data(), $file->as, $file->mime))->asInline()
                    );

                    return "cid:{$part->getContentId()}";
                }
            );
        }

        $fileObject = new File($file);

        $this->message->addPart(
            $part = (new DataPart($fileObject, $fileObject->getFilename()))->asInline()
        );

        return "cid:{$part->getContentId()}";
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
        $part = (new DataPart($data, $name, $contentType))->asInline();

        $this->message->addPart($part);

        return "cid:{$part->getContentId()}";
    }

    /**
     * Get the underlying Symfony Email instance.
     *
     * @return \Symfony\Component\Mime\Email
     */
    public function getSymfonyMessage()
    {
        return $this->message;
    }

    /**
     * Dynamically pass missing methods to the Symfony instance.
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
