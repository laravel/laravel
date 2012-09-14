<?php namespace Laravel\Database;

class Exception extends \Exception {

	/**
	 * The inner exception.
	 *
	 * @var Exception
	 */
	protected $inner;

	/**
	 * Create a new database exception instance.
	 *
	 * @param  string     $sql
	 * @param  array      $bindings
	 * @param  Exception  $inner
	 * @return void
	 */
	public function __construct($sql, $bindings, \Exception $inner)
	{
		$this->inner = $inner;

		$this->setMessage($sql, $bindings);
		$this->setCode();
	}

	/**
	 * Set the exception message to include the SQL and bindings.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return void
	 */
	protected function setMessage($sql, $bindings)
	{
		$this->message = $this->inner->getMessage();

		$this->message .= "\n\nSQL: ".$sql."\n\nBindings: ".var_export($bindings, true);
	}
	
	/**
	 * Set the exception code.
	 *
	 * @return void
	 */        
	protected function setCode()
	{
		$this->code = $this->inner->getCode();
	}

}