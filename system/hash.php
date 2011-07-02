<?php namespace System;

class Hash {

    /**
	 * The salty, hashed value.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * The salt used during hashing.
	 *
	 * @var string
	 */
	public $salt;
	/**
	* The workfactor for making password cracking harder
	*
	* @var integer
	**/
	
	const DEFAULT_WORK_FACTOR = 8;
	
	/**
	 * Create a new hash instance.
	 *
	 * @param  string  $value
	 * @param  string  $salt
	 * @param  integer $workFactor
	 * @return void
	 */
	public function __construct($value, $salt = null, $workFactor=0)
	{
		// -------------------------------------------------------
		// Need to check that the work factor is acceptable 
		// 
		// Otherwise, we will use the default of 8
		// -------------------------------------------------------
		if ($work_factor < 4 || $work_factor > 31) $work_factor = self::DEFAULT_WORK_FACTOR;

		// -------------------------------------------------------
		// If no salt is given, we'll create a random salt to
		// use when hashing the password.
		//
		// Otherwise, we will use the given salt.
		// -------------------------------------------------------
		
		if(is_null($salt)){
			$this->salt = '$2a$' . str_pad($work_factor, 2, '0', STR_PAD_LEFT) . '$' .
			        substr(
			            strtr(base64_encode(Str::random(16)), '+', '.'), 
			            0, 22
			        )
			    ;
		}else{
			$this->salt = $salt;
		}
		
		$this->value = crypt($value, $this->salt);
	}

	/**
	 * Factory for creating hash instances.
	 *
	 * @access public
	 * @param  string  $value
	 * @param  string  $salt
	 * @param integer $workFactor
	 * @return Hash
	 */
	public static function make($value, $salt = null, $workFactor = 0)
	{
		return new self($value, $salt, $workFactor );
	}

}	