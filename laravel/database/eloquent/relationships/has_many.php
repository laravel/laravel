<?php namespace Laravel\Database\Eloquent\Relationships;

class Has_Many extends Has_One_Or_Many {

	/**
	 * Get the properly hydrated results for the relationship.
	 *
	 * @return array
	 */
	public function results()
	{
		return parent::get();
	}

	/**
	 * Sync the association table with an array of models.
	 *
	 * @param  mixed  $models
	 * @return bool
	 */
	public function save($models)
	{
		if ( ! is_array($models)) $models = array($models);

		$current = $this->table->lists($this->model->key());

		foreach ($models as $attributes)
		{
			$attributes[$this->foreign_key()] = $this->base->get_key();

			// If the "attributes" are actually an array of the related model we'll
			// just use the existing instance instead of creating a fresh model
			// instance for the attributes. This allows for validation.
			if ($attributes instanceof get_class($this->model))
			{
				$model = $attributes;
			}
			else
			{
				$model = $this->fresh_model($attributes);
			}

			$id = $model->get_key();

			$model->exists = ( ! is_null($id) and in_array($id, $current));

			// Before saving we'll force the entire model to be "dirty" so all of
			// the attributes are saved. It shouldn't affect the updates as
			// saving all the attributes shouldn't hurt anything.
			$model->original = array();

			$model->save();
		}

		return true;
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
			$parent->relationships[$relationship] = array();
		}
	}

	/**
	 * Match eagerly loaded child models to their parent models.
	 *
	 * @param  array  $parents
	 * @param  array  $children
	 * @return void
	 */
	public function match($relationship, &$parents, $children)
	{
		$foreign = $this->foreign_key();

		foreach ($children as $key => $child)
		{
			$parents[$child->$foreign]->relationships[$relationship][$child->get_key()] = $child;
		}
	}

}