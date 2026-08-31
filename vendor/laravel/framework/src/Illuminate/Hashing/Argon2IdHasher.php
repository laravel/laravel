<?php

namespace Illuminate\Hashing;

use RuntimeException;

class Argon2IdHasher extends ArgonHasher
{
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
            throw new RuntimeException('This password does not use the Argon2id algorithm.');
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Verify the hashed value's algorithm.
     *
     * @param  string  $hashedValue
     * @return bool
     */
    protected function isUsingCorrectAlgorithm($hashedValue)
    {
        return $this->info($hashedValue)['algoName'] === 'argon2id';
    }

    /**
     * Get the algorithm that should be used for hashing.
     *
     * @return int
     */
    protected function algorithm()
    {
        return PASSWORD_ARGON2ID;
    }
}
