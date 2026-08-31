<?php

namespace Illuminate\Encryption;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\StringEncrypter;
use RuntimeException;

class Encrypter implements EncrypterContract, StringEncrypter
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * The previous / legacy encryption keys.
     *
     * @var array
     */
    protected $previousKeys = [];

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher;

    /**
     * The supported cipher algorithms and their properties.
     *
     * @var array
     */
    private static $supportedCiphers = [
        'aes-128-cbc' => ['size' => 16, 'aead' => false],
        'aes-256-cbc' => ['size' => 32, 'aead' => false],
        'aes-128-gcm' => ['size' => 16, 'aead' => true],
        'aes-256-gcm' => ['size' => 32, 'aead' => true],
    ];

    /**
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  string  $cipher
     *
     * @throws \RuntimeException
     */
    public function __construct($key, $cipher = 'aes-128-cbc')
    {
        $key = (string) $key;

        if (! static::supported($key, $cipher)) {
            $ciphers = implode(', ', array_keys(self::$supportedCiphers));

            throw new RuntimeException("Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}.");
        }

        $this->key = $key;
        $this->cipher = $cipher;
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        if (! isset(self::$supportedCiphers[strtolower($cipher)])) {
            return false;
        }

        return mb_strlen($key, '8bit') === self::$supportedCiphers[strtolower($cipher)]['size'];
    }

    /**
     * Create a new encryption key for the given cipher.
     *
     * @param  string  $cipher
     * @return string
     */
    public static function generateKey($cipher)
    {
        return random_bytes(self::$supportedCiphers[strtolower($cipher)]['size'] ?? 32);
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encrypt(#[\SensitiveParameter] $value, $serialize = true)
    {
        $iv = random_bytes(openssl_cipher_iv_length(strtolower($this->cipher)));

        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            strtolower($this->cipher), $this->key, 0, $iv, $tag
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $iv = base64_encode($iv);
        $tag = base64_encode($tag ?? '');

        $mac = self::$supportedCiphers[strtolower($this->cipher)]['aead']
            ? '' // For AEAD-algorithms, the tag / MAC is returned by openssl_encrypt...
            : $this->hash($iv, $value, $this->key);

        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString(#[\SensitiveParameter] $value)
    {
        return $this->encrypt($value, false);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $this->ensureTagIsValid(
            $tag = empty($payload['tag']) ? null : base64_decode($payload['tag'])
        );

        $foundValidMac = false;

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        foreach ($this->getAllKeys() as $key) {
            if (
                $this->shouldValidateMac() &&
                ! ($foundValidMac = $foundValidMac || $this->validMacForKey($payload, $key))
            ) {
                continue;
            }

            $decrypted = \openssl_decrypt(
                $payload['value'], strtolower($this->cipher), $key, 0, $iv, $tag ?? ''
            );

            if ($decrypted !== false) {
                break;
            }
        }

        if ($this->shouldValidateMac() && ! $foundValidMac) {
            throw new DecryptException('The MAC is invalid.');
        }

        if (($decrypted ?? false) === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @param  string  $key
     * @return string
     */
    protected function hash(#[\SensitiveParameter] $iv, #[\SensitiveParameter] $value, #[\SensitiveParameter] $key)
    {
        return hash_hmac('sha256', $iv.$value, $key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    protected function getJsonPayload($payload)
    {
        if (! is_string($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $this->validPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function validPayload($payload)
    {
        if (! is_array($payload)) {
            return false;
        }

        foreach (['iv', 'value', 'mac'] as $item) {
            if (! isset($payload[$item]) || ! is_string($payload[$item])) {
                return false;
            }
        }

        if (isset($payload['tag']) && ! is_string($payload['tag'])) {
            return false;
        }

        return strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(strtolower($this->cipher));
    }

    /**
     * Determine if the MAC for the given payload is valid for the primary key.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function validMac(array $payload)
    {
        return $this->validMacForKey($payload, $this->key);
    }

    /**
     * Determine if the MAC is valid for the given payload and key.
     *
     * @param  array  $payload
     * @param  string  $key
     * @return bool
     */
    protected function validMacForKey(#[\SensitiveParameter] $payload, $key)
    {
        return hash_equals(
            $this->hash($payload['iv'], $payload['value'], $key), $payload['mac']
        );
    }

    /**
     * Ensure the given tag is a valid tag given the selected cipher.
     *
     * @param  string  $tag
     * @return void
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    protected function ensureTagIsValid($tag)
    {
        if (self::$supportedCiphers[strtolower($this->cipher)]['aead'] && strlen($tag) !== 16) {
            throw new DecryptException('Could not decrypt the data.');
        }

        if (! self::$supportedCiphers[strtolower($this->cipher)]['aead'] && is_string($tag)) {
            throw new DecryptException('Unable to use tag because the cipher algorithm does not support AEAD.');
        }
    }

    /**
     * Determine if we should validate the MAC while decrypting.
     *
     * @return bool
     */
    protected function shouldValidateMac()
    {
        return ! self::$supportedCiphers[strtolower($this->cipher)]['aead'];
    }

    /**
     * Determine if the given value appears to be encrypted by this encrypter.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function appearsEncrypted($value)
    {
        if (! is_string($value)) {
            return false;
        }

        $decoded = base64_decode($value, true);

        if ($decoded === false) {
            return false;
        }

        $payload = json_decode($decoded, true);

        return is_array($payload)
            && isset($payload['iv'], $payload['value'], $payload['mac']);
    }

    /**
     * Get the encryption key that the encrypter is currently using.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get the current encryption key and all previous encryption keys.
     *
     * @return array
     */
    public function getAllKeys()
    {
        return [$this->key, ...$this->previousKeys];
    }

    /**
     * Get the previous encryption keys.
     *
     * @return array
     */
    public function getPreviousKeys()
    {
        return $this->previousKeys;
    }

    /**
     * Set the previous / legacy encryption keys that should be utilized if decryption fails.
     *
     * @param  array  $keys
     * @return $this
     *
     * @throws \RuntimeException
     */
    public function previousKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (! static::supported($key, $this->cipher)) {
                $ciphers = implode(', ', array_keys(self::$supportedCiphers));

                throw new RuntimeException("Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}.");
            }
        }

        $this->previousKeys = $keys;

        return $this;
    }
}
