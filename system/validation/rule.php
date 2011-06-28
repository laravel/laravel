<?php namespace System\Validation;

use System\Lang;

abstract class Rule {

	/**
	 * The attributes being validated by the rule.
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * The validation error message.
	 *
	 * @var string
	 */
	public $message;

	/**
	 * The error type. This is used for rules that have more than
	 * one type of error such as Size_Of and Upload_Of.
	 *
	 * @var string
	 */
	public $error;

	/**
	 * Create a new validation Rule instance.
	 *
	 * @param  array      $attributes
	 * @return void
	 */
	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * Run the validation rule.
	 *
	 * @param  array            $attributes
	 * @param  Error_Collector  $errors
	 * @return void
	 */
	public function validate($attributes, $errors)
	{
		foreach ($this->attributes as $attribute)
		{
			$this->error = null;

			if ( ! $this->check($attribute, $attributes))
			{
				$errors->add($attribute, Message::get($this, $attribute));
			}
		}
	}

	/**
	 * Set the validation error message.
	 *
	 * @param  string  $message
	 * @return Rule
	 */
	public function message($message)
	{
		$this->message = $message;
		return $this;
	}

}