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
		// If the given "models" are not an array, we'll force them into an array so
		// we can conveniently loop through them and insert all of them into the
		// related database table assigned to the associated model instance.
		if ( ! is_array($models)) $models = array($models);

		$current = $this->table->lists($this->model->key());

		foreach ($models as $attributes)
		{
			$class = get_class($this->model);

			// If the "attributes" are actually an array of the related model we'll
			// just use the existing instance instead of creating a fresh model
			// instance for the attributes. This allows for validation.
			if ($attributes instanceof $class)
			{
				$model = $attributes;
			}
			else
			{
				$model = $this->fresh_model($attributes);
			}

			// We'll need to associate the model with its parent, so we'll set the
			// foreign key on the model to the key of the parent model, making
			// sure that the two models are associated in the database.
			$foreign = $this->foreign_key();

			$model->$foreign = $this->base->get_key();

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

		foreach ($parents as &$parent)
		{
			$matching = array_filter($children, function($v) use (&$parent, $foreign)
			{
				return $v->$foreign == $parent->get_key();
			});

			$parent->relationships[$relationship] = array_values($matching);
		}
	}

}