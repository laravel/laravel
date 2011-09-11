<?php namespace Laravel\Security\Hashing;

class Bcrypt implements Engine {

	/**
	 * The number of iterations that should be performed.
	 *
	 * @var int
	 */
	protected $rounds;

	/**
	 * Create a new bcrypt hashing engine.
	 *
	 * @param  int   $rounds
	 * @return void
	 */
	public function __construct($rounds)
	{
		$this->rounds = $rounds;

		if ( ! function_exists('openssl_random_pseudo_bytes'))
		{
			throw new \Exception("The openssl PHP extension is required to perform bcrypt hashing.");
		}
	}

	/**
	 * Perform a one-way hash on a string using bcrypt.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function hash($value)
	{
		$salt = sprintf('$2a$%02d$', $this->rounds).substr(base64_encode(openssl_random_pseudo_bytes(16)), 0, 22);

		return crypt($value, str_replace('+', '.', $salt));
	}

	/**
	 * Determine if an unhashed value matches a given hash.
	 *
	 * @param  string  $value
	 * @param  string  $hash
	 * @return bool
	 */
	public function check($value, $hash)
	{
		return crypt($value, $hash) === $hash;
	}

}