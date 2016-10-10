<?php namespace Illuminate\Database\Eloquent;

use DateTime;
use ArrayAccess;
use Carbon\Carbon;
use LogicException;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

abstract class Model implements ArrayAccess, ArrayableInterface, JsonableInterface {

	/**
	 * The connection name for the model.
	 *
	 * @var string
	 */
	protected $connection;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * The number of models to return for pagination.
	 *
	 * @var int
	 */
	protected $perPage = 15;

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = true;

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The model attribute's original state.
	 *
	 * @var array
	 */
	protected $original = array();

	/**
	 * The loaded relationships for the model.
	 *
	 * @var array
	 */
	protected $relations = array();

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * The attributes that should be visible in arrays.
	 *
	 * @var array
	 */
	protected $visible = array();

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = array();

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array();

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('*');

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = array();

	/**
	 * The relationships that should be touched on save.
	 *
	 * @var array
	 */
	protected $touches = array();

	/**
	 * User exposed observable events
	 *
	 * @var array
	 */
	protected $observables = array();

	/**
	 * The relations to eager load on every query.
	 *
	 * @var array
	 */
	protected $with = array();

	/**
	 * The class name to be used in polymorphic relations.
	 *
	 * @var string
	 */
	protected $morphClass;

	/**
	 * Indicates if the model exists.
	 *
	 * @var bool
	 */
	public $exists = false;

	/**
	 * Indicates if the model should soft delete.
	 *
	 * @var bool
	 */
	protected $softDelete = false;

	/**
	 * Indicates whether attributes are snake cased on arrays.
	 *
	 * @var bool
	 */
	public static $snakeAttributes = true;

	/**
	 * The connection resolver instance.
	 *
	 * @var \Illuminate\Database\ConnectionResolverInterface
	 */
	protected static $resolver;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected static $dispatcher;

	/**
	 * The array of booted models.
	 *
	 * @var array
	 */
	protected static $booted = array();

	/**
	 * Indicates if all mass assignment is enabled.
	 *
	 * @var bool
	 */
	protected static $unguarded = false;

	/**
	 * The cache of the mutated attributes for each class.
	 *
	 * @var array
	 */
	protected static $mutatorCache = array();

	/**
	 * The many to many relationship methods.
	 *
	 * @var array
	 */
	public static $manyMethods = array('belongsToMany', 'morphToMany', 'morphedByMany');

	/**
	 * The name of the "created at" column.
	 *
	 * @var string
	 */
	const CREATED_AT = 'created_at';

	/**
	 * The name of the "updated at" column.
	 *
	 * @var string
	 */
	const UPDATED_AT = 'updated_at';

	/**
	 * The name of the "deleted at" column.
	 *
	 * @var string
	 */
	const DELETED_AT = 'deleted_at';

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct(array $attributes = array())
	{
		$this->bootIfNotBooted();

		$this->syncOriginal();

		$this->fill($attributes);
	}

	/**
	 * Check if the model needs to be booted and if so, do it.
	 *
	 * @return void
	 */
	protected function bootIfNotBooted()
	{
		if ( ! isset(static::$booted[get_class($this)]))
		{
			static::$booted[get_class($this)] = true;

			$this->fireModelEvent('booting', false);

			static::boot();

			$this->fireModelEvent('booted', false);
		}
	}

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		$class = get_called_class();

		static::$mutatorCache[$class] = array();

