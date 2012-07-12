<?php namespace Laravel\Database\Eloquent;

use Laravel\Str;
use Laravel\Event;
use Laravel\Database;
use Laravel\Database\Eloquent\Relationships\Has_Many_And_Belongs_To;

abstract class Model {

	/**
	 * All of the model's attributes.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * The model's attributes in their original state.
	 *
	 * @var array
	 */
	public $original = array();

	/**
	 * The relationships that have been loaded for the query.
	 *
	 * @var array
	 */
	public $relationships = array();

	/**
	 * Indicates if the model exists in the database.
	 *
	 * @var bool
	 */
	public $exists = false;

	/**
	 * The relationships that should be eagerly loaded.
	 *
	 * @var array
	 */
	public $includes = array();

	/**
	 * The primary key for the model on the database table.
	 *
	 * @var string
	 */
	public static $key = 'id';

	/**
	 * The attributes that are accessible for mass assignment.
	 *
	 * @var array
	 */
	public static $accessible;

	/**
	 * The attributes that should be excluded from to_array.
	 *
	 * @var array
	 */
	public static $hidden = array();

	/**
	 * Indicates if the model has update and creation timestamps.
	 *
	 * @var bool
	 */
	public static $timestamps = true;

	/**
	 * The name of the table associated with the model.
	 *
	 * @var string
	 */
	public static $table;

	/**
	 * The name of the database connection that should be used for the model.
	 *
	 * @var string
	 */
	public static $connection;

	/**
	 * The name of the sequence associated with the model.
	 *
	 * @var string
	 */
	public static $sequence;

	/**
	 * The default number of models to show per page when paginating.
	 *
	 * @var int
	 */
	public static $per_page = 20;

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return void
	 */
	public function __construct($attributes = array(), $exists = false)
	{
		$this->exists = $exists;

		$this->fill($attributes);
	}

	/**
	 * Hydrate the model with an array of attributes.
	 *
	 * @param  array  $attributes
	 * @param  bool   $raw
	 * @return Model
	 */
	public function fill(array $attributes, $raw = false)
	{
		foreach ($attributes as $key => $value)
		{
			// If the "raw" flag is set, it means that we'll just load every value from
			// the array directly into the attributes, without any accessibility or
			// mutators being accounted for. What you pass in is what you get.
			if ($raw)
			{
				$this->set_attribute($key, $value);

				continue;
			}

			// If the "accessible" property is an array, the developer is limiting the
			// attributes that may be mass assigned, and we need to verify that the
			// current attribute is included in that list of allowed attributes.
			if (is_array(static::$accessible))
			{
				if (in_array($key, static::$accessible))
				{
					$this->$key = $value;
				}
			}

			// If the "accessible" property is not an array, no attributes have been
			// white-listed and we are free to set the value of the attribute to
			// the value that has been passed into the method without a check.
			else
			{
				$this->$key = $value;
			}
		}

		// If the original attribute values have not been set, we will set
		// them to the values passed to this method allowing us to easily
		// check if the model has changed since hydration.
		if (count($this->original) === 0)
		{
			$this->original = $this->attributes;
		}

		return $this;
	}

	/**
	 * Fill the model with the contents of the array.
	 *
	 * No mutators or accessibility checks will be accounted for.
	 *
	 * @param  array  $attributes
	 * @return Model
	 */
	public function fill_raw(array $attributes)
	{
		return $this->fill($attributes, true);
	}

	/**
	 * Set the accessible attributes for the given model.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public static function accessible($attributes = null)
	{
		if (is_null($attributes)) return static::$accessible;

		static::$accessible = $attributes;
	}

	/**
	 * Create a new model and store it in the database.
	 *
	 * If save is successful, the model will be returned, otherwise false.
	 *
	 * @param  array        $attributes
	 * @return Model|false
	 */
	public static function create($attributes)
	{
		$model = new static($attributes);

		$success = $model->save();

		return ($success) ? $model : false;
	}

