<?php namespace System\Validation\Rules;

use System\DB;
use System\DB\Eloquent;
use System\Validation\Rule;

class Uniqueness_Of extends Rule {

	/**
	 * The database table that should be checked.
	 *
	 * @var string
	 */
	public $table;

	/**
	 * The database column that should be checked.
	 *
	 * @var string
	 */
	public $column;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return void
	 */
	public function check($attribute, $attributes)
	{
		if ( ! array_key_exists($attribute, $attributes))
		{
			return true;
		}

		if (is_null($this->column))
		{
			$this->column = $attribute;
		}

		return DB::table($this->table)->where($this->column, '=', $attributes[$attribute])->count() == 0;
	}

	/**
	 * Set the database table and column.
	 *
	 * @param  string  $table
	 * @param  string  $column
	 * @return Uniqueness_Of
	 */
	public function on($table, $column = null)
	{
		$this->table = $table;
		$this->column = $column;

		return $this;
	}

}