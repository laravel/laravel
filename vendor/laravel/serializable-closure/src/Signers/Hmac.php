<?php

namespace Laravel\SerializableClosure\Signers;

use Laravel\SerializableClosure\Contracts\Signer;

class Hmac implements Signer
{
    /**
     * The secret key.
     *
     * @var string
     */
    protected $secret;

    /**
     * Creates a new signer instance.
     *
     * @param  string  $secret
     * @return void
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Sign the given serializable.
     *
     * @param  string  $serialized
     * @return array
     */
    public function sign($serialized)
    {
        return [
            'serializable' => $serialized,
            'hash' => base64_encode(hash_hmac('sha256', $serialized, $this->secret, true)),
        ];
    }

    /**
     * Verify the given signature.
     *
     * @param  array{serializable: string, hash: string}  $signature
     * @return bool
     */
    public function verify($signature)
    {
        return hash_equals(base64_encode(
            hash_hmac('sha256', $signature['serializable'], $this->secret, true)
        ), $signature['hash']);
    }
}
