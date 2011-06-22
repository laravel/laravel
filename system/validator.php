<?php namespace System;

class Validator {

	/**
	 * The attributes being validated.
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * The validation error collector.
	 *
	 * @var Error_Collector
	 */
	public $errors;

	/**
	 * The validation rules.
	 *
	 * @var array
	 */
	public $rules = array();

	/**
	 * Create a new Validator instance.
	 *
	 * @param  mixed  $target
	 * @return void
	 */
	public function __construct($target)
	{
		// ---------------------------------------------------------
		// If the source is an Eloquent model, use the model's
		// attributes as the validation attributes.
		// ---------------------------------------------------------
		$this->attributes = ($target instanceof DB\Eloquent) ? $target->attributes : (array) $target;

		$this->errors = new Validation\Error_Collector;
	}

	/**
	 * Create a new Validator instance.
	 *
	 * @param  mixed      $target
	 * @return Validator
	 */
	public static function of($target)
	{
		return new static($target);
	}

	/**
	 * Determine if the attributes pass all of the validation rules.
	 *
	 * @return bool
	 */
	public function is_valid()
	{
		$this->errors->messages = array();

		foreach ($this->rules as $rule)
		{
			// ---------------------------------------------------------
			// The error collector is passed to the rule so that the
			// rule may conveniently add error messages.
			// ---------------------------------------------------------
			$rule->validate($this->attributes, $this->errors);
		}

		return count($this->errors->messages) == 0;
	}

	/**
	 * Magic Method for dynamically creating validation rules.
	 */
	public function __call($method, $parameters)
	{
		// ---------------------------------------------------------
		// Check if the validation rule is defined in the rules
		// directory. If it is, create a new rule and return it.
		// ---------------------------------------------------------
		if (file_exists(SYS_PATH.'validation/rules/'.$method.EXT))
		{
			$rule = '\\System\\Validation\\Rules\\'.$method;

			return $this->rules[] = new $rule($parameters);
		}

		throw new \Exception("Method [$method] does not exist on Validator class.");
	}

}