	/**
	 * Update a model instance in the database.
	 *
	 * @param  mixed  $id
	 * @param  array  $attributes
	 * @return int
	 */
	public static function update($id, $attributes)
	{
		$model = new static(array(), true);

		$model->fill($attributes);

		if (static::$timestamps) $model->timestamp();

		return $model->query()->where($model->key(), '=', $id)->update($model->attributes);
	}

	/**
	 * Find a model by its primary key.
	 *
	 * @param  string  $id
	 * @param  array   $columns
	 * @return Model
	 */
	public function _find($id, $columns = array('*'))
	{
		return $this->query()->where(static::$key, '=', $id)->first($columns);
	}

	/**
	 * Get all of the models in the database.
	 *
	 * @return array
	 */
	public static function all()
	{
		return with(new static)->query()->get();
	}

	/**
	 * The relationships that should be eagerly loaded by the query.
	 *
	 * @param  array  $includes
	 * @return Model
	 */
	public function _with($includes)
	{
		$includes = (array) $includes;

		$all_includes = array();

		foreach($includes as $include)
		{
			$nested = explode('.', $include);

			$inc = array();

			foreach($nested as $relation)
			{
				$inc[] = $relation;

				$all_includes[] = implode('.', $inc);
			}

		}

		//remove duplicates and reset the array keys.
		$this->includes = array_values(array_unique($all_includes));

		return $this;
	}

	/**
	 * Get the query for a one-to-one association.
	 *
	 * @param  string        $model
	 * @param  string        $foreign
	 * @return Relationship
	 */
	public function has_one($model, $foreign = null)
	{
		return $this->has_one_or_many(__FUNCTION__, $model, $foreign);
	}

	/**
	 * Get the query for a one-to-many association.
	 *
	 * @param  string        $model
	 * @param  string        $foreign
	 * @return Relationship
	 */
	public function has_many($model, $foreign = null)
	{
		return $this->has_one_or_many(__FUNCTION__, $model, $foreign);
	}

	/**
	 * Get the query for a one-to-one / many association.
	 *
	 * @param  string        $type
	 * @param  string        $model
	 * @param  string        $foreign
	 * @return Relationship
	 */
	protected function has_one_or_many($type, $model, $foreign)
	{
		if ($type == 'has_one')
		{
			return new Relationships\Has_One($this, $model, $foreign);
		}
		else
		{
			return new Relationships\Has_Many($this, $model, $foreign);
		}
	}

	/**
	 * Get the query for a one-to-one (inverse) relationship.
	 *
	 * @param  string        $model
	 * @param  string        $foreign
	 * @return Relationship
	 */
	public function belongs_to($model, $foreign = null)
	{
		// If no foreign key is specified for the relationship, we will assume that the
		// name of the calling function matches the foreign key. For example, if the
		// calling function is "manager", we'll assume the key is "manager_id".
		if (is_null($foreign))
		{
			list(, $caller) = debug_backtrace(false);

			$foreign = "{$caller['function']}_id";
		}

		return new Relationships\Belongs_To($this, $model, $foreign);
	}

	/**
	 * Get the query for a many-to-many relationship.
	 *
	 * @param  string        $model
	 * @param  string        $table
	 * @param  string        $foreign
	 * @param  string        $other
	 * @return Relationship
	 */
	public function has_many_and_belongs_to($model, $table = null, $foreign = null, $other = null)
	{
		return new Has_Many_And_Belongs_To($this, $model, $table, $foreign, $other);
	}

	/**
	 * Save the model and all of its relations to the database.
	 *
	 * @return bool
	 */
	public function push()
	{
		$this->save();

		// To sync all of the relationships to the database, we will simply spin through
		// the relationships, calling the "push" method on each of the models in that
		// given relationship, this should ensure that each model is saved.
		foreach ($this->relationships as $name => $models)
		{
			if ( ! is_array($models))
			{
				$models = array($models);
			}

			foreach ($models as $model)
			{
				$model->push();
			}
		}
	}

