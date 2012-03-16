<?php namespace Laravel\Database\Eloquent\Relationships; use Eloquent\Model, Eloquent\Query;

abstract class Relationship extends Query {

	/**
	 * The base model for the relationship.
	 *
	 * @var Model
	 */
	protected $base;

	/**
	 * Create a new has one or many association instance.
	 *
	 * @param  Model   $model
	 * @param  string  $associated
	 * @param  string  $foreign
	 * @return void
	 */
	public function __construct($model, $associated, $foreign)
	{
		$this->foreign = $foreign;

		// We will go ahead and set the model and associated instances on the relationship
		// to match the relationship targets passed in from the model. These will allow
		// us to gather more inforamtion on the relationship.
		$this->model = ($associated instanceof Model) ? $associated : new $associated;

		if ($model instanceof Model)
		{
			$this->base = $model;
		}
		else
		{
			$this->base = new $model;
		}

		// Next we'll set the fluent query builder for the relationship and constrain
		// the query such that it only returns the models that are appropriate for
		// the relationship, typically by setting the foreign key.
		$this->table = $this->query();

		$this->constrain();
	}

	/**
	 * Get the foreign key name for the given model.
	 *
	 * @param  string  $model
	 * @param  string  $foreign
	 * @return string
	 */
	public static function foreign($model, $foreign = null)
	{
		if ( ! is_null($foreign)) return $foreign;

		// If the model is an object, we will simply get the class of the object and
		// then take the basename, which is simply the object name minus the
		// namespace, and we'll append "_id" to the name.
		if (is_object($model))
		{
			$model = get_class($model);
		}

		return strtolower(basename($model).'_id');
	}

	/**
	 * Get the foreign key for the relationship.
	 *
	 * @return string
	 */
	protected function foreign_key()
	{
		return Relationship::foreign($this->base, $this->foreign);
	}

}