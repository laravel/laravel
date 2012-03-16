<?php namespace Laravel\Database\Eloquent\Relationships;

use Laravel\Database\Eloquent\Model;

class Has_One_Or_Many extends Relationship {

	/**
	 * Insert a new record for the association.
	 *
	 * @param  array  $attributes
	 * @return bool
	 */
	public function insert($attributes)
	{
		$attributes[$this->foreign_key()] = $this->base->get_key();

		return parent::insert($attributes);
	}

	/**
	 * Set the proper constraints on the relationship table.
	 *
	 * @return void
	 */
	protected function constrain()
	{
		$this->table->where($this->foreign_key(), '=', $this->base->get_key());
	}

	/**
	 * Set the proper constraints on the relationship table for an eager load.
	 *
	 * @param  array  $results
	 * @return void
	 */
	public function eagerly_constrain($results)
	{
		$this->table->where_in($this->foreign_key(), array_keys($results));
	}

}