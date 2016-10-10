<?php namespace Illuminate\Hashing;

class BcryptHasher implements HasherInterface {

	/**
	 * Default crypt cost factor.
	 *
	 * @var int
	 */
	protected $rounds = 10;

	/**
	 * Hash the given value.
	 *
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	public function make($value, array $options = array())
	{
		$cost = isset($options['rounds']) ? $options['rounds'] : $this->rounds;

		$hash = password_hash($value, PASSWORD_BCRYPT, array('cost' => $cost));

		if ($hash === false)
		{
			throw new \RuntimeException("Bcrypt hashing not supported.");
		}

		return $hash;
	}

	/**
	 * Check the given plain value against a hash.
	 *
	 * @param  string  $value
	 * @param  string  $hashedValue
	 * @param  array   $options
	 * @return bool
	 */
	public function check($value, $hashedValue, array $options = array())
	{
		return password_verify($value, $hashedValue);
	}

	/**
	 * Check if the given hash has been hashed using the given options.
	 *
	 * @param  string  $hashedValue
	 * @param  array   $options
	 * @return bool
	 */
	public function needsRehash($hashedValue, array $options = array())
	{
		$cost = isset($options['rounds']) ? $options['rounds'] : $this->rounds;

		return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, array('cost' => $cost));
	}

}
