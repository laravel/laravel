<?php namespace System\Validation\Rules;

use System\DB;
use System\Validation\Nullable_Rule;

class Uniqueness_Of extends Nullable_Rule {

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
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		if ( ! is_null($nullable = parent::check($attribute, $attributes)))
		{
			return $nullable;
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
	 * The attribute name will be used as the column name if no other
	 * column name is specified.
	 *
	 * @param  string         $table
	 * @param  string         $column
	 * @return Uniqueness_Of
	 */
	public function on($table, $column = null)
	{
		$this->table = $table;
		$this->column = $column;

		return $this;
	}

}