	/**
	 * Save the model instance to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		if ( ! $this->dirty()) return true;

		if (static::$timestamps)
		{
			$this->timestamp();
		}

		$this->fire_event('saving');

		// If the model exists, we only need to update it in the database, and the update
		// will be considered successful if there is one affected row returned from the
		// fluent query instance. We'll set the where condition automatically.
		if ($this->exists)
		{
			$query = $this->query()->where(static::$key, '=', $this->get_key());

			$result = $query->update($this->get_dirty()) === 1;

			if ($result) $this->fire_event('updated');
		}

		// If the model does not exist, we will insert the record and retrieve the last
		// insert ID that is associated with the model. If the ID returned is numeric
		// then we can consider the insert successful.
		else
		{
			$id = $this->query()->insert_get_id($this->attributes, $this->key());

			$this->set_key($id);

			$this->exists = $result = is_numeric($this->get_key());

			if ($result) $this->fire_event('created');
		}

		// After the model has been "saved", we will set the original attributes to
		// match the current attributes so the model will not be viewed as being
		// dirty and subsequent calls won't hit the database.
		$this->original = $this->attributes;

		if ($result)
		{
			$this->fire_event('saved');
		}

		return $result;
	}

	/**
	 * Delete the model from the database.
	 *
	 * @return int
	 */
	public function delete()
	{
		if ($this->exists)
		{
			$this->fire_event('deleting');

			$result = $this->query()->where(static::$key, '=', $this->get_key())->delete();

			$this->fire_event('deleted');

			return $result;
		}
	}

	/**
	 * Set the update and creation timestamps on the model.
	 *
	 * @return void
	 */
	protected function timestamp()
	{
		$this->updated_at = new \DateTime;

		if ( ! $this->exists) $this->created_at = $this->updated_at;
	}

	/**
	 * Get a new fluent query builder instance for the model.
	 *
	 * @return Query
	 */
	protected function query()
	{
		return new Query($this);
	}

	/**
	 * Sync the original attributes with the current attributes.
	 *
	 * @return bool
	 */
	final public function sync()
	{
		$this->original = $this->attributes;

		return true;
	}

	/**
	 * Determine if a given attribute has changed from its original state.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	public function changed($attribute)
	{
		return array_get($this->attributes, $attribute) != array_get($this->original, $attribute);
	}

	/**
	 * Determine if the model has been changed from its original state.
	 *
	 * Models that haven't been persisted to storage are always considered dirty.
	 *
	 * @return bool
	 */
	public function dirty()
	{
		return ! $this->exists or count($this->get_dirty()) > 0;
	}

	/**
	 * Get the name of the table associated with the model.
	 *
	 * @return string
	 */
	public function table()
	{
		return static::$table ?: strtolower(Str::plural(class_basename($this)));
	}

	/**
	 * Get the dirty attributes for the model.
	 *
	 * @return array
	 */
	public function get_dirty()
	{
		$dirty = array();

		foreach ($this->attributes as $key => $value)
		{
			if ( ! array_key_exists($key, $this->original) or $value !== $this->original[$key])
			{
				$dirty[$key] = $value;
			}
		}

		return $dirty;
	}

	/**
	 * Get the value of the primary key for the model.
	 *
	 * @return int
	 */
	public function get_key()
	{
		return $this->get_attribute(static::$key);
	}

	/**
	 * Set the value of the primary key for the model.
	 *
	 * @param  int   $value
	 * @return void
	 */
	public function set_key($value)
	{
		return $this->set_attribute(static::$key, $value);
	}

	/**
	 * Get a given attribute from the model.
	 *
	 * @param  string  $key
	 */
	public function get_attribute($key)
	{
		return array_get($this->attributes, $key);
	}

	/**
	 * Set an attribute's value on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set_attribute($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	/**
	 * Remove an attribute from the model.
	 *
	 * @param  string  $key
	 */
	final public function purge($key)
	{
		unset($this->original[$key]);

		unset($this->attributes[$key]);
	}

