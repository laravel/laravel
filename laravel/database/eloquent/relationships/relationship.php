<?php namespace Laravel\Database\Eloquent\Relationships;

use Laravel\Database\Eloquent\Model;
use Laravel\Database\Eloquent\Query;

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

		// We will go ahead and set the model and associated instances on the
		// relationship to match the relationship targets passed in from the
		// model. These will allow us to gather the relationship info.
		if ($associated instanceof Model)
		{
			$this->model = $associated;
		}
		else
		{
			$this->model = new $associated;
		}

		// For relationships, we'll set the base model to be the model being
		// associated from. This model contains the value of the foreign
		// key needed to connect to the associated model.
		if ($model instanceof Model)
		{
			$this->base = $model;
		}
		else
		{
			$this->base = new $model;
		}

		// Next we'll set the fluent query builder for the relationship and
		// constrain the query such that it only returns the models that
		// are appropriate for the relationship.
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

		// If the model is an object we'll simply get the class of the object and
		// then take the basename, which is simply the object name minus the
		// namespace, and we'll append "_id" to the name.
		if (is_object($model))
		{
			$model = class_basename($model);
		}

		return strtolower(basename($model).'_id');
	}

	/**
	 * Get a freshly instantiated instance of the related model class.
	 *
	 * @param  array  $attributes
	 * @return Model
	 */
	protected function fresh_model($attributes = array())
	{
		$class = get_class($this->model);

		return new $class($attributes);
	}

	/**
	 * Get the foreign key for the relationship.
	 *
	 * @return string
	 */
	public function foreign_key()
	{
		return static::foreign($this->base, $this->foreign);
	}

}