<?php namespace System\DB;

use System\Str;
use System\Config;
use System\Inflector;

abstract class Eloquent {

	/**
	 * Indicates if the model exists in the database.
	 *
	 * @var bool
	 */
	public $exists = false;

	/**
	 * The model's attributes. 
	 *
	 * Typically, a model has an attribute for each column on the table.
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
	 * Ignored attributes will not be saved to the database, and are
	 * primarily used to hold relationships.
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
	 * The table name of the model being resolved. 
	 *
	 * This is used during many-to-many eager loading.
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
	 * Create a new Eloquent model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct($attributes = array())
	{
		$this->fill($attributes);
	}

	/**
	 * Set the attributes of the model using an array.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function fill($attributes)
	{
		foreach ($attributes as $key => $value)
		{
			$this->$key = $value;
		}
	}

	/**
	 * Get the table name for a model.
	 *
	 * @param  string  $class
	 * @return string
	 */
	public static function table($class)
	{
		if (property_exists($class, 'table'))
		{
			return $class::$table;
		}

		return strtolower(Inflector::plural($class));
	}

	/**
	 * Factory for creating new Eloquent model instances.
	 *
	 * @param  string  $class
	 * @return object
	 */
	public static function make($class)
	{
		$model = new $class;

		// Since this method is only used for instantiating models for querying
		// purposes, we will go ahead and set the Query instance on the model.
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
		$model = static::make(get_called_class());

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
	 * Get paginated model results.
	 *
	 * @param  int        $per_page
	 * @return Paginator
	 */
	private function _paginate($per_page = null)
	{
		$total = $this->query->count();

		if (is_null($per_page))
		{
			if ( ! property_exists(get_class($this), 'per_page'))
			{
				throw new \Exception("The number of models to display per page for model [".get_class($this)."] has not been specified.");
			}

			$per_page = static::$per_page;
		}

		$current_page = \System\Paginator::page($total, $per_page);

		return new \System\Paginator($this->for_page($current_page, $per_page)->get(), $total, $per_page);
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
	 * The default foreign key for has one and has many relationships is the name
	 * of the model with an appended _id. For example, the foreign key for a
	 * User model would be user_id. Photo would be photo_id, etc.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	private function has_one_or_many($model, $foreign_key)
	{
		$this->relating_key = (is_null($foreign_key)) ? strtolower(get_class($this)).'_id' : $foreign_key;

		return static::make($model)->where($this->relating_key, '=', $this->id);
	}

	/**
	 * Retrieve the query for a 1:1 belonging relationship.
	 *
	 * The default foreign key for belonging relationships is the name of the
	 * relationship method name with _id. So, if a model has a "manager" method
	 * returning a belongs_to relationship, the key would be manager_id.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function belongs_to($model, $foreign_key = null)
	{
		$this->relating = __FUNCTION__;

		if ( ! is_null($foreign_key))
		{
			$this->relating_key = $foreign_key;
		}
		else
		{
			list(, $caller) = debug_backtrace(false);

			$this->relating_key = $caller['function'].'_id';
		}

		return static::make($model)->where('id', '=', $this->attributes[$this->relating_key]);
	}

	/**
	 * Retrieve the query for a *:* relationship.
	 *
	 * By default, the intermediate table name is the plural names of the models
	 * arranged alphabetically and concatenated with an underscore.
	 *
	 * The default foreign key for many-to-many relations is the name of the model
	 * with an appended _id. This is the same convention as has_one and has_many.
	 *
	 * @param  string  $model
	 * @param  string  $table
	 * @param  string  $foreign_key
	 * @param  string  $associated_key
	 * @return mixed
	 */
	public function has_and_belongs_to_many($model, $table = null, $foreign_key = null, $associated_key = null)
	{
		$this->relating = __FUNCTION__;

		if (is_null($table))
		{
			$models = array(Inflector::plural($model), Inflector::plural(get_class($this)));

			sort($models);

			$this->relating_table = strtolower($models[0].'_'.$models[1]);
		}
		else
		{
			$this->relating_table = $table;
		}

		// Allowing the overriding of the foreign and associated keys provides the flexibility for
		// self-referential many-to-many relationships, such as a "buddy list".
		$this->relating_key = (is_null($foreign_key)) ? strtolower(get_class($this)).'_id' : $foreign_key;

		$associated_key = (is_null($associated_key)) ? strtolower($model).'_id' : $associated_key;

		return static::make($model)
                             ->select(array(static::table($model).'.*'))
                             ->join($this->relating_table, static::table($model).'.id', '=', $this->relating_table.'.'.$associated_key)
                             ->where($this->relating_table.'.'.$this->relating_key, '=', $this->id);
	}

	/**
	 * Save the model to the database.
	 *
	 * @return void
	 */
	public function save()
	{
		if ($this->exists and count($this->dirty) == 0)
		{
			return true;
		}

		$model = get_class($this);

		// Since the model was instantiated using "new", a query instance has not been set.
		// Only models being used for querying have their query instances set by default.
		$this->query = Query::table(static::table($model));

		if (property_exists($model, 'timestamps') and $model::$timestamps)
		{
			$this->updated_at = date('Y-m-d H:i:s');

			if ( ! $this->exists)
			{
				$this->created_at = $this->updated_at;
			}
		}

		// If the model already exists in the database, we will just update it.
		// Otherwise, we will insert the model and set the ID attribute.
		if ($this->exists)
		{
			$this->query->where('id', '=', $this->attributes['id'])->update($this->dirty);
		}
		else
		{
			$this->attributes['id'] = $this->query->insert_get_id($this->attributes);
		}

		$this->exists = true;

		$this->dirty = array();
	}

	/**
	 * Delete a model from the database.
	 *
	 * @param  int  $id
	 * @return int
	 */
	public function delete($id = null)
	{
		if ($this->exists)
		{
			return Query::table(static::table(get_class($this)))->delete($this->id);
		}

		return $this->query->delete();
	}

	/**
	 * Magic method for retrieving model attributes.
	 */
	public function __get($key)
	{
		// The ignored attributes hold all of the loaded relationships for the model.
		if (array_key_exists($key, $this->ignore))
		{
			return $this->ignore[$key];
		}

		// If the attribute is a relationship method, return the related models.
		if (method_exists($this, $key))
		{
			$model = $this->$key();

			return $this->ignore[$key] = (in_array($this->relating, array('has_one', 'belongs_to'))) ? $model->first() : $model->get();
		}

		return (array_key_exists($key, $this->attributes)) ? $this->attributes[$key] : null;
	}

	/**
	 * Magic Method for setting model attributes.
	 */
	public function __set($key, $value)
	{
		// If the key is a relationship, add it to the ignored attributes.
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
		unset($this->attributes[$key], $this->ignore[$key], $this->dirty[$key]);
	}

	/**
	 * Magic Method for handling dynamic method calls.
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, array('get', 'first', 'paginate')))
		{
			$method = '_'.$method;

			return $this->$method();
		}

		if (in_array($method, array('count', 'sum', 'min', 'max', 'avg')))
		{
			return call_user_func_array(array($this->query, $method), $parameters);
		}

		// Pass the method to the query instance. This allows the chaining of methods
		// from the query builder, providing a nice, convenient API.
		call_user_func_array(array($this->query, $method), $parameters);

		return $this;
	}

	/**
	 * Magic Method for handling dynamic static method calls.
	 */
	public static function __callStatic($method, $parameters)
	{
		$model = static::make(get_called_class());

		if ($method == 'all')
		{
			return $model->_get();
		}

		if (in_array($method, array('get', 'first', 'paginate')))
		{
			$method = '_'.$method;

			return $model->$method();
		}

		if (in_array($method, array('count', 'sum', 'min', 'max', 'avg')))
		{
			return call_user_func_array(array($model->query, $method), $parameters);
		}

		// Pass the method to the query instance. This allows the chaining of methods
		// from the query builder, providing a nice, convenient API.
		call_user_func_array(array($model->query, $method), $parameters);

		return $model;
	}

}