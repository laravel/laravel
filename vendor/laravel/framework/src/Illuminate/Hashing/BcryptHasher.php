<?php

namespace Illuminate\Hashing;

use Error;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use InvalidArgumentException;
use RuntimeException;

class BcryptHasher extends AbstractHasher implements HasherContract
{
    /**
     * The default cost factor.
     *
     * @var int
     */
    protected $rounds = 12;

    /**
     * Indicates whether to perform an algorithm check.
     *
     * @var bool
     */
    protected $verifyAlgorithm = false;

    /**
     * The maximum allowed length of strings that can be hashed.
     *
     * @var int|null
     */
    protected $limit;

    /**
     * Create a new hasher instance.
     *
     * @param  array  $options
     */
    public function __construct(array $options = [])
    {
        $this->rounds = $options['rounds'] ?? $this->rounds;
        $this->verifyAlgorithm = $options['verify'] ?? $this->verifyAlgorithm;
        $this->limit = $options['limit'] ?? $this->limit;
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function make(#[\SensitiveParameter] $value, array $options = [])
    {
        try {
            if ($this->limit && strlen($value) > $this->limit) {
                throw new InvalidArgumentException('Value is too long to hash. Value must be less than '.$this->limit.' bytes.');
            }

            $hash = password_hash($value, PASSWORD_BCRYPT, [
                'cost' => $this->cost($options),
            ]);
        } catch (Error) {
            throw new RuntimeException('Bcrypt hashing not supported.');
        }

        return $hash;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function check(#[\SensitiveParameter] $value, $hashedValue, array $options = [])
    {
        if (is_null($hashedValue) || strlen($hashedValue) === 0) {
            return false;
        }

        if ($this->verifyAlgorithm && ! $this->isUsingCorrectAlgorithm($hashedValue)) {
            throw new RuntimeException('This password does not use the Bcrypt algorithm.');
        }

        return parent::check($value, $hashedValue, $options);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
            'cost' => $this->cost($options),
        ]);
    }

    /**
     * Verifies that the configuration is less than or equal to what is configured.
     *
     * @internal
     */
    public function verifyConfiguration($value)
    {
        return $this->isUsingCorrectAlgorithm($value) && $this->isUsingValidOptions($value);
    }

    /**
     * Verify the hashed value's algorithm.
     *
     * @param  string  $hashedValue
     * @return bool
     */
    protected function isUsingCorrectAlgorithm($hashedValue)
    {
        return $this->info($hashedValue)['algoName'] === 'bcrypt';
    }

    /**
     * Verify the hashed value's options.
     *
     * @param  string  $hashedValue
     * @return bool
     */
    protected function isUsingValidOptions($hashedValue)
    {
        ['options' => $options] = $this->info($hashedValue);

        if (! is_int($options['cost'] ?? null)) {
            return false;
        }

        if ($options['cost'] > $this->rounds) {
            return false;
        }

        return true;
    }

    /**
     * Set the default password work factor.
     *
     * @param  int  $rounds
     * @return $this
     */
    public function setRounds($rounds)
    {
        $this->rounds = (int) $rounds;

        return $this;
    }

    /**
     * Extract the cost value from the options array.
     *
     * @param  array  $options
     * @return int
     */
    protected function cost(array $options = [])
    {
        return $options['rounds'] ?? $this->rounds;
    }
}
