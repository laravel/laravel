<?php namespace System\DB\Eloquent;

use System\DB;
use System\Str;
use System\Config;
use System\Inflector;
use System\Paginator;

abstract class Model {

	/**
	 * The connection that should be used for the model.
	 *
	 * @var string
	 */
	public static $connection;

	/**
	 * The model query instance.
	 *
	 * @var Query
	 */
	public $query;

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
	 * @return Model
	 */
	public function fill($attributes)
	{
		foreach ($attributes as $key => $value)
		{
			$this->$key = $value;
		}

		return $this;
	}

	/**
	 * Set the eagerly loaded models on the queryable model.
	 *
	 * @return Model
	 */
	private function _with()
	{
		$this->includes = func_get_args();
		return $this;
	}

	/**
	 * Factory for creating queryable Eloquent model instances.
	 *
	 * @param  string  $class
	 * @return object
	 */
	public static function query($class)
	{
		$model = new $class;

		// Since this method is only used for instantiating models for querying
		// purposes, we will go ahead and set the Query instance on the model.
		$model->query = DB::connection(static::$connection)->table(static::table($class));

		return $model;
	}

	/**
	 * Get the table name for a model.
	 *
	 * @param  string  $class
	 * @return string
	 */
	public static function table($class)
	{
		if (property_exists($class, 'table')) return $class::$table;

		return strtolower(Inflector::plural(static::model_name($class)));
	}

	/**
	 * Get an Eloquent model name without any namespaces.
	 *
	 * @param  string|Model  $model
	 * @return string
	 */
	public static function model_name($model)
	{
		$class = (is_object($model)) ? get_class($model) : $model;

		$segments = array_reverse(explode('\\', $class));

		return $segments[0];
	}

	/**
	 * Get all of the models from the database.
	 *
	 * @return array
	 */
	public static function all()
	{
		return Hydrator::hydrate(static::query(get_called_class()));
	}

	/**
	 * Get a model by the primary key.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public static function find($id)
	{
		return static::query(get_called_class())->where('id', '=', $id)->first();
	}

	/**
	 * Get an array of models from the database.
	 *
	 * @return array
	 */
	private function _get()
	{
		return Hydrator::hydrate($this);
	}

	/**
	 * Get the first model result
	 *
	 * @return mixed
	 */
	private function _first()
	{
		return (count($results = Hydrator::hydrate($this->take(1))) > 0) ? reset($results) : null;
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
			$per_page = (property_exists(get_class($this), 'per_page')) ? static::$per_page : 20;
		}

		return Paginator::make($this->for_page(Paginator::page($total, $per_page), $per_page)->get(), $total, $per_page);
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
		$this->relating_key = (is_null($foreign_key)) ? strtolower(static::model_name($this)).'_id' : $foreign_key;

		return static::query($model)->where($this->relating_key, '=', $this->id);
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