		// Here we will extract all of the mutated attributes so that we can quickly
		// spin through them after we export models to their array form, which we
		// need to be fast. This will let us always know the attributes mutate.
		foreach (get_class_methods($class) as $method)
		{
			if (preg_match('/^get(.+)Attribute$/', $method, $matches))
			{
				if (static::$snakeAttributes) $matches[1] = snake_case($matches[1]);

				static::$mutatorCache[$class][] = lcfirst($matches[1]);
			}
		}
	}

	/**
	 * Register an observer with the Model.
	 *
	 * @param  object  $class
	 * @return void
	 */
	public static function observe($class)
	{
		$instance = new static;

		$className = get_class($class);

		// When registering a model observer, we will spin through the possible events
		// and determine if this observer has that method. If it does, we will hook
		// it into the model's event system, making it convenient to watch these.
		foreach ($instance->getObservableEvents() as $event)
		{
			if (method_exists($class, $event))
			{
				static::registerModelEvent($event, $className.'@'.$event);
			}
		}
	}

	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model|static
	 *
	 * @throws MassAssignmentException
	 */
	public function fill(array $attributes)
	{
		$totallyGuarded = $this->totallyGuarded();

		foreach ($this->fillableFromArray($attributes) as $key => $value)
		{
			$key = $this->removeTableFromKey($key);

			// The developers may choose to place some attributes in the "fillable"
			// array, which means only those attributes may be set through mass
			// assignment to the model, and all others will just be ignored.
			if ($this->isFillable($key))
			{
				$this->setAttribute($key, $value);
			}
			elseif ($totallyGuarded)
			{
				throw new MassAssignmentException($key);
			}
		}

		return $this;
	}

	/**
	 * Get the fillable attributes of a given array.
	 *
	 * @param  array  $attributes
	 * @return array
	 */
	protected function fillableFromArray(array $attributes)
	{
		if (count($this->fillable) > 0 && ! static::$unguarded)
		{
			return array_intersect_key($attributes, array_flip($this->fillable));
		}

		return $attributes;
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return \Illuminate\Database\Eloquent\Model|static
	 */
	public function newInstance($attributes = array(), $exists = false)
	{
		// This method just provides a convenient way for us to generate fresh model
		// instances of this current model. It is particularly useful during the
		// hydration of new objects via the Eloquent query builder instances.
		$model = new static((array) $attributes);

		$model->exists = $exists;

		return $model;
	}

	/**
	 * Create a new model instance that is existing.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model|static
	 */
	public function newFromBuilder($attributes = array())
	{
		$instance = $this->newInstance(array(), true);

		$instance->setRawAttributes((array) $attributes, true);

		return $instance;
	}

	/**
	 * Create a collection of models from plain arrays.
	 *
	 * @param  array  $items
	 * @param  string  $connection
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function hydrate(array $items, $connection = null)
	{
		$collection = with($instance = new static)->newCollection();

		foreach ($items as $item)
		{
			$model = $instance->newFromBuilder($item);

			if ( ! is_null($connection))
			{
				$model->setConnection($connection);
			}

			$collection->push($model);
		}

		return $collection;
	}

	/**
	 * Create a collection of models from a raw query.
	 *
	 * @param  string  $query
	 * @param  array  $bindings
	 * @param  string  $connection
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function hydrateRaw($query, $bindings = array(), $connection = null)
	{
		$instance = new static;

		if ( ! is_null($connection))
		{
			$instance->setConnection($connection);
		}

		$items = $instance->getConnection()->select($query, $bindings);

		return static::hydrate($items, $connection);
	}

	/**
	 * Save a new model and return the instance.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model|static
	 */
	public static function create(array $attributes)
	{
		$model = new static($attributes);

		$model->save();

		return $model;
	}

	/**
	 * Get the first record matching the attributes or create it.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public static function firstOrCreate(array $attributes)
	{
		if ( ! is_null($instance = static::firstByAttributes($attributes)))
		{
			return $instance;
		}

		return static::create($attributes);
	}

	/**
	 * Get the first record matching the attributes or instantiate it.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public static function firstOrNew(array $attributes)
	{
		if ( ! is_null($instance = static::firstByAttributes($attributes)))
		{
			return $instance;
		}

		return new static($attributes);
	}

	/**
	 * Get the first model for the given attributes.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	protected static function firstByAttributes($attributes)
	{
		$query = static::query();

		foreach ($attributes as $key => $value)
		{
			$query->where($key, $value);
		}

		return $query->first() ?: null;
	}

	/**
	 * Begin querying the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public static function query()
	{
		return with(new static)->newQuery();
	}

	/**
	 * Begin querying the model on a given connection.
	 *
	 * @param  string  $connection
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public static function on($connection = null)
	{
		// First we will just create a fresh instance of this model, and then we can
		// set the connection on the model so that it is be used for the queries
		// we execute, as well as being set on each relationship we retrieve.
		$instance = new static;

		$instance->setConnection($connection);

		return $instance->newQuery();
	}

	/**
	 * Get all of the models from the database.
	 *
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function all($columns = array('*'))
	{
		$instance = new static;

		return $instance->newQuery()->get($columns);
	}

	/**
	 * Find a model by its primary key.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Model|Collection|static
	 */
	public static function find($id, $columns = array('*'))
	{
		if (is_array($id) && empty($id)) return new Collection;

		$instance = new static;

		return $instance->newQuery()->find($id, $columns);
	}

	/**
	 * Find a model by its primary key or return new static.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Model|Collection|static
	 */
	public static function findOrNew($id, $columns = array('*'))
	{
		if ( ! is_null($model = static::find($id, $columns))) return $model;

		return new static($columns);
	}

	/**
	 * Find a model by its primary key or throw an exception.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Model|Collection|static
	 *
	 * @throws ModelNotFoundException
	 */
	public static function findOrFail($id, $columns = array('*'))
	{
		if ( ! is_null($model = static::find($id, $columns))) return $model;

		throw with(new ModelNotFoundException)->setModel(get_called_class());
	}

	/**
	 * Eager load relations on the model.
	 *
	 * @param  array|string  $relations
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function load($relations)
	{
		if (is_string($relations)) $relations = func_get_args();

		$query = $this->newQuery()->with($relations);

		$query->eagerLoadRelations(array($this));

		return $this;
	}

	/**
	 * Being querying a model with eager loading.
	 *
	 * @param  array|string  $relations
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public static function with($relations)
	{
		if (is_string($relations)) $relations = func_get_args();

		$instance = new static;

		return $instance->newQuery()->with($relations);
	}

	/**
	 * Define a one-to-one relationship.
	 *
	 * @param  string  $related
	 * @param  string  $foreignKey
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function hasOne($related, $foreignKey = null, $localKey = null)
	{
		$foreignKey = $foreignKey ?: $this->getForeignKey();

		$instance = new $related;

		$localKey = $localKey ?: $this->getKeyName();

		return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
	}

	/**
	 * Define a polymorphic one-to-one relationship.
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\MorphOne
	 */
	public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
	{
		$instance = new $related;

		list($type, $id) = $this->getMorphs($name, $type, $id);

		$table = $instance->getTable();

		$localKey = $localKey ?: $this->getKeyName();

		return new MorphOne($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
	}

	/**
	 * Define an inverse one-to-one or many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relation
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
	{
		// If no relation name was given, we will use this debug backtrace to extract
		// the calling method's name and use that as the relationship name as most
		// of the time this will be what we desire to use for the relationships.
		if (is_null($relation))
		{
			list(, $caller) = debug_backtrace(false);

			$relation = $caller['function'];
		}

		// If no foreign key was supplied, we can use a backtrace to guess the proper
		// foreign key name by using the name of the relationship function, which
		// when combined with an "_id" should conventionally match the columns.
		if (is_null($foreignKey))
		{
			$foreignKey = snake_case($relation).'_id';
		}

		$instance = new $related;

		// Once we have the foreign key names, we'll just create a new Eloquent query
		// for the related models and returns the relationship instance which will
		// actually be responsible for retrieving and hydrating every relations.
		$query = $instance->newQuery();

		$otherKey = $otherKey ?: $instance->getKeyName();

		return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
	}

	/**
	 * Define a polymorphic, inverse one-to-one or many relationship.
	 *
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function morphTo($name = null, $type = null, $id = null)
	{
		// If no name is provided, we will use the backtrace to get the function name
		// since that is most likely the name of the polymorphic interface. We can
		// use that to get both the class and foreign key that will be utilized.
		if (is_null($name))
		{
			list(, $caller) = debug_backtrace(false);

			$name = snake_case($caller['function']);
		}

		list($type, $id) = $this->getMorphs($name, $type, $id);

		// If the type value is null it is probably safe to assume we're eager loading
		// the relationship. When that is the case we will pass in a dummy query as
		// there are multiple types in the morph and we can't use single queries.
		if (is_null($class = $this->$type))
		{
			return new MorphTo(
				$this->newQuery(), $this, $id, null, $type, $name
			);
		}

		// If we are not eager loading the relationship we will essentially treat this
		// as a belongs-to style relationship since morph-to extends that class and
		// we will pass in the appropriate values so that it behaves as expected.
		else
		{
			$instance = new $class;

			return new MorphTo(
				with($instance)->newQuery(), $this, $id, $instance->getKeyName(), $type, $name
			);
		}
	}

	/**
	 * Define a one-to-many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $foreignKey
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function hasMany($related, $foreignKey = null, $localKey = null)
	{
		$foreignKey = $foreignKey ?: $this->getForeignKey();

		$instance = new $related;

		$localKey = $localKey ?: $this->getKeyName();

		return new HasMany($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
	}

	/**
	 * Define a has-many-through relationship.
	 *
	 * @param  string  $related
	 * @param  string  $through
	 * @param  string|null  $firstKey
	 * @param  string|null  $secondKey
	 * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null)
	{
		$through = new $through;

		$firstKey = $firstKey ?: $this->getForeignKey();

		$secondKey = $secondKey ?: $through->getForeignKey();

		return new HasManyThrough(with(new $related)->newQuery(), $this, $through, $firstKey, $secondKey);
	}

	/**
	 * Define a polymorphic one-to-many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @param  string  $localKey
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
	{
		$instance = new $related;

		// Here we will gather up the morph type and ID for the relationship so that we
		// can properly query the intermediate table of a relation. Finally, we will
		// get the table and create the relationship instances for the developers.
		list($type, $id) = $this->getMorphs($name, $type, $id);

		$table = $instance->getTable();

		$localKey = $localKey ?: $this->getKeyName();

		return new MorphMany($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
	}

	/**
	 * Define a many-to-many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relation
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
	{
		// If no relationship name was passed, we will pull backtraces to get the
		// name of the calling function. We will use that function name as the
		// title of this relation since that is a great convention to apply.
		if (is_null($relation))
		{
			$relation = $this->getBelongsToManyCaller();
		}

		// First, we'll need to determine the foreign key and "other key" for the
		// relationship. Once we have determined the keys we'll make the query
		// instances as well as the relationship instances we need for this.
		$foreignKey = $foreignKey ?: $this->getForeignKey();

		$instance = new $related;

		$otherKey = $otherKey ?: $instance->getForeignKey();

		// If no table name was provided, we can guess it by concatenating the two
		// models using underscores in alphabetical order. The two model names
		// are transformed to snake case from their default CamelCase also.
		if (is_null($table))
		{
			$table = $this->joiningTable($related);
		}

		// Now we're ready to create a new query builder for the related model and
		// the relationship instances for the relation. The relations will set
		// appropriate query constraint and entirely manages the hydrations.
		$query = $instance->newQuery();

		return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
	}

	/**
	 * Define a polymorphic many-to-many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  bool    $inverse
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false)
	{
		$caller = $this->getBelongsToManyCaller();

		// First, we will need to determine the foreign key and "other key" for the
		// relationship. Once we have determined the keys we will make the query
		// instances, as well as the relationship instances we need for these.
		$foreignKey = $foreignKey ?: $name.'_id';

		$instance = new $related;

		$otherKey = $otherKey ?: $instance->getForeignKey();

		// Now we're ready to create a new query builder for this related model and
		// the relationship instances for this relation. This relations will set
		// appropriate query constraints then entirely manages the hydrations.
		$query = $instance->newQuery();

		$table = $table ?: str_plural($name);

		return new MorphToMany(
			$query, $this, $name, $table, $foreignKey,
			$otherKey, $caller, $inverse
		);
	}

	/**
	 * Define a polymorphic, inverse many-to-many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $name
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function morphedByMany($related, $name, $table = null, $foreignKey = null, $otherKey = null)
	{
		$foreignKey = $foreignKey ?: $this->getForeignKey();

		// For the inverse of the polymorphic many-to-many relations, we will change
		// the way we determine the foreign and other keys, as it is the opposite
		// of the morph-to-many method since we're figuring out these inverses.
		$otherKey = $otherKey ?: $name.'_id';

		return $this->morphToMany($related, $name, $table, $foreignKey, $otherKey, true);
	}

	/**
	 * Get the relationship name of the belongs to many.
	 *
	 * @return  string
	 */
	protected function getBelongsToManyCaller()
	{
		$self = __FUNCTION__;

		$caller = array_first(debug_backtrace(false), function($key, $trace) use ($self)
		{
			$caller = $trace['function'];

			return ( ! in_array($caller, Model::$manyMethods) && $caller != $self);
		});

		return ! is_null($caller) ? $caller['function'] : null;
	}

	/**
	 * Get the joining table name for a many-to-many relation.
	 *
	 * @param  string  $related
	 * @return string
	 */
	public function joiningTable($related)
	{
		// The joining table name, by convention, is simply the snake cased models
		// sorted alphabetically and concatenated with an underscore, so we can
		// just sort the models and join them together to get the table name.
		$base = snake_case(class_basename($this));

		$related = snake_case(class_basename($related));

		$models = array($related, $base);

		// Now that we have the model names in an array we can just sort them and
		// use the implode function to join them together with an underscores,
		// which is typically used by convention within the database system.
		sort($models);

		return strtolower(implode('_', $models));
	}

	/**
	 * Destroy the models for the given IDs.
	 *
	 * @param  array|int  $ids
	 * @return int
	 */
	public static function destroy($ids)
	{
		// We'll initialize a count here so we will return the total number of deletes
		// for the operation. The developers can then check this number as a boolean
		// type value or get this total count of records deleted for logging, etc.
		$count = 0;

		$ids = is_array($ids) ? $ids : func_get_args();

		$instance = new static;

		// We will actually pull the models from the database table and call delete on
		// each of them individually so that their events get fired properly with a
		// correct set of attributes in case the developers wants to check these.
		$key = $instance->getKeyName();

		foreach ($instance->whereIn($key, $ids)->get() as $model)
		{
			if ($model->delete()) $count++;
		}

		return $count;
	}

	/**
	 * Delete the model from the database.
	 *
	 * @return bool|null
	 */
	public function delete()
	{
		if (is_null($this->primaryKey))
		{
			throw new \Exception("No primary key defined on model.");
		}

		if ($this->exists)
		{
			if ($this->fireModelEvent('deleting') === false) return false;

			// Here, we'll touch the owning models, verifying these timestamps get updated
			// for the models. This will allow any caching to get broken on the parents
			// by the timestamp. Then we will go ahead and delete the model instance.
			$this->touchOwners();

			$this->performDeleteOnModel();

			$this->exists = false;

			// Once the model has been deleted, we will fire off the deleted event so that
			// the developers may hook into post-delete operations. We will then return
			// a boolean true as the delete is presumably successful on the database.
			$this->fireModelEvent('deleted', false);

			return true;
		}
	}

	/**
	 * Force a hard delete on a soft deleted model.
	 *
	 * @return void
	 */
	public function forceDelete()
	{
		$softDelete = $this->softDelete;

		// We will temporarily disable false delete to allow us to perform the real
		// delete operation against the model. We will then restore the deleting
		// state to what this was prior to this given hard deleting operation.
		$this->softDelete = false;

		$this->delete();

		$this->softDelete = $softDelete;
	}

	/**
	 * Perform the actual delete query on this model instance.
	 *
	 * @return void
	 */
	protected function performDeleteOnModel()
	{
		$query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

		if ($this->softDelete)
		{
			$this->{static::DELETED_AT} = $time = $this->freshTimestamp();

			$query->update(array(static::DELETED_AT => $this->fromDateTime($time)));
		}
		else
		{
			$query->delete();
		}
	}

	/**
	 * Restore a soft-deleted model instance.
	 *
	 * @return bool|null
	 */
	public function restore()
	{
		if ($this->softDelete)
		{
			// If the restoring event does not return false, we will proceed with this
			// restore operation. Otherwise, we bail out so the developer will stop
			// the restore totally. We will clear the deleted timestamp and save.
			if ($this->fireModelEvent('restoring') === false)
			{
				return false;
			}

			$this->{static::DELETED_AT} = null;

			// Once we have saved the model, we will fire the "restored" event so this
			// developer will do anything they need to after a restore operation is
			// totally finished. Then we will return the result of the save call.
			$result = $this->save();

			$this->fireModelEvent('restored', false);

			return $result;
		}
	}

	/**
	 * Register a saving model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function saving($callback)
	{
		static::registerModelEvent('saving', $callback);
	}

	/**
	 * Register a saved model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function saved($callback)
	{
		static::registerModelEvent('saved', $callback);
	}

	/**
	 * Register an updating model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function updating($callback)
	{
		static::registerModelEvent('updating', $callback);
	}

	/**
	 * Register an updated model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function updated($callback)
	{
		static::registerModelEvent('updated', $callback);
	}

	/**
	 * Register a creating model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function creating($callback)
	{
		static::registerModelEvent('creating', $callback);
	}

	/**
	 * Register a created model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function created($callback)
	{
		static::registerModelEvent('created', $callback);
	}

	/**
	 * Register a deleting model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function deleting($callback)
	{
		static::registerModelEvent('deleting', $callback);
	}

	/**
	 * Register a deleted model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function deleted($callback)
	{
		static::registerModelEvent('deleted', $callback);
	}

	/**
	 * Register a restoring model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function restoring($callback)
	{
		static::registerModelEvent('restoring', $callback);
	}

	/**
	 * Register a restored model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public static function restored($callback)
	{
		static::registerModelEvent('restored', $callback);
	}

	/**
	 * Remove all of the event listeners for the model.
	 *
	 * @return void
	 */
	public static function flushEventListeners()
	{
		if ( ! isset(static::$dispatcher)) return;

		$instance = new static;

		foreach ($instance->getObservableEvents() as $event)
		{
			static::$dispatcher->forget("eloquent.{$event}: ".get_called_class());
		}
	}

	/**
	 * Register a model event with the dispatcher.
	 *
	 * @param  string  $event
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	protected static function registerModelEvent($event, $callback)
	{
		if (isset(static::$dispatcher))
		{
			$name = get_called_class();

			static::$dispatcher->listen("eloquent.{$event}: {$name}", $callback);
		}
	}

	/**
	 * Get the observable event names.
	 *
	 * @return array
	 */
	public function getObservableEvents()
	{
		return array_merge(
			array(
				'creating', 'created', 'updating', 'updated',
				'deleting', 'deleted', 'saving', 'saved',
				'restoring', 'restored',
			),
			$this->observables
		);
	}

	/**
	 * Increment a column's value by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @return int
	 */
	protected function increment($column, $amount = 1)
	{
		return $this->incrementOrDecrement($column, $amount, 'increment');
	}

	/**
	 * Decrement a column's value by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @return int
	 */
	protected function decrement($column, $amount = 1)
	{
		return $this->incrementOrDecrement($column, $amount, 'decrement');
	}

	/**
	 * Run the increment or decrement method on the model.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @param  string  $method
	 * @return int
	 */
	protected function incrementOrDecrement($column, $amount, $method)
	{
		$query = $this->newQuery();

		if ( ! $this->exists)
		{
			return $query->{$method}($column, $amount);
		}

		return $query->where($this->getKeyName(), $this->getKey())->{$method}($column, $amount);
	}

	/**
	 * Update the model in the database.
	 *
	 * @param  array  $attributes
	 * @return mixed
	 */
	public function update(array $attributes = array())
	{
		if ( ! $this->exists)
		{
			return $this->newQuery()->update($attributes);
		}

		return $this->fill($attributes)->save();
	}

	/**
	 * Save the model and all of its relationships.
	 *
	 * @return bool
	 */
	public function push()
	{
		if ( ! $this->save()) return false;

		// To sync all of the relationships to the database, we will simply spin through
		// the relationships and save each model via this "push" method, which allows
		// us to recurs into all of these nested relations for this model instance.
		foreach ($this->relations as $models)
		{
			foreach (Collection::make($models) as $model)
			{
				if ( ! $model->push()) return false;
			}
		}

		return true;
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		$query = $this->newQueryWithDeleted();

		// If the "saving" event returns false we'll bail out of the save and return
		// false, indicating that the save failed. This gives an opportunities to
		// listeners to cancel save operations if validations fail or whatever.
		if ($this->fireModelEvent('saving') === false)
		{
			return false;
		}

		// If the model already exists in the database we can just update our record
		// that is already in this database using the current IDs in this "where"
		// clause to only update this model. Otherwise, we'll just insert them.
		if ($this->exists)
		{
			$saved = $this->performUpdate($query);
		}

		// If the model is brand new, we'll insert it into our database and set the
		// ID attribute on the model to the value of the newly inserted row's ID
		// which is typically an auto-increment value managed by the database.
		else
		{
			$saved = $this->performInsert($query);
		}

		if ($saved) $this->finishSave($options);

		return $saved;
	}

	/**
	 * Finish processing on a successful save operation.
	 *
	 * @param  array  $options
	 * @return void
	 */
	protected function finishSave(array $options)
	{
		$this->syncOriginal();

		$this->fireModelEvent('saved', false);

		if (array_get($options, 'touch', true)) $this->touchOwners();
	}

	/**
	 * Perform a model update operation.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return bool|null
	 */
	protected function performUpdate(Builder $query)
	{
		$dirty = $this->getDirty();

		if (count($dirty) > 0)
		{
			// If the updating event returns false, we will cancel the update operation so
			// developers can hook Validation systems into their models and cancel this
			// operation if the model does not pass validation. Otherwise, we update.
			if ($this->fireModelEvent('updating') === false)
			{
				return false;
			}

			// First we need to create a fresh query instance and touch the creation and
			// update timestamp on the model which are maintained by us for developer
			// convenience. Then we will just continue saving the model instances.
			if ($this->timestamps)
			{
				$this->updateTimestamps();
			}

			// Once we have run the update operation, we will fire the "updated" event for
			// this model instance. This will allow developers to hook into these after
			// models are updated, giving them a chance to do any special processing.
			$dirty = $this->getDirty();

			if (count($dirty) > 0)
			{
				$this->setKeysForSaveQuery($query)->update($dirty);

				$this->fireModelEvent('updated', false);
			}
		}

		return true;
	}

	/**
	 * Perform a model insert operation.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return bool
	 */
	protected function performInsert(Builder $query)
	{
		if ($this->fireModelEvent('creating') === false) return false;

		// First we'll need to create a fresh query instance and touch the creation and
		// update timestamps on this model, which are maintained by us for developer
		// convenience. After, we will just continue saving these model instances.
		if ($this->timestamps)
		{
			$this->updateTimestamps();
		}

		// If the model has an incrementing key, we can use the "insertGetId" method on
		// the query builder, which will give us back the final inserted ID for this
		// table from the database. Not all tables have to be incrementing though.
		$attributes = $this->attributes;

		if ($this->incrementing)
		{
			$this->insertAndSetId($query, $attributes);
		}

		// If the table is not incrementing we'll simply insert this attributes as they
		// are, as this attributes arrays must contain an "id" column already placed
		// there by the developer as the manually determined key for these models.
		else
		{
			$query->insert($attributes);
		}

		// We will go ahead and set the exists property to true, so that it is set when
		// the created event is fired, just in case the developer tries to update it
		// during the event. This will allow them to do so and run an update here.
		$this->exists = true;

		$this->fireModelEvent('created', false);

		return true;
	}

	/**
	 * Insert the given attributes and set the ID on the model.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  array  $attributes
	 * @return void
	 */
	protected function insertAndSetId(Builder $query, $attributes)
	{
		$id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

		$this->setAttribute($keyName, $id);
	}

	/**
	 * Touch the owning relations of the model.
	 *
	 * @return void
	 */
	public function touchOwners()
	{
		foreach ($this->touches as $relation)
		{
			$this->$relation()->touch();
		}
	}

	/**
	 * Determine if the model touches a given relation.
	 *
	 * @param  string  $relation
	 * @return bool
	 */
	public function touches($relation)
	{
		return in_array($relation, $this->touches);
	}

	/**
	 * Fire the given event for the model.
	 *
	 * @param  string  $event
	 * @param  bool    $halt
	 * @return mixed
	 */
	protected function fireModelEvent($event, $halt = true)
	{
		if ( ! isset(static::$dispatcher)) return true;

		// We will append the names of the class to the event to distinguish it from
		// other model events that are fired, allowing us to listen on each model
		// event set individually instead of catching event for all the models.
		$event = "eloquent.{$event}: ".get_class($this);

		$method = $halt ? 'until' : 'fire';

		return static::$dispatcher->$method($event, $this);
	}

	/**
	 * Set the keys for a save update query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected function setKeysForSaveQuery(Builder $query)
	{
		$query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());

		return $query;
	}

	/**
	 * Get the primary key value for a save query.
	 *
	 * @return mixed
	 */
	protected function getKeyForSaveQuery()
	{
		if (isset($this->original[$this->getKeyName()]))
		{
			return $this->original[$this->getKeyName()];
		}
		else
		{
			return $this->getAttribute($this->getKeyName());
		}
	}

	/**
	 * Update the model's update timestamp.
	 *
	 * @return bool
	 */
	public function touch()
	{
		$this->updateTimestamps();

		return $this->save();
	}

	/**
	 * Update the creation and update timestamps.
	 *
	 * @return void
	 */
	protected function updateTimestamps()
	{
		$time = $this->freshTimestamp();

		if ( ! $this->isDirty(static::UPDATED_AT))
		{
			$this->setUpdatedAt($time);
		}

		if ( ! $this->exists && ! $this->isDirty(static::CREATED_AT))
		{
			$this->setCreatedAt($time);
		}
	}

	/**
	 * Set the value of the "created at" attribute.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function setCreatedAt($value)
	{
		$this->{static::CREATED_AT} = $value;
	}

	/**
	 * Set the value of the "updated at" attribute.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function setUpdatedAt($value)
	{
		$this->{static::UPDATED_AT} = $value;
	}

	/**
	 * Get the name of the "created at" column.
	 *
	 * @return string
	 */
	public function getCreatedAtColumn()
	{
		return static::CREATED_AT;
	}

	/**
	 * Get the name of the "updated at" column.
	 *
	 * @return string
	 */
	public function getUpdatedAtColumn()
	{
		return static::UPDATED_AT;
	}

	/**
	 * Get the name of the "deleted at" column.
	 *
	 * @return string
	 */
	public function getDeletedAtColumn()
	{
		return static::DELETED_AT;
	}

	/**
	 * Get the fully qualified "deleted at" column.
	 *
	 * @return string
	 */
	public function getQualifiedDeletedAtColumn()
	{
		return $this->getTable().'.'.$this->getDeletedAtColumn();
	}

	/**
	 * Get a fresh timestamp for the model.
	 *
	 * @return \Carbon\Carbon
	 */
	public function freshTimestamp()
	{
		return new Carbon;
	}

	/**
	 * Get a fresh timestamp for the model.
	 *
	 * @return string
	 */
	public function freshTimestampString()
	{
		return $this->fromDateTime($this->freshTimestamp());
	}

	/**
	 * Get a new query builder for the model's table.
	 *
	 * @param  bool  $excludeDeleted
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newQuery($excludeDeleted = true)
	{
		$builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

		// Once we have the query builders, we will set the model instances so the
		// builder can easily access any information it may need from the model
		// while it is constructing and executing various queries against it.
		$builder->setModel($this)->with($this->with);

		if ($excludeDeleted && $this->softDelete)
		{
			$builder->whereNull($this->getQualifiedDeletedAtColumn());
		}

		return $builder;
	}

	/**
	 * Get a new query builder that includes soft deletes.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newQueryWithDeleted()
	{
		return $this->newQuery(false);
	}

	/**
	 * Create a new Eloquent query builder for the model.
	 *
	 * @param  \Illuminate\Database\Query\Builder $query
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newEloquentBuilder($query)
	{
		return new Builder($query);
	}

	/**
	 * Determine if the model instance has been soft-deleted.
	 *
	 * @return bool
	 */
	public function trashed()
	{
		return $this->softDelete && ! is_null($this->{static::DELETED_AT});
	}

	/**
	 * Get a new query builder that includes soft deletes.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public static function withTrashed()
	{
		return with(new static)->newQueryWithDeleted();
	}

	/**
	 * Get a new query builder that only includes soft deletes.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public static function onlyTrashed()
	{
		$instance = new static;

		$column = $instance->getQualifiedDeletedAtColumn();

		return $instance->newQueryWithDeleted()->whereNotNull($column);
	}

	/**
	 * Get a new query builder instance for the connection.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function newBaseQueryBuilder()
	{
		$conn = $this->getConnection();

		$grammar = $conn->getQueryGrammar();

		return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
	}

	/**
	 * Create a new Eloquent Collection instance.
	 *
	 * @param  array  $models
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}

	/**
	 * Create a new pivot model instance.
	 *
	 * @param  \Illuminate\Database\Eloquent\Model  $parent
	 * @param  array   $attributes
	 * @param  string  $table
	 * @param  bool    $exists
	 * @return \Illuminate\Database\Eloquent\Relations\Pivot
	 */
	public function newPivot(Model $parent, array $attributes, $table, $exists)
	{
		return new Pivot($parent, $attributes, $table, $exists);
	}

	/**
	 * Get the table associated with the model.
	 *
	 * @return string
	 */
	public function getTable()
	{
		if (isset($this->table)) return $this->table;

		return str_replace('\\', '', snake_case(str_plural(class_basename($this))));
	}

	/**
	 * Set the table associated with the model.
	 *
	 * @param  string  $table
	 * @return void
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}

	/**
	 * Get the value of the model's primary key.
	 *
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->getAttribute($this->getKeyName());
	}

	/**
	 * Get the primary key for the model.
	 *
	 * @return string
	 */
	public function getKeyName()
	{
		return $this->primaryKey;
	}

	/**
	 * Get the table qualified key name.
	 *
	 * @return string
	 */
	public function getQualifiedKeyName()
	{
		return $this->getTable().'.'.$this->getKeyName();
	}

	/**
	 * Determine if the model uses timestamps.
	 *
	 * @return bool
	 */
	public function usesTimestamps()
	{
		return $this->timestamps;
	}

	/**
	 * Determine if the model instance uses soft deletes.
	 *
	 * @return bool
	 */
	public function isSoftDeleting()
	{
		return $this->softDelete;
	}

	/**
	 * Set the soft deleting property on the model.
	 *
	 * @param  bool  $enabled
	 * @return void
	 */
	public function setSoftDeleting($enabled)
	{
		$this->softDelete = $enabled;
	}

	/**
	 * Get the polymorphic relationship columns.
	 *
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $id
	 * @return array
	 */
	protected function getMorphs($name, $type, $id)
	{
		$type = $type ?: $name.'_type';

		$id = $id ?: $name.'_id';

		return array($type, $id);
	}

	/**
	 * Get the class name for polymorphic relations.
	 *
	 * @return string
	 */
	public function getMorphClass()
	{
		return $this->morphClass ?: get_class($this);
	}

	/**
	 * Get the number of models to return per page.
	 *
	 * @return int
	 */
	public function getPerPage()
	{
		return $this->perPage;
	}

	/**
	 * Set the number of models to return per page.
	 *
	 * @param  int   $perPage
	 * @return void
	 */
	public function setPerPage($perPage)
	{
		$this->perPage = $perPage;
	}

	/**
	 * Get the default foreign key name for the model.
	 *
	 * @return string
	 */
	public function getForeignKey()
	{
		return snake_case(class_basename($this)).'_id';
	}

	/**
	 * Get the hidden attributes for the model.
	 *
	 * @return array
	 */
	public function getHidden()
	{
		return $this->hidden;
	}

	/**
	 * Set the hidden attributes for the model.
	 *
	 * @param  array  $hidden
	 * @return void
	 */
	public function setHidden(array $hidden)
	{
		$this->hidden = $hidden;
	}

	/**
	 * Set the visible attributes for the model.
	 *
	 * @param  array  $visible
	 * @return void
	 */
	public function setVisible(array $visible)
	{
		$this->visible = $visible;
	}

	/**
	 * Set the accessors to append to model arrays.
	 *
	 * @param  array  $appends
	 * @return void
	 */
	public function setAppends(array $appends)
	{
		$this->appends = $appends;
	}

	/**
	 * Get the fillable attributes for the model.
	 *
	 * @return array
	 */
	public function getFillable()
	{
		return $this->fillable;
	}

	/**
	 * Set the fillable attributes for the model.
	 *
	 * @param  array  $fillable
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function fillable(array $fillable)
	{
		$this->fillable = $fillable;

		return $this;
	}

	/**
	 * Set the guarded attributes for the model.
	 *
	 * @param  array  $guarded
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function guard(array $guarded)
	{
		$this->guarded = $guarded;

		return $this;
	}

	/**
	 * Disable all mass assignable restrictions.
	 *
	 * @return void
	 */
	public static function unguard()
	{
		static::$unguarded = true;
	}

	/**
	 * Enable the mass assignment restrictions.
	 *
	 * @return void
	 */
	public static function reguard()
	{
		static::$unguarded = false;
	}

	/**
	 * Set "unguard" to a given state.
	 *
	 * @param  bool  $state
	 * @return void
	 */
	public static function setUnguardState($state)
	{
		static::$unguarded = $state;
	}

	/**
	 * Determine if the given attribute may be mass assigned.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function isFillable($key)
	{
		if (static::$unguarded) return true;

		// If the key is in the "fillable" array, we can of course assume that it's
		// a fillable attribute. Otherwise, we will check the guarded array when
		// we need to determine if the attribute is black-listed on the model.
		if (in_array($key, $this->fillable)) return true;

		if ($this->isGuarded($key)) return false;

		return empty($this->fillable) && ! starts_with($key, '_');
	}

	/**
	 * Determine if the given key is guarded.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function isGuarded($key)
	{
		return in_array($key, $this->guarded) || $this->guarded == array('*');
	}

	/**
	 * Determine if the model is totally guarded.
	 *
	 * @return bool
	 */
	public function totallyGuarded()
	{
		return count($this->fillable) == 0 && $this->guarded == array('*');
	}

	/**
	 * Remove the table name from a given key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected function removeTableFromKey($key)
	{
		if ( ! str_contains($key, '.')) return $key;

		return last(explode('.', $key));
	}

	/**
	 * Get the relationships that are touched on save.
	 *
	 * @return array
	 */
	public function getTouchedRelations()
	{
		return $this->touches;
	}

	/**
	 * Set the relationships that are touched on save.
	 *
	 * @param  array  $touches
	 * @return void
	 */
	public function setTouchedRelations(array $touches)
	{
		$this->touches = $touches;
	}

	/**
	 * Get the value indicating whether the IDs are incrementing.
	 *
	 * @return bool
	 */
	public function getIncrementing()
	{
		return $this->incrementing;
	}

	/**
	 * Set whether IDs are incrementing.
	 *
	 * @param  bool  $value
	 * @return void
	 */
	public function setIncrementing($value)
	{
		$this->incrementing = $value;
	}

	/**
	 * Convert the model instance to JSON.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$attributes = $this->attributesToArray();

		return array_merge($attributes, $this->relationsToArray());
	}

	/**
	 * Convert the model's attributes to an array.
	 *
	 * @return array
	 */
	public function attributesToArray()
	{
		$attributes = $this->getArrayableAttributes();

		// If an attribute is a date, we will cast it to a string after converting it
		// to a DateTime / Carbon instance. This is so we will get some consistent
		// formatting while accessing attributes vs. arraying / JSONing a model.
		foreach ($this->getDates() as $key)
		{
			if ( ! isset($attributes[$key])) continue;

			$attributes[$key] = (string) $this->asDateTime($attributes[$key]);
		}

		// We want to spin through all the mutated attributes for this model and call
		// the mutator for the attribute. We cache off every mutated attributes so
		// we don't have to constantly check on attributes that actually change.
		foreach ($this->getMutatedAttributes() as $key)
		{
			if ( ! array_key_exists($key, $attributes)) continue;

			$attributes[$key] = $this->mutateAttributeForArray(
				$key, $attributes[$key]
			);
		}

		// Here we will grab all of the appended, calculated attributes to this model
		// as these attributes are not really in the attributes array, but are run
		// when we need to array or JSON the model for convenience to the coder.
		foreach ($this->appends as $key)
		{
			$attributes[$key] = $this->mutateAttributeForArray($key, null);
		}

		return $attributes;
	}

	/**
	 * Get an attribute array of all arrayable attributes.
	 *
	 * @return array
	 */
	protected function getArrayableAttributes()
	{
		return $this->getArrayableItems($this->attributes);
	}

	/**
	 * Get the model's relationships in array form.
	 *
	 * @return array
	 */
	public function relationsToArray()
	{
		$attributes = array();

		foreach ($this->getArrayableRelations() as $key => $value)
		{
			if (in_array($key, $this->hidden)) continue;

			// If the values implements the Arrayable interface we can just call this
			// toArray method on the instances which will convert both models and
			// collections to their proper array form and we'll set the values.
			if ($value instanceof ArrayableInterface)
			{
				$relation = $value->toArray();
			}

			// If the value is null, we'll still go ahead and set it in this list of
			// attributes since null is used to represent empty relationships if
			// if it a has one or belongs to type relationships on the models.
			elseif (is_null($value))
			{
				$relation = $value;
			}

			// If the relationships snake-casing is enabled, we will snake case this
			// key so that the relation attribute is snake cased in this returned
			// array to the developers, making this consistent with attributes.
			if (static::$snakeAttributes)
			{
				$key = snake_case($key);
			}

			// If the relation value has been set, we will set it on this attributes
			// list for returning. If it was not arrayable or null, we'll not set
			// the value on the array because it is some type of invalid value.
			if (isset($relation) || is_null($value))
			{
				$attributes[$key] = $relation;
			}
		}

		return $attributes;
	}

	/**
	 * Get an attribute array of all arrayable relations.
	 *
	 * @return array
	 */
	protected function getArrayableRelations()
	{
		return $this->getArrayableItems($this->relations);
	}

	/**
	 * Get an attribute array of all arrayable values.
	 *
	 * @param  array  $values
	 * @return array
	 */
	protected function getArrayableItems(array $values)
	{
		if (count($this->visible) > 0)
		{
			return array_intersect_key($values, array_flip($this->visible));
		}

		return array_diff_key($values, array_flip($this->hidden));
	}

	/**
	 * Get an attribute from the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		$inAttributes = array_key_exists($key, $this->attributes);

		// If the key references an attribute, we can just go ahead and return the
		// plain attribute value from the model. This allows every attribute to
		// be dynamically accessed through the _get method without accessors.
		if ($inAttributes || $this->hasGetMutator($key))
		{
			return $this->getAttributeValue($key);
		}

		// If the key already exists in the relationships array, it just means the
		// relationship has already been loaded, so we'll just return it out of
		// here because there is no need to query within the relations twice.
		if (array_key_exists($key, $this->relations))
		{
			return $this->relations[$key];
		}

		// If the "attribute" exists as a method on the model, we will just assume
		// it is a relationship and will load and return results from the query
		// and hydrate the relationship's value on the "relationships" array.
		$camelKey = camel_case($key);

		if (method_exists($this, $camelKey))
		{
			return $this->getRelationshipFromMethod($key, $camelKey);
		}
	}

	/**
	 * Get a plain attribute (not a relationship).
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function getAttributeValue($key)
	{
		$value = $this->getAttributeFromArray($key);

		// If the attribute has a get mutator, we will call that then return what
		// it returns as the value, which is useful for transforming values on
		// retrieval from the model to a form that is more useful for usage.
		if ($this->hasGetMutator($key))
		{
			return $this->mutateAttribute($key, $value);
		}

		// If the attribute is listed as a date, we will convert it to a DateTime
		// instance on retrieval, which makes it quite convenient to work with
		// date fields without having to create a mutator for each property.
		elseif (in_array($key, $this->getDates()))
		{
			if ($value) return $this->asDateTime($value);
		}

		return $value;
	}

	/**
	 * Get an attribute from the $attributes array.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function getAttributeFromArray($key)
	{
		if (array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}
	}

	/**
	 * Get a relationship value from a method.
	 *
	 * @param  string  $key
	 * @param  string  $camelKey
	 * @return mixed
	 *
	 * @throws \LogicException
	 */
	protected function getRelationshipFromMethod($key, $camelKey)
	{
		$relations = $this->$camelKey();

		if ( ! $relations instanceof Relation)
		{
			throw new LogicException('Relationship method must return an object of type '
				. 'Illuminate\Database\Eloquent\Relations\Relation');
		}

		return $this->relations[$key] = $relations->getResults();
	}

	/**
	 * Determine if a get mutator exists for an attribute.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasGetMutator($key)
	{
		return method_exists($this, 'get'.studly_case($key).'Attribute');
	}

	/**
	 * Get the value of an attribute using its mutator.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function mutateAttribute($key, $value)
	{
		return $this->{'get'.studly_case($key).'Attribute'}($value);
	}

	/**
	 * Get the value of an attribute using its mutator for array conversion.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function mutateAttributeForArray($key, $value)
	{
		$value = $this->mutateAttribute($key, $value);

		return $value instanceof ArrayableInterface ? $value->toArray() : $value;
	}

	/**
	 * Set a given attribute on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function setAttribute($key, $value)
	{
		// First we will check for the presence of a mutator for the set operation
		// which simply lets the developers tweak the attribute as it is set on
		// the model, such as "json_encoding" an listing of data for storage.
		if ($this->hasSetMutator($key))
		{
			$method = 'set'.studly_case($key).'Attribute';

			return $this->{$method}($value);
		}

		// If an attribute is listed as a "date", we'll convert it from a DateTime
		// instance into a form proper for storage on the database tables using
		// the connection grammar's date format. We will auto set the values.
		elseif (in_array($key, $this->getDates()))
		{
			if ($value)
			{
				$value = $this->fromDateTime($value);
			}
		}

		$this->attributes[$key] = $value;
	}

	/**
	 * Determine if a set mutator exists for an attribute.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasSetMutator($key)
	{
		return method_exists($this, 'set'.studly_case($key).'Attribute');
	}

	/**
	 * Get the attributes that should be converted to dates.
	 *
	 * @return array
	 */
	public function getDates()
	{
		$defaults = array(static::CREATED_AT, static::UPDATED_AT, static::DELETED_AT);

		return array_merge($this->dates, $defaults);
	}

	/**
	 * Convert a DateTime to a storable string.
	 *
	 * @param  \DateTime|int  $value
	 * @return string
	 */
	public function fromDateTime($value)
	{
		$format = $this->getDateFormat();

		// If the value is already a DateTime instance, we will just skip the rest of
		// these checks since they will be a waste of time, and hinder performance
		// when checking the field. We will just return the DateTime right away.
		if ($value instanceof DateTime)
		{
			//
		}

		// If the value is totally numeric, we will assume it is a UNIX timestamp and
		// format the date as such. Once we have the date in DateTime form we will
		// format it according to the proper format for the database connection.
		elseif (is_numeric($value))
		{
			$value = Carbon::createFromTimestamp($value);
		}

		// If the value is in simple year, month, day format, we will format it using
		// that setup. This is for simple "date" fields which do not have hours on
		// the field. This conveniently picks up those dates and format correct.
		elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value))
		{
			$value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
		}

		// If this value is some other type of string, we'll create the DateTime with
		// the format used by the database connection. Once we get the instance we
		// can return back the finally formatted DateTime instances to the devs.
		elseif ( ! $value instanceof DateTime)
		{
			$value = Carbon::createFromFormat($format, $value);
		}

		return $value->format($format);
	}

	/**
	 * Return a timestamp as DateTime object.
	 *
	 * @param  mixed  $value
	 * @return \Carbon\Carbon
	 */
	protected function asDateTime($value)
	{
		// If this value is an integer, we will assume it is a UNIX timestamp's value
		// and format a Carbon object from this timestamp. This allows flexibility
		// when defining your date fields as they might be UNIX timestamps here.
		if (is_numeric($value))
		{
			return Carbon::createFromTimestamp($value);
		}

		// If the value is in simply year, month, day format, we will instantiate the
		// Carbon instances from that format. Again, this provides for simple date
		// fields on the database, while still supporting Carbonized conversion.
		elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value))
		{
			return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
		}

		// Finally, we will just assume this date is in the format used by default on
		// the database connection and use that format to create the Carbon object
		// that is returned back out to the developers after we convert it here.
		elseif ( ! $value instanceof DateTime)
		{
			$format = $this->getDateFormat();

			return Carbon::createFromFormat($format, $value);
		}

		return Carbon::instance($value);
	}

	/**
	 * Get the format for database stored dates.
	 *
	 * @return string
	 */
	protected function getDateFormat()
	{
		return $this->getConnection()->getQueryGrammar()->getDateFormat();
	}

	/**
	 * Clone the model into a new, non-existing instance.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function replicate()
	{
		$attributes = array_except($this->attributes, array($this->getKeyName()));

		with($instance = new static)->setRawAttributes($attributes);

		return $instance->setRelations($this->relations);
	}

	/**
	 * Get all of the current attributes on the model.
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Set the array of model attributes. No checking is done.
	 *
	 * @param  array  $attributes
	 * @param  bool   $sync
	 * @return void
	 */
	public function setRawAttributes(array $attributes, $sync = false)
	{
		$this->attributes = $attributes;

		if ($sync) $this->syncOriginal();
	}

	/**
	 * Get the model's original attribute values.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public function getOriginal($key = null, $default = null)
	{
		return array_get($this->original, $key, $default);
	}

	/**
	 * Sync the original attributes with the current.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function syncOriginal()
	{
		$this->original = $this->attributes;

		return $this;
	}

	/**
	 * Determine if a given attribute is dirty.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	public function isDirty($attribute)
	{
		return array_key_exists($attribute, $this->getDirty());
	}

	/**
	 * Get the attributes that have been changed since last sync.
	 *
	 * @return array
	 */
	public function getDirty()
	{
		$dirty = array();

		foreach ($this->attributes as $key => $value)
		{
			if ( ! array_key_exists($key, $this->original))
			{
				$dirty[$key] = $value;
			}
			elseif ($value !== $this->original[$key] &&
                                 ! $this->originalIsNumericallyEquivalent($key))
			{
				$dirty[$key] = $value;
			}
		}

		return $dirty;
	}

	/**
	 * Determine if the new and old values for a given key are numerically equivalent.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function originalIsNumericallyEquivalent($key)
	{
		$current = $this->attributes[$key];

		$original = $this->original[$key];

		return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
	}

	/**
	 * Get all the loaded relations for the instance.
	 *
	 * @return array
	 */
	public function getRelations()
	{
		return $this->relations;
	}

	/**
	 * Get a specified relationship.
	 *
	 * @param  string  $relation
	 * @return mixed
	 */
	public function getRelation($relation)
	{
		return $this->relations[$relation];
	}

	/**
	 * Set the specific relationship in the model.
	 *
	 * @param  string  $relation
	 * @param  mixed   $value
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function setRelation($relation, $value)
	{
		$this->relations[$relation] = $value;

		return $this;
	}

	/**
	 * Set the entire relations array on the model.
	 *
	 * @param  array  $relations
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function setRelations(array $relations)
	{
		$this->relations = $relations;

		return $this;
	}

	/**
	 * Get the database connection for the model.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return static::resolveConnection($this->connection);
	}

	/**
	 * Get the current connection name for the model.
	 *
	 * @return string
	 */
	public function getConnectionName()
	{
		return $this->connection;
	}

	/**
	 * Set the connection associated with the model.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function setConnection($name)
	{
		$this->connection = $name;

		return $this;
	}

	/**
	 * Resolve a connection instance.
	 *
	 * @param  string  $connection
	 * @return \Illuminate\Database\Connection
	 */
	public static function resolveConnection($connection = null)
	{
		return static::$resolver->connection($connection);
	}

	/**
	 * Get the connection resolver instance.
	 *
	 * @return \Illuminate\Database\ConnectionResolverInterface
	 */
	public static function getConnectionResolver()
	{
		return static::$resolver;
	}

	/**
	 * Set the connection resolver instance.
	 *
	 * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
	 * @return void
	 */
	public static function setConnectionResolver(Resolver $resolver)
	{
		static::$resolver = $resolver;
	}

	/**
	 * Unset the connection resolver for models.
	 *
	 * @return void
	 */
	public static function unsetConnectionResolver()
	{
		static::$resolver = null;
	}

	/**
	 * Get the event dispatcher instance.
	 *
	 * @return \Illuminate\Events\Dispatcher
	 */
	public static function getEventDispatcher()
	{
		return static::$dispatcher;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public static function setEventDispatcher(Dispatcher $dispatcher)
	{
		static::$dispatcher = $dispatcher;
	}

	/**
	 * Unset the event dispatcher for models.
	 *
	 * @return void
	 */
	public static function unsetEventDispatcher()
	{
		static::$dispatcher = null;
	}

	/**
	 * Get the mutated attributes for a given instance.
	 *
	 * @return array
	 */
	public function getMutatedAttributes()
	{
		$class = get_class($this);

		if (isset(static::$mutatorCache[$class]))
		{
			return static::$mutatorCache[get_class($this)];
		}

		return array();
	}

	/**
	 * Dynamically retrieve attributes on the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->getAttribute($key);
	}

	/**
	 * Dynamically set attributes on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->setAttribute($key, $value);
	}

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param  mixed  $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->$offset);
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->$offset;
	}

	/**
	 * Set the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
	}

	/**
	 * Unset the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}

	/**
	 * Determine if an attribute exists on the model.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return ((isset($this->attributes[$key]) || isset($this->relations[$key])) ||
				($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key))));
	}

	/**
	 * Unset an attribute on the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key]);

		unset($this->relations[$key]);
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, array('increment', 'decrement')))
		{
			return call_user_func_array(array($this, $method), $parameters);
		}

		$query = $this->newQuery();

		return call_user_func_array(array($query, $method), $parameters);
	}

	/**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = new static;

		return call_user_func_array(array($instance, $method), $parameters);
	}

	/**
	 * Convert the model to its string representation.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

	/**
	 * When a model is being unserialized, check if it needs to be booted.
	 *
	 * @return void
	 */
	public function __wakeup()
	{
		$this->bootIfNotBooted();
	}

}
