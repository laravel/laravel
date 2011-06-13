<?php namespace System\DB;

abstract class Eloquent {

	/**
	 * Indicates if the model exists in the database.
	 *
	 * @var bool
	 */
	public $exists = false;

	/**
	 * The model attributes.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * The model's dirty attributes.
	 *
	 * @var array
	 */
	public $dirty = array();

	/**
	 * The model's ignored attributes.
	 *
	 * Ignored attributes will not be saved to the database, and
	 * are primarily used to hold relationships.
	 *
	 * @var array
	 */
	public $ignore = array();

	/**
	 * The relationships that should be eagerly loaded.
	 *
	 * @var array
	 */
	public $includes = array();

	/**
	 * The relationship type the model is currently resolving.
	 *
	 * @var string
	 */
	public $relating;

	/**
	 * The foreign key of the "relating" relationship.
	 *
	 * @var string
	 */
	public $relating_key;

	/**
	 * The table name of the model being resolved. Used during many-to-many eager loading.
	 *
	 * @var string
	 */
	public $relating_table;

	/**
	 * The model query instance.
	 *
	 * @var Query
	 */
	public $query;

	/**
	 * Create a new model instance and set the relationships
	 * that should be eagerly loaded.
	 *
	 * @return mixed
	 */
	public static function with()
	{
		$model = Eloquent\Factory::make(get_called_class());
		$model->includes = func_get_args();

		return $model;
	}

	/**
	 * Get a model by the primary key.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public static function find($id)
	{
		return Eloquent\Factory::make(get_called_class())->where('id', '=', $id)->first();
	}

	/**
	 * Get an array of models from the database.
	 *
	 * @return array
	 */
	private function _get()
	{
		return Eloquent\Hydrate::from($this);
	}

	/**
	 * Get the first model result
	 *
	 * @return mixed
	 */
	private function _first()
	{
		$results = Eloquent\Hydrate::from($this->take(1));

		if (count($results) > 0)
		{
			reset($results);

			return current($results);
		}
	}

	/**
	 * Retrieve the query for a 1:1 relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function has_one($model, $foreign_key = null)
	{
		return Eloquent\Relate::has_one($model, $foreign_key, $this);
	}

	/**
	 * Retrieve the query for a 1:* relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function has_many($model, $foreign_key = null)
	{
		return Eloquent\Relate::has_many($model, $foreign_key, $this);
	}

	/**
	 * Retrieve the query for a 1:1 belonging relationship.
	 *
	 * @param  string  $model
	 * @return mixed
	 */
	public function belongs_to($model)
	{
		// -----------------------------------------------------
		// Get the calling function name.
		// -----------------------------------------------------
		list(, $caller) = debug_backtrace(false);

		return Eloquent\Relate::belongs_to($caller, $model, $this);
	}

	/**
	 * Retrieve the query for a *:* relationship.
	 *
	 * @param  string  $model
	 * @return mixed
	 */
	public function has_many_and_belongs_to($model)
	{
		return Eloquent\Relate::has_many_and_belongs_to($model, $this);
	}

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		if ($this->exists and count($this->dirty) == 0)
		{
			return true;
		}

		return Eloquent\Warehouse::store($this);
	}

	/**
	 * Magic method for retrieving model attributes.
	 */
	public function __get($key)
	{
		// -----------------------------------------------------
		// Check the ignored attributes first.
		// -----------------------------------------------------
		if (array_key_exists($key, $this->ignore))
		{
			return $this->ignore[$key];
		}

		// -----------------------------------------------------
		// Is the attribute actually a relationship?
		// -----------------------------------------------------
		if (method_exists($this, $key))
		{
			// -----------------------------------------------------
			// Get the query / model for the relationship.
			// -----------------------------------------------------
			$model = $this->$key();

			return ($this->relating == 'has_one' or $this->relating == 'belongs_to')
													? $this->ignore[$key] = $model->first()
													: $this->ignore[$key] = $model->get();
		}

		return (array_key_exists($key, $this->attributes)) ? $this->attributes[$key] : null;
	}

	/**
	 * Magic Method for setting model attributes.
	 */
	public function __set($key, $value)
	{
		// -----------------------------------------------------
		// Is the key actually a relationship?
		// -----------------------------------------------------
		if (method_exists($this, $key))
		{
			$this->ignore[$key] = $value;
		}
		else
		{
			$this->attributes[$key] = $value;
			$this->dirty[$key] = $value;
		}
	}

	/**
	 * Magic Method for determining if a model attribute is set.
	 */
	public function __isset($key)
	{
		return (array_key_exists($key, $this->attributes) or array_key_exists($key, $this->ignore));
	}

	/**
	 * Magic Method for unsetting model attributes.
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key]);
		unset($this->ignore[$key]);
		unset($this->dirty[$key]);
	}

	/**
	 * Magic Method for handling dynamic method calls.
	 */
	public function __call($method, $parameters)
	{
		if ($method == 'get')
		{
			return $this->_get();
		}

		if ($method == 'first')
		{
			return $this->_first();
		}

		// -----------------------------------------------------
		// If the method is an aggregate function, just return
		// the aggregate value from the query.
		// -----------------------------------------------------
		if (in_array($method, array('count', 'sum', 'min', 'max', 'avg')))
		{
			return call_user_func_array(array($this->query, $method), $parameters);
		}

		// -----------------------------------------------------
		// Pass the method call to the query instance.
		// -----------------------------------------------------
		call_user_func_array(array($this->query, $method), $parameters);

		return $this;
	}

	/**
	 * Magic Method for handling dynamic static method calls.
	 */
	public static function __callStatic($method, $parameters)
	{
		$model = Eloquent\Factory::make(get_called_class());

		if ($method == 'get')
		{
			return $model->_get();
		}

		if ($method == 'first')
		{
			return $model->_first();
		}

		// -----------------------------------------------------
		// If the method is an aggregate function, just return
		// the aggregate value from the query.
		// -----------------------------------------------------
		if (in_array($method, array('count', 'sum', 'min', 'max', 'avg')))
		{
			return call_user_func_array(array($model->query, $method), $parameters);
		}

		// -----------------------------------------------------
		// Pass the method call to the query instance.
		// -----------------------------------------------------
		call_user_func_array(array($model->query, $method), $parameters);

		return $model;
	}

}