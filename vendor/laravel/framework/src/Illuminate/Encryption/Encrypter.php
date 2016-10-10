<?php namespace Illuminate\Encryption;

use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Security\Core\Util\SecureRandom;

class DecryptException extends \RuntimeException {}

class Encrypter {

	/**
	 * The encryption key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * The algorithm used for encryption.
	 *
	 * @var string
	 */
	protected $cipher = 'rijndael-256';

	/**
	 * The mode used for encryption.
	 *
	 * @var string
	 */
	protected $mode = 'cbc';

	/**
	 * The block size of the cipher.
	 *
	 * @var int
	 */
	protected $block = 32;

	/**
	 * Create a new encrypter instance.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __construct($key)
	{
		$this->key = $key;
	}

	/**
	 * Encrypt the given value.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function encrypt($value)
	{
		$iv = mcrypt_create_iv($this->getIvSize(), $this->getRandomizer());

		$value = base64_encode($this->padAndMcrypt($value, $iv));

		// Once we have the encrypted value we will go ahead base64_encode the input
		// vector and create the MAC for the encrypted value so we can verify its
		// authenticity. Then, we'll JSON encode the data in a "payload" array.
		$mac = $this->hash($iv = base64_encode($iv), $value);

		return base64_encode(json_encode(compact('iv', 'value', 'mac')));
	}

	/**
	 * Pad and use mcrypt on the given value and input vector.
	 *
	 * @param  string  $value
	 * @param  string  $iv
	 * @return string
	 */
	protected function padAndMcrypt($value, $iv)
	{
		$value = $this->addPadding(serialize($value));

		return mcrypt_encrypt($this->cipher, $this->key, $value, $this->mode, $iv);
	}

	/**
	 * Decrypt the given value.
	 *
	 * @param  string  $payload
	 * @return string
	 */
	public function decrypt($payload)
	{
		$payload = $this->getJsonPayload($payload);

		// We'll go ahead and remove the PKCS7 padding from the encrypted value before
		// we decrypt it. Once we have the de-padded value, we will grab the vector
		// and decrypt the data, passing back the unserialized from of the value.
		$value = base64_decode($payload['value']);

		$iv = base64_decode($payload['iv']);

		return unserialize($this->stripPadding($this->mcryptDecrypt($value, $iv)));
	}

	/**
	 * Run the mcrypt decryption routine for the value.
	 *
	 * @param  string  $value
	 * @param  string  $iv
	 * @return string
	 */
	protected function mcryptDecrypt($value, $iv)
	{
		return mcrypt_decrypt($this->cipher, $this->key, $value, $this->mode, $iv);
	}

	/**
	 * Get the JSON array from the given payload.
	 *
	 * @param  string  $payload
	 * @return array
	 *
	 * @throws DecryptException
	 */
	protected function getJsonPayload($payload)
	{
		$payload = json_decode(base64_decode($payload), true);

		// If the payload is not valid JSON or does not have the proper keys set we will
		// assume it is invalid and bail out of the routine since we will not be able
		// to decrypt the given value. We'll also check the MAC for this encryption.
		if ( ! $payload || $this->invalidPayload($payload))
		{
			throw new DecryptException("Invalid data.");
		}

		if ( ! $this->validMac($payload))
		{
			throw new DecryptException("MAC is invalid.");
		}

		return $payload;
	}

	/**
	 * Determine if the MAC for the given payload is valid.
	 *
	 * @param  array  $payload
	 * @return bool
	 */
	protected function validMac(array $payload)
	{
		$bytes = with(new SecureRandom)->nextBytes(16);

		$calcMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);

		return StringUtils::equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calcMac);
	}

	/**
	 * Create a MAC for the given value.
	 *
	 * @param  string  $iv
	 * @param  string  $value
	 * @return string
	 */
	protected function hash($iv, $value)
	{
		return hash_hmac('sha256', $iv.$value, $this->key);
	}

	/**
	 * Add PKCS7 padding to a given value.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function addPadding($value)
	{
		$pad = $this->block - (strlen($value) % $this->block);

		return $value.str_repeat(chr($pad), $pad);
	}

	/**
	 * Remove the padding from the given value.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function stripPadding($value)
	{
		$pad = ord($value[($len = strlen($value)) - 1]);

		return $this->paddingIsValid($pad, $value) ? substr($value, 0, $len - $pad) : $value;
	}

	/**
	 * Determine if the given padding for a value is valid.
	 *
	 * @param  string  $pad
	 * @param  string  $value
	 * @return bool
	 */
	protected function paddingIsValid($pad, $value)
	{
		$beforePad = strlen($value) - $pad;

		return substr($value, $beforePad) == str_repeat(substr($value, -1), $pad);
	}

	/**
	 * Verify that the encryption payload is valid.
	 *
	 * @param  array|mixed  $data
	 * @return bool
	 */
	protected function invalidPayload($data)
	{
		return ! is_array($data) || ! isset($data['iv']) || ! isset($data['value']) || ! isset($data['mac']);
	}

	/**
	 * Get the IV size for the cipher.
	 *
	 * @return int
	 */
	protected function getIvSize()
	{
		return mcrypt_get_iv_size($this->cipher, $this->mode);
	}

	/**
	 * Get the random data source available for the OS.
	 *
	 * @return int
	 */
	protected function getRandomizer()
	{
		if (defined('MCRYPT_DEV_URANDOM')) return MCRYPT_DEV_URANDOM;

		if (defined('MCRYPT_DEV_RANDOM')) return MCRYPT_DEV_RANDOM;

		mt_srand();

		return MCRYPT_RAND;
	}

	/**
	 * Set the encryption key.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * Set the encryption cipher.
	 *
	 * @param  string  $cipher
	 * @return void
	 */
	public function setCipher($cipher)
	{
		$this->cipher = $cipher;
	}

	/**
	 * Set the encryption mode.
	 *
	 * @param  string  $mode
	 * @return void
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;
	}

}
