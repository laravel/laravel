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
	 * Get the table name for a model.
	 *
	 * @param  string  $class
	 * @return string
	 */
	public static function table($class)
	{
		// -----------------------------------------------------
		// Check for a table name override.
		// -----------------------------------------------------
		if (property_exists($class, 'table'))
		{
			return $class::$table;
		}

		return \System\Str::lower(\System\Inflector::plural($class));
	}

	/**
	 * Factory for creating new Eloquent model instances.
	 *
	 * @param  string  $class
	 * @return object
	 */
	public static function make($class)
	{
		// -----------------------------------------------------
		// Instantiate the Eloquent model.
		// -----------------------------------------------------
		$model = new $class;
		
		// -----------------------------------------------------
		// Set the fluent query builder on the model.
		// -----------------------------------------------------
		$model->query = Query::table(static::table($class));

		return $model;
	}

	/**
	 * Create a new model instance and set the relationships
	 * that should be eagerly loaded.
	 *
	 * @return mixed
	 */
	public static function with()
	{
		// -----------------------------------------------------
		// Create a new model instance.
		// -----------------------------------------------------
		$model = static::make(get_called_class());

		// -----------------------------------------------------
		// Set the relationships that should be eager loaded.
		// -----------------------------------------------------
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
		return static::make(get_called_class())->where('id', '=', $id)->first();
	}

	/**
	 * Get an array of models from the database.
	 *
	 * @return array
	 */
	private function _get()
	{
		return Eloquent\Hydrator::hydrate($this);
	}

	/**
	 * Get the first model result
	 *
	 * @return mixed
	 */
	private function _first()
	{
		return (count($results = Eloquent\Hydrator::hydrate($this->take(1))) > 0) ? reset($results) : null;
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
		$this->relating = __FUNCTION__;
		return $this->has_one_or_many($model, $foreign_key);
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
		$this->relating = __FUNCTION__;
		return $this->has_one_or_many($model, $foreign_key);
	}

	/**
	 * Retrieve the query for a 1:1 or 1:* relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	private function has_one_or_many($model, $foreign_key)
	{
		// -----------------------------------------------------
		// Determine the foreign key for the relationship.
		//
		// The foreign key is typically the name of the related
		// model with an appeneded _id.
		// -----------------------------------------------------
		$this->relating_key = (is_null($foreign_key)) ? \System\Str::lower(get_class($this)).'_id' : $foreign_key;

		return static::make($model)->where($this->relating_key, '=', $this->id);
	}

	/**
	 * Retrieve the query for a 1:1 belonging relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function belongs_to($model, $foreign_key = null)
	{
		$this->relating = __FUNCTION__;

		// -----------------------------------------------------
		// Determine the foreign key of the relationship.
		// -----------------------------------------------------
		if ( ! is_null($foreign_key))
		{
			$this->relating_key = $foreign_key;
		}
		else
		{
			// -----------------------------------------------------
			// Get the calling function name.
			// -----------------------------------------------------
			list(, $caller) = debug_backtrace(false);

			// -----------------------------------------------------
			// Determine the foreign key for the relationship.
			//
			// The foreign key for belonging relationships is the
			// name of the relationship method with an appended _id.
			// -----------------------------------------------------
			$this->relating_key = $caller['function'].'_id';
		}

		return static::make($model)->where('id', '=', $this->attributes[$this->relating_key]);
	}

	/**
	 * Retrieve the query for a *:* relationship.
	 *
	 * @param  string  $model
	 * @param  string  $table
	 * @return mixed
	 */
	public function has_and_belongs_to_many($model, $table = null)
	{
		$this->relating = __FUNCTION__;

		// -----------------------------------------------------
		// Determine the intermediate table name.
		// -----------------------------------------------------
		if ( ! is_null($table))
		{
			$this->relating_table = $table;
		}
		else
		{
			// -----------------------------------------------------
			// By default, the intermediate table name is the plural
			// names of the models arranged alphabetically and
			// concatenated with an underscore.
			// -----------------------------------------------------
			$models = array(\System\Inflector::plural($model), \System\Inflector::plural(get_class($this)));
			sort($models);

			$this->relating_table = \System\Str::lower($models[0].'_'.$models[1]);
		}

		// -----------------------------------------------------
		// Determine the foreign key for the relationship.
		// -----------------------------------------------------
		$this->relating_key = $this->relating_table.'.'.\System\Str::lower(get_class($this)).'_id';

		return static::make($model)
						->select(static::table($model).'.*')
						->join($this->relating_table, static::table($model).'.id', '=', $this->relating_table.'.'.\System\Str::lower($model).'_id')
						->where($this->relating_key, '=', $this->id);
	}

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		// -----------------------------------------------------
		// If the model doesn't have any dirty attributes, there
		// is no need to save it to the database.
		// -----------------------------------------------------
		if ($this->exists and count($this->dirty) == 0)
		{
			return true;
		}

		// -----------------------------------------------------
		// Get the class name of the Eloquent model.
		// -----------------------------------------------------
		$model = get_class($this);

		// -----------------------------------------------------
		// Get a fresh query instance for the model.
		// -----------------------------------------------------
		$this->query = Query::table(static::table($model));

		// -----------------------------------------------------
		// Set the creation and update timestamps.
		// -----------------------------------------------------
		if (property_exists($model, 'timestamps') and $model::$timestamps)
		{
			$this->updated_at = date('Y-m-d H:i:s');

			if ( ! $this->exists)
			{
				$this->created_at = $this->updated_at;
			}
		}

		// -----------------------------------------------------
		// If the model already exists in the database, we only
		// need to update it. Otherwise, we'll insert it.
		// -----------------------------------------------------
		if ($this->exists)
		{
			$result = $this->query->where('id', '=', $this->attributes['id'])->update($this->dirty) == 1;
		}
		else
		{
			$this->attributes['id'] = $this->query->insert_get_id($this->attributes);

			$result = $this->exists = is_numeric($this->id);
		}		

		// -----------------------------------------------------
		// The dirty attributes can be cleared after each save.
		// -----------------------------------------------------
		$this->dirty = array();

		return $result;
	}

	/**
	 * Delete a model from the database.
	 */
	public function delete($id = null)
	{
		// -----------------------------------------------------
		// If the method is being called from an existing model,
		// only delete that model from the database.
		// -----------------------------------------------------
		if ($this->exists)
		{
			return Query::table(static::table(get_class($this)))->delete($this->id) == 1;
		}

		return $this->query->delete($id);
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
		// If the key is a relationship, add it to the ignored.
		// -----------------------------------------------------
		if (method_exists($this, $key))
		{
			$this->ignore[$key] = $value;
		}
		// -----------------------------------------------------
		// Set the attribute and add it to the dirty array.
		// -----------------------------------------------------
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
		// -----------------------------------------------------
		// Retrieve an array of models.
		// -----------------------------------------------------
		if ($method == 'get')
		{
			return $this->_get();
		}

		// -----------------------------------------------------
		// Retrieve the first model result.
		// -----------------------------------------------------
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
		$model = static::make(get_called_class());

		// -----------------------------------------------------
		// Retrieve the entire table of models.
		// -----------------------------------------------------
		if ($method == 'get')
		{
			return $model->_get();
		}

		// -----------------------------------------------------
		// Retrieve the first model result.
		// -----------------------------------------------------
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