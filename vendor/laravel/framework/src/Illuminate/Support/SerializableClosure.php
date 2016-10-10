<?php namespace Illuminate\Support;

use Jeremeamia\SuperClosure\SerializableClosure as SuperClosure;

/**
 * Extends SuperClosure for backwards compatibility.
 */
class SerializableClosure extends SuperClosure {

	/**
	 * The code for the closure
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * The variables that were "used" or imported from the parent scope
	 *
	 * @var array
	 */
	protected $variables;

	/**
	 * Returns the code of the closure being serialized
	 *
	 * @return string
	 */
	public function getCode()
	{
		$this->determineCodeAndVariables();

		return $this->code;
	}

	/**
	 * Returns the "used" variables of the closure being serialized
	 *
	 * @return array
	 */
	public function getVariables()
	{
		$this->determineCodeAndVariables();

		return $this->variables;
	}

	/**
	 * Uses the serialize method directly to lazily fetch the code and variables if needed
	 */
	protected function determineCodeAndVariables()
	{
		if (!$this->code)
		{
			list($this->code, $this->variables) = unserialize($this->serialize());
		}
	}

}
