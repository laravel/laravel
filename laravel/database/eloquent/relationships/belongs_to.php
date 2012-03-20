<?php namespace Laravel\Database\Eloquent\Relationships;

class Belongs_To extends Relationship {

	/**
	 * Get the properly hydrated results for the relationship.
	 *
	 * @return Model
	 */
	public function results()
	{
		return parent::first();
	}

	/**
	 * Update the parent model of the relationship.
	 *
	 * @param  array  $attributes
	 * @return int
	 */
	public function update($attributes)
	{
		return $this->model->update($this->foreign_value(), $attributes);
	}

	/**
	 * Set the proper constraints on the relationship table.
	 *
	 * @return void
	 */
	protected function constrain()
	{
		$this->table->where($this->base->key(), '=', $this->foreign_value());
	}

	/**
	 * Initialize a relationship on an array of parent models.
	 *
	 * @param  array   $parents
	 * @param  string  $relationship
	 * @return void
	 */
	public function initialize(&$parents, $relationship)
	{
		foreach ($parents as &$parent)
		{
			$parent->relationships[$relationship] = null;
		}
	}

	/**
	 * Set the proper constraints on the relationship table for an eager load.
	 *
	 * @param  array  $results
	 * @return void
	 */
	public function eagerly_constrain($results)
	{
		$keys = array();

		// Inverse one-to-many relationships require us to gather the keys from the
		// parent models and use those keys when setting the constraint since we
		// are looking for the parent of a child model in this relationship.
		foreach ($results as $result)
		{
			$keys[] = $result->{$this->foreign_key()};
		}

		$this->table->where_in($this->model->key(), array_unique($keys));
	}

	/**
	 * Match eagerly loaded child models to their parent models.
	 *
	 * @param  array  $children
	 * @param  array  $parents
	 * @return void
	 */
	public function match($relationship, &$children, $parents)
	{
		$foreign = $this->foreign_key();

		foreach ($children as &$child)
		{
			if (array_key_exists($child->$foreign, $parents))
			{
				$child->relationships[$relationship] = $parents[$child->$foreign];
			}
		}
	}

	/**
	 * Get the value of the foreign key from the base model.
	 *
	 * @return mixed
	 */
	public function foreign_value()
	{
		return $this->base->get_attribute($this->foreign);
	}

}