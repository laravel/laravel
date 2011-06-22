<?php namespace System\Validation;

abstract class Rule {

	/**
	 * The attributes being validated.
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
	 * Create a new validation Rule instance.
	 *
	 * @param  array      $attributes
	 * @param  Validator  $class
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
		if (is_null($this->message))
		{
			throw new \Exception("An error message must be specified for every Eloquent validation rule.");
		}

		foreach ($this->attributes as $attribute)
		{
			if ( ! $this->check($attribute, $attributes))
			{
				$errors->add($attribute, $this->prepare_message($attribute));
			}
		}
	}

	/**
	 * Prepare the message to be added to the error collector.
	 *
	 * Attribute and size place-holders will replace with their actual values.
	 *
	 * @param  string  $attribute
	 * @return string
	 */
	private function prepare_message($attribute)
	{
		$message = $this->message;

		if (strpos($message, ':attribute'))
		{
			$message = str_replace(':attribute', Lang::line('attributes.'.$attribute)->get(), $message);
		}

		if ($this instanceof Rules\Size_Of)
		{
			$message = str_replace(':max', $this->maximum, $message);
			$message = str_replace(':min', $this->minimum, $message);
			$message = str_replace(':size', $this->length, $message);
		}

		return $message;
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