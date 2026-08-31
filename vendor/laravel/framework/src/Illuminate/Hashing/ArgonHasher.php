<?php

namespace Illuminate\Hashing;

use Error;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use RuntimeException;

class ArgonHasher extends AbstractHasher implements HasherContract
{
    /**
     * The default memory cost factor.
     *
     * @var int
     */
    protected $memory = 1024;

    /**
     * The default time cost factor.
     *
     * @var int
     */
    protected $time = 2;

    /**
     * The default threads factor.
     *
     * @var int
     */
    protected $threads = 2;

    /**
     * Indicates whether to perform an algorithm check.
     *
     * @var bool
     */
    protected $verifyAlgorithm = false;

    /**
     * Create a new hasher instance.
     *
     * @param  array  $options
     */
    public function __construct(array $options = [])
    {
        $this->time = $options['time'] ?? $this->time;
        $this->memory = $options['memory'] ?? $this->memory;
        $this->threads = $this->threads($options);
        $this->verifyAlgorithm = $options['verify'] ?? $this->verifyAlgorithm;
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public function make(#[\SensitiveParameter] $value, array $options = [])
    {
        try {
            $hash = password_hash($value, $this->algorithm(), [
                'memory_cost' => $this->memory($options),
                'time_cost' => $this->time($options),
                'threads' => $this->threads($options),
            ]);
        } catch (Error) {
            throw new RuntimeException('Argon2 hashing not supported.');
        }

        return $hash;
    }

    /**
     * Get the algorithm that should be used for hashing.
     *
     * @return int
     */
    protected function algorithm()
    {
        return PASSWORD_ARGON2I;
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
            throw new RuntimeException('This password does not use the Argon2i algorithm.');
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
        return password_needs_rehash($hashedValue, $this->algorithm(), [
            'memory_cost' => $this->memory($options),
            'time_cost' => $this->time($options),
            'threads' => $this->threads($options),
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
        return $this->info($hashedValue)['algoName'] === 'argon2i';
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

        if (
            ! is_int($options['memory_cost'] ?? null) ||
            ! is_int($options['time_cost'] ?? null) ||
            ! is_int($options['threads'] ?? null)
        ) {
            return false;
        }

        if (
            $options['memory_cost'] > $this->memory ||
            $options['time_cost'] > $this->time ||
            $options['threads'] > $this->threads
        ) {
            return false;
        }

        return true;
    }

    /**
     * Set the default password memory factor.
     *
     * @param  int  $memory
     * @return $this
     */
    public function setMemory(int $memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * Set the default password timing factor.
     *
     * @param  int  $time
     * @return $this
     */
    public function setTime(int $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Set the default password threads factor.
     *
     * @param  int  $threads
     * @return $this
     */
    public function setThreads(int $threads)
    {
        $this->threads = $threads;

        return $this;
    }

    /**
     * Extract the memory cost value from the options array.
     *
     * @param  array  $options
     * @return int
     */
    protected function memory(array $options)
    {
        return $options['memory'] ?? $this->memory;
    }

    /**
     * Extract the time cost value from the options array.
     *
     * @param  array  $options
     * @return int
     */
    protected function time(array $options)
    {
        return $options['time'] ?? $this->time;
    }

    /**
     * Extract the thread's value from the options array.
     *
     * @param  array  $options
     * @return int
     */
    protected function threads(array $options)
    {
        if (defined('PASSWORD_ARGON2_PROVIDER') && PASSWORD_ARGON2_PROVIDER === 'sodium') {
            return 1;
        }

        return $options['threads'] ?? $this->threads;
    }
}