		return static::query($model)->where('id', '=', $this->attributes[$this->relating_key]);
	}

	/**
	 * Retrieve the query for a *:* relationship.
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

		$this->relating_table = (is_null($table)) ? $this->intermediate_table($model) : $table;

		// Allowing the overriding of the foreign and associated keys provides the flexibility for
		// self-referential many-to-many relationships, such as a "buddy list".
		$this->relating_key = (is_null($foreign_key)) ? strtolower(static::model_name($this)).'_id' : $foreign_key;

		// The associated key is the foreign key name of the related model. So, if the related model
		// is "Role", the associated key on the intermediate table would be "role_id".
		$associated_key = (is_null($associated_key)) ? strtolower(static::model_name($model)).'_id' : $associated_key;

		return static::query($model)
                             ->select(array(static::table($model).'.*'))
                             ->join($this->relating_table, static::table($model).'.id', '=', $this->relating_table.'.'.$associated_key)
                             ->where($this->relating_table.'.'.$this->relating_key, '=', $this->id);
	}

	/**
	 * Determine the intermediate table name for a given model.
	 *
	 * By default, the intermediate table name is the plural names of the models
	 * arranged alphabetically and concatenated with an underscore.
	 *
	 * @param  string  $model
	 * @return string
	 */
	private function intermediate_table($model)
	{
		$models = array(Inflector::plural(static::model_name($model)), Inflector::plural(static::model_name($this)));

		sort($models);

		return strtolower($models[0].'_'.$models[1]);
	}

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		// If the model does not have any dirty attributes, there is no reason
		// to save it to the database.
		if ($this->exists and count($this->dirty) == 0) return true;

		$model = get_class($this);

		// Since the model was instantiated using "new", a query instance has not been set.
		// Only models being used for querying have their query instances set by default.
		$this->query = DB::connection(static::$connection)->table(static::table($model));

		if (property_exists($model, 'timestamps') and $model::$timestamps)
		{
			$this->timestamp();
		}

		// If the model already exists in the database, we will just update it.
		// Otherwise, we will insert the model and set the ID attribute.
		if ($this->exists)
		{
			$success = ($this->query->where_id($this->attributes['id'])->update($this->dirty) === 1);
		}
		else
		{
			$success = is_numeric($this->attributes['id'] = $this->query->insert_get_id($this->attributes));
		}

		($this->exists = true) and $this->dirty = array();

		return $success;
	}

	/**
	 * Set the creation and update timestamps on the model.
	 *
	 * @return void
	 */
	private function timestamp()
	{
		$this->updated_at = date('Y-m-d H:i:s');

		if ( ! $this->exists) $this->created_at = $this->updated_at;
	}

	/**
	 * Delete a model from the database.
	 *
	 * @param  int  $id
	 * @return int
	 */
	public function delete($id = null)
	{
		// If the delete method is being called on an existing model, we only want to delete
		// that model. If it is being called from an Eloquent query model, it is probably
		// the developer's intention to delete more than one model, so we will pass the
		// delete statement to the query instance.
		if ( ! $this->exists) return $this->query->delete();

		return DB::connection(static::$connection)->table(static::table(get_class($this)))->delete($this->id);
	}

	/**
	 * Magic method for retrieving model attributes.
	 */
	public function __get($key)
	{
		// Is the requested item a model relationship that has already been loaded?
		// All of the loaded relationships are stored in the "ignore" array.
		if (array_key_exists($key, $this->ignore))
		{
			return $this->ignore[$key];
		}
		// Is the requested item a model relationship? If it is, we will dynamically
		// load it and return the results of the relationship query.
		elseif (method_exists($this, $key))
		{
			$query = $this->$key();

			return $this->ignore[$key] = (in_array($this->relating, array('has_one', 'belongs_to'))) ? $query->first() : $query->get();
		}
		elseif (array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}
	}

	/**
	 * Magic Method for setting model attributes.
	 */
	public function __set($key, $value)
	{
		// If the key is a relationship, add it to the ignored attributes.
		// Ignored attributes are not stored in the database.
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
		// To allow the "with", "get", "first", and "paginate" methods to be called both
		// staticly and on an instance, we need to have private, underscored versions
		// of the methods and handle them dynamically.
		if (in_array($method, array('with', 'get', 'first', 'paginate')))
		{
			return call_user_func_array(array($this, '_'.$method), $parameters);
		}

		// All of the aggregate and persistance functions can be passed directly to the query
		// instance. For these functions, we can simply return the response of the query.
		if (in_array($method, array('insert', 'update', 'count', 'sum', 'min', 'max', 'avg')))
		{
			return call_user_func_array(array($this->query, $method), $parameters);
		}

		// Pass the method to the query instance. This allows the chaining of methods
		// from the query builder, providing the same convenient query API as the
		// query builder itself.
		call_user_func_array(array($this->query, $method), $parameters);

		return $this;
	}

	/**
	 * Magic Method for handling dynamic static method calls.
	 */
	public static function __callStatic($method, $parameters)
	{
		// Just pass the method to a model instance and let the __call method take care of it.
		return call_user_func_array(array(static::query(get_called_class()), $method), $parameters);
	}

}