	/**
	 * Get the model attributes and relationships in array form.
	 *
	 * @return array
	 */
	public function to_array()
	{
		$attributes = array();

		// First we need to gather all of the regular attributes. If the attribute
		// exists in the array of "hidden" attributes, it will not be added to
		// the array so we can easily exclude things like passwords, etc.
		foreach (array_keys($this->attributes) as $attribute)
		{
			if ( ! in_array($attribute, static::$hidden))
			{
				$attributes[$attribute] = $this->$attribute;
			}
		}

		foreach ($this->relationships as $name => $models)
		{
			// If the relationship is not a "to-many" relationship, we can just
			// to_array the related model and add it as an attribute to the
			// array of existing regular attributes we gathered.
			if ($models instanceof Model)
			{
				$attributes[$name] = $models->to_array();
			}

			// If the relationship is a "to-many" relationship we need to spin
			// through each of the related models and add each one with the
			// to_array method, keying them both by name and ID.
			elseif (is_array($models))
			{
				foreach ($models as $id => $model)
				{
					$attributes[$name][$id] = $model->to_array();
				}
			}
			elseif (is_null($models))
			{
				$attributes[$name] = $models;
			}
		}

		return $attributes;
	}

	/**
	 * Fire a given event for the model.
	 *
	 * @param  string  $event
	 * @return array
	 */
	protected function fire_event($event)
	{
		$events = array("eloquent.{$event}", "eloquent.{$event}: ".get_class($this));

		Event::fire($events, array($this));
	}

	/**
	 * Handle the dynamic retrieval of attributes and associations.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		// First we will check to see if the requested key is an already loaded
		// relationship and return it if it is. All relationships are stored
		// in the special relationships array so they are not persisted.
		if (array_key_exists($key, $this->relationships))
		{
			return $this->relationships[$key];
		}

		// Next we'll check if the requested key is in the array of attributes
		// for the model. These are simply regular properties that typically
		// correspond to a single column on the database for the model.
		elseif (array_key_exists($key, $this->attributes))
		{
			return $this->{"get_{$key}"}();
		}

		// If the item is not a loaded relationship, it may be a relationship
		// that hasn't been loaded yet. If it is, we will lazy load it and
		// set the value of the relationship in the relationship array.
		elseif (method_exists($this, $key))
		{
			return $this->relationships[$key] = $this->$key()->results();
		}

		// Finally we will just assume the requested key is just a regular
		// attribute and attempt to call the getter method for it, which
		// will fall into the __call method if one doesn't exist.
		else
		{
			return $this->{"get_{$key}"}();
		}
	}

	/**
	 * Handle the dynamic setting of attributes.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->{"set_{$key}"}($value);
	}

	/**
	 * Determine if an attribute exists on the model.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function __isset($key)
	{
		foreach (array('attributes', 'relationships') as $source)
		{
			if (array_key_exists($key, $this->$source)) return !is_null($this->$source[$key]);
		}
		
		if (method_exists($this, $key)) return true;
	}

	/**
	 * Remove an attribute from the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		foreach (array('attributes', 'relationships') as $source)
		{
			unset($this->$source[$key]);
		}
	}

	/**
	 * Handle dynamic method calls on the model.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$meta = array('key', 'table', 'connection', 'sequence', 'per_page', 'timestamps');

		// If the method is actually the name of a static property on the model, we'll
		// return the value of the static property. This makes it convenient for
		// relationships to access these values off of the instances.
		if (in_array($method, $meta))
		{
			return static::$$method;
		}

		$underscored = array('with', 'find');

		// Some methods need to be accessed both statically and non-statically so we'll
		// keep underscored methods of those methods and intercept calls to them
		// here so they can be called either way on the model instance.
		if (in_array($method, $underscored))
		{
			return call_user_func_array(array($this, '_'.$method), $parameters);
		}

		// First we want to see if the method is a getter / setter for an attribute.
		// If it is, we'll call the basic getter and setter method for the model
		// to perform the appropriate action based on the method.
		if (starts_with($method, 'get_'))
		{
			return $this->get_attribute(substr($method, 4));
		}
		elseif (starts_with($method, 'set_'))
		{
			$this->set_attribute(substr($method, 4), $parameters[0]);
		}

		// Finally we will assume that the method is actually the beginning of a
		// query, such as "where", and will create a new query instance and
		// call the method on the query instance, returning it after.
		else
		{
			return call_user_func_array(array($this->query(), $method), $parameters);
		}
	}

	/**
	 * Dynamically handle static method calls on the model.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$model = get_called_class();

		return call_user_func_array(array(new $model, $method), $parameters);
	}

}