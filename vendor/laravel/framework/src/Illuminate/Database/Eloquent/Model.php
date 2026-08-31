<?php

namespace Illuminate\Database\Eloquent;

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Broadcasting\HasBroadcastChannel;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Attributes\Initialize;
use Illuminate\Database\Eloquent\Attributes\Scope as LocalScope;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable as SupportStringable;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonException;
use JsonSerializable;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use Stringable;

use function Illuminate\Support\enum_value;

abstract class Model implements Arrayable, ArrayAccess, CanBeEscapedWhenCastToString, HasBroadcastChannel, Jsonable, JsonSerializable, QueueableEntity, Stringable, UrlRoutable
{
    use Concerns\HasAttributes,
        Concerns\HasEvents,
        Concerns\HasGlobalScopes,
        Concerns\HasRelationships,
        Concerns\HasTimestamps,
        Concerns\HasUniqueIds,
        Concerns\HidesAttributes,
        Concerns\GuardsAttributes,
        Concerns\PreventsCircularRecursion,
        Concerns\TransformsToResource,
        ForwardsCalls;
    /** @use HasCollection<\Illuminate\Database\Eloquent\Collection<array-key, static & self>> */
    use HasCollection;

    /**
     * The connection name for the model.
     *
     * @var \UnitEnum|string|null
     */
    protected $connection;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The relationship counts that should be eager loaded on every query.
     *
     * @var array
     */
    protected $withCount = [];

    /**
     * Indicates whether lazy loading will be prevented on this model.
     *
     * @var bool
     */
    public $preventsLazyLoading = false;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Indicates if the model was inserted during the object's lifecycle.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;

    /**
     * Indicates that the object's string representation should be escaped when __toString is invoked.
     *
     * @var bool
     */
    protected $escapeWhenCastingToString = false;

    /**
     * The connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected static $resolver;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected static $dispatcher;

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * The callbacks that should be executed after the model has booted.
     *
     * @var array
     */
    protected static $bootedCallbacks = [];

    /**
     * The array of trait initializers that will be called on each new instance.
     *
     * @var array
     */
    protected static $traitInitializers = [];

    /**
     * The array of global scopes on the model.
     *
     * @var array
     */
    protected static $globalScopes = [];

    /**
     * The list of models classes that should not be affected with touch.
     *
     * @var array
     */
    protected static $ignoreOnTouch = [];

    /**
     * Indicates whether lazy loading should be restricted on all models.
     *
     * @var bool
     */
    protected static $modelsShouldPreventLazyLoading = false;

    /**
     * Indicates whether relations should be automatically loaded on all models when they are accessed.
     *
     * @var bool
     */
    protected static $modelsShouldAutomaticallyEagerLoadRelationships = false;

    /**
     * The callback that is responsible for handling lazy loading violations.
     *
     * @var (callable(self, string))|null
     */
    protected static $lazyLoadingViolationCallback;

    /**
     * Indicates if an exception should be thrown instead of silently discarding non-fillable attributes.
     *
     * @var bool
     */
    protected static $modelsShouldPreventSilentlyDiscardingAttributes = false;

    /**
     * The callback that is responsible for handling discarded attribute violations.
     *
     * @var (callable(self, array))|null
     */
    protected static $discardedAttributeViolationCallback;

    /**
     * Indicates if an exception should be thrown when trying to access a missing attribute on a retrieved model.
     *
     * @var bool
     */
    protected static $modelsShouldPreventAccessingMissingAttributes = false;

    /**
     * The callback that is responsible for handling missing attribute violations.
     *
     * @var (callable(self, string))|null
     */
    protected static $missingAttributeViolationCallback;

    /**
     * Indicates if broadcasting is currently enabled.
     *
     * @var bool
     */
    protected static $isBroadcasting = true;

    /**
     * The Eloquent query builder class to use for the model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Builder<*>>
     */
    protected static string $builder = Builder::class;

    /**
     * The Eloquent collection class to use for the model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Collection<*, *>>
     */
    protected static string $collectionClass = Collection::class;

    /**
     * Cache of soft deletable models.
     *
     * @var array<class-string<self>, bool>
     */
    protected static array $isSoftDeletable;

    /**
     * Cache of prunable models.
     *
     * @var array<class-string<self>, bool>
     */
    protected static array $isPrunable;

    /**
     * Cache of mass prunable models.
     *
     * @var array<class-string<self>, bool>
     */
    protected static array $isMassPrunable;

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

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
        if (! isset(static::$booted[static::class])) {
            static::$booted[static::class] = true;

            $this->fireModelEvent('booting', false);

            static::booting();
            static::boot();
            static::booted();

            static::$bootedCallbacks[static::class] ??= [];

            foreach (static::$bootedCallbacks[static::class] as $callback) {
                $callback();
            }

            $this->fireModelEvent('booted', false);
        }
    }

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booting()
    {
        //
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        static::bootTraits();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits()
    {
        $class = static::class;

        $booted = [];

        static::$traitInitializers[$class] = [];

        $uses = class_uses_recursive($class);

        $conventionalBootMethods = array_map(static fn ($trait) => 'boot'.class_basename($trait), $uses);
        $conventionalInitMethods = array_map(static fn ($trait) => 'initialize'.class_basename($trait), $uses);

        foreach ((new ReflectionClass($class))->getMethods() as $method) {
            if (! in_array($method->getName(), $booted) &&
                $method->isStatic() &&
                (in_array($method->getName(), $conventionalBootMethods) ||
                $method->getAttributes(Boot::class) !== [])) {
                $method->invoke(null);

                $booted[] = $method->getName();
            }

            if (in_array($method->getName(), $conventionalInitMethods) ||
                $method->getAttributes(Initialize::class) !== []) {
                static::$traitInitializers[$class][] = $method->getName();
            }
        }

        static::$traitInitializers[$class] = array_unique(static::$traitInitializers[$class]);
    }

    /**
     * Initialize any initializable traits on the model.
     *
     * @return void
     */
    protected function initializeTraits()
    {
        foreach (static::$traitInitializers[static::class] as $method) {
            $this->{$method}();
        }
    }

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        //
    }

    /**
     * Register a closure to be executed after the model has booted.
     *
     * @param  \Closure  $callback
     * @return void
     */
    protected static function whenBooted(Closure $callback)
    {
        static::$bootedCallbacks[static::class] ??= [];

        static::$bootedCallbacks[static::class][] = $callback;
    }

    /**
     * Clear the list of booted models so they will be re-booted.
     *
     * @return void
     */
    public static function clearBootedModels()
    {
        static::$booted = [];
        static::$bootedCallbacks = [];

        static::$globalScopes = [];
    }

    /**
     * Disables relationship model touching for the current class during given callback scope.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function withoutTouching(callable $callback)
    {
        static::withoutTouchingOn([static::class], $callback);
    }

    /**
     * Disables relationship model touching for the given model classes during given callback scope.
     *
     * @param  array  $models
     * @param  callable  $callback
     * @return void
     */
    public static function withoutTouchingOn(array $models, callable $callback)
    {
        static::$ignoreOnTouch = array_values(array_merge(static::$ignoreOnTouch, $models));

        try {
            $callback();
        } finally {
            static::$ignoreOnTouch = array_values(array_diff(static::$ignoreOnTouch, $models));
        }
    }

    /**
     * Determine if the given model is ignoring touches.
     *
     * @param  string|null  $class
     * @return bool
     */
    public static function isIgnoringTouch($class = null)
    {
        $class = $class ?: static::class;

        if (! get_class_vars($class)['timestamps'] || ! $class::UPDATED_AT) {
            return true;
        }

        foreach (static::$ignoreOnTouch as $ignoredClass) {
            if ($class === $ignoredClass || is_subclass_of($class, $ignoredClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Indicate that models should prevent lazy loading, silently discarding attributes, and accessing missing attributes.
     *
     * @param  bool  $shouldBeStrict
     * @return void
     */
    public static function shouldBeStrict(bool $shouldBeStrict = true)
    {
        static::preventLazyLoading($shouldBeStrict);
        static::preventSilentlyDiscardingAttributes($shouldBeStrict);
        static::preventAccessingMissingAttributes($shouldBeStrict);
    }

    /**
     * Prevent model relationships from being lazy loaded.
     *
     * @param  bool  $value
     * @return void
     */
    public static function preventLazyLoading($value = true)
    {
        static::$modelsShouldPreventLazyLoading = $value;
    }

    /**
     * Determine if model relationships should be automatically eager loaded when accessed.
     *
     * @param  bool  $value
     * @return void
     */
    public static function automaticallyEagerLoadRelationships($value = true)
    {
        static::$modelsShouldAutomaticallyEagerLoadRelationships = $value;
    }

    /**
     * Register a callback that is responsible for handling lazy loading violations.
     *
     * @param  (callable(self, string))|null  $callback
     * @return void
     */
    public static function handleLazyLoadingViolationUsing(?callable $callback)
    {
        static::$lazyLoadingViolationCallback = $callback;
    }

    /**
     * Prevent non-fillable attributes from being silently discarded.
     *
     * @param  bool  $value
     * @return void
     */
    public static function preventSilentlyDiscardingAttributes($value = true)
    {
        static::$modelsShouldPreventSilentlyDiscardingAttributes = $value;
    }

    /**
     * Register a callback that is responsible for handling discarded attribute violations.
     *
     * @param  (callable(self, array))|null  $callback
     * @return void
     */
    public static function handleDiscardedAttributeViolationUsing(?callable $callback)
    {
        static::$discardedAttributeViolationCallback = $callback;
    }

    /**
     * Prevent accessing missing attributes on retrieved models.
     *
     * @param  bool  $value
     * @return void
     */
    public static function preventAccessingMissingAttributes($value = true)
    {
        static::$modelsShouldPreventAccessingMissingAttributes = $value;
    }

    /**
     * Register a callback that is responsible for handling missing attribute violations.
     *
     * @param  (callable(self, string))|null  $callback
     * @return void
     */
    public static function handleMissingAttributeViolationUsing(?callable $callback)
    {
        static::$missingAttributeViolationCallback = $callback;
    }

    /**
     * Execute a callback without broadcasting any model events for all model types.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutBroadcasting(callable $callback)
    {
        $isBroadcasting = static::$isBroadcasting;

        static::$isBroadcasting = false;

        try {
            return $callback();
        } finally {
            static::$isBroadcasting = $isBroadcasting;
        }
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array<string, mixed>  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();

        $fillable = $this->fillableFromArray($attributes);

        foreach ($fillable as $key => $value) {
            // The developers may choose to place some attributes in the "fillable" array
            // which means only those attributes may be set through mass assignment to
            // the model, and all others will just get ignored for security reasons.
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded || static::preventsSilentlyDiscardingAttributes()) {
                if (isset(static::$discardedAttributeViolationCallback)) {
                    call_user_func(static::$discardedAttributeViolationCallback, $this, [$key]);
                } else {
                    throw new MassAssignmentException(sprintf(
                        'Add [%s] to fillable property to allow mass assignment on [%s].',
                        $key, get_class($this)
                    ));
                }
            }
        }

        if (count($attributes) !== count($fillable) &&
            static::preventsSilentlyDiscardingAttributes()) {
            $keys = array_diff(array_keys($attributes), array_keys($fillable));

            if (isset(static::$discardedAttributeViolationCallback)) {
                call_user_func(static::$discardedAttributeViolationCallback, $this, $keys);
            } else {
                throw new MassAssignmentException(sprintf(
                    'Add fillable property [%s] to allow mass assignment on [%s].',
                    implode(', ', $keys),
                    get_class($this)
                ));
            }
        }

        return $this;
    }

    /**
     * Fill the model with an array of attributes. Force mass assignment.
     *
     * @param  array<string, mixed>  $attributes
     * @return $this
     */
    public function forceFill(array $attributes)
    {
        return static::unguarded(fn () => $this->fill($attributes));
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyColumn($column)
    {
        if (str_contains($column, '.')) {
            return $column;
        }

        return $this->getTable().'.'.$column;
    }

    /**
     * Qualify the given columns with the model's table.
     *
     * @param  array  $columns
     * @return array
     */
    public function qualifyColumns($columns)
    {
        return (new BaseCollection($columns))
            ->map(fn ($column) => $this->qualifyColumn($column))
            ->all();
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array<string, mixed>  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new static;

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable($this->getTable());

        $model->mergeCasts($this->casts);

        $model->fill((array) $attributes);

        return $model;
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array<string, mixed>  $attributes
     * @param  \UnitEnum|string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?? $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * Begin querying the model on a given connection.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public static function on($connection = null)
    {
        // First we will just create a fresh instance of this model, and then we can set the
        // connection on the model so that it is used for the queries we execute, as well
        // as being set on every relation we retrieve without a custom connection name.
        return (new static)->setConnection($connection)->newQuery();
    }

    /**
     * Begin querying the model on the write connection.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public static function onWriteConnection()
    {
        return static::query()->useWritePdo();
    }

    /**
     * Get all of the models from the database.
     *
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function all($columns = ['*'])
    {
        return static::query()->get(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     * Begin querying a model with eager loading.
     *
     * @param  array|string  $relations
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public static function with($relations)
    {
        return static::query()->with(
            is_string($relations) ? func_get_args() : $relations
        );
    }

    /**
     * Eager load relations on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function load($relations)
    {
        $query = $this->newQueryWithoutRelationships()->with(
            is_string($relations) ? func_get_args() : $relations
        );

        $query->eagerLoadRelations([$this]);

        return $this;
    }

    /**
     * Eager load relationships on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @return $this
     */
    public function loadMorph($relation, $relations)
    {
        if (! $this->{$relation}) {
            return $this;
        }

        $className = get_class($this->{$relation});

        $this->{$relation}->load($relations[$className] ?? []);

        return $this;
    }

    /**
     * Eager load relations on the model if they are not already eager loaded.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadMissing($relations)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->newCollection([$this])->loadMissing($relations);

        return $this;
    }

    /**
     * Eager load relation's column aggregations on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @param  string|null  $function
     * @return $this
     */
    public function loadAggregate($relations, $column, $function = null)
    {
        $this->newCollection([$this])->loadAggregate($relations, $column, $function);

        return $this;
    }

    /**
     * Eager load relation counts on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadCount($relations)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        return $this->loadAggregate($relations, '*', 'count');
    }

    /**
     * Eager load relation max column values on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMax($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'max');
    }

    /**
     * Eager load relation min column values on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMin($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'min');
    }

    /**
     * Eager load relation's column summations on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadSum($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'sum');
    }

    /**
     * Eager load relation average column values on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadAvg($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'avg');
    }

    /**
     * Eager load related model existence values on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadExists($relations)
    {
        return $this->loadAggregate($relations, '*', 'exists');
    }

    /**
     * Eager load relationship column aggregation on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @param  string|null  $function
     * @return $this
     */
    public function loadMorphAggregate($relation, $relations, $column, $function = null)
    {
        if (! $this->{$relation}) {
            return $this;
        }

        $className = get_class($this->{$relation});

        $this->{$relation}->loadAggregate($relations[$className] ?? [], $column, $function);

        return $this;
    }

    /**
     * Eager load relationship counts on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @return $this
     */
    public function loadMorphCount($relation, $relations)
    {
        return $this->loadMorphAggregate($relation, $relations, '*', 'count');
    }

    /**
     * Eager load relationship max column values on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphMax($relation, $relations, $column)
    {
        return $this->loadMorphAggregate($relation, $relations, $column, 'max');
    }

    /**
     * Eager load relationship min column values on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphMin($relation, $relations, $column)
    {
        return $this->loadMorphAggregate($relation, $relations, $column, 'min');
    }

    /**
     * Eager load relationship column summations on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphSum($relation, $relations, $column)
    {
        return $this->loadMorphAggregate($relation, $relations, $column, 'sum');
    }

    /**
     * Eager load relationship average column values on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphAvg($relation, $relations, $column)
    {
        return $this->loadMorphAggregate($relation, $relations, $column, 'avg');
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @return int
     */
    protected function increment($column, $amount = 1, array $extra = [])
    {
        return $this->incrementOrDecrement($column, $amount, $extra, 'increment');
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @return int
     */
    protected function decrement($column, $amount = 1, array $extra = [])
    {
        return $this->incrementOrDecrement($column, $amount, $extra, 'decrement');
    }

    /**
     * Run the increment or decrement method on the model.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @param  string  $method
     * @return int
     */
    protected function incrementOrDecrement($column, $amount, $extra, $method)
    {
        if (! $this->exists) {
            return $this->newQueryWithoutRelationships()->{$method}($column, $amount, $extra);
        }

        $this->{$column} = $this->isClassDeviable($column)
            ? $this->deviateClassCastableAttribute($method, $column, $amount)
            : $this->{$column} + ($method === 'increment' ? $amount : $amount * -1);

        $this->forceFill($extra);

        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        if ($this->isClassDeviable($column)) {
            $amount = (clone $this)->setAttribute($column, $amount)->getAttributeFromArray($column);
        }

        return tap($this->setKeysForSaveQuery($this->newQueryWithoutScopes())->{$method}($column, $amount, $extra), function () use ($column) {
            $this->syncChanges();

            $this->fireModelEvent('updated', false);

            $this->syncOriginalAttribute($column);
        });
    }

    /**
     * Update the model in the database.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        return $this->fill($attributes)->save($options);
    }

    /**
     * Update the model in the database within a transaction.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $options
     * @return bool
     *
     * @throws \Throwable
     */
    public function updateOrFail(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        return $this->fill($attributes)->saveOrFail($options);
    }

    /**
     * Update the model in the database without raising any events.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $options
     * @return bool
     */
    public function updateQuietly(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        return $this->fill($attributes)->saveQuietly($options);
    }

    /**
     * Increment a column's value by a given amount without raising any events.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @return int
     */
    protected function incrementQuietly($column, $amount = 1, array $extra = [])
    {
        return static::withoutEvents(
            fn () => $this->incrementOrDecrement($column, $amount, $extra, 'increment')
        );
    }

    /**
     * Decrement a column's value by a given amount without raising any events.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @return int
     */
    protected function decrementQuietly($column, $amount = 1, array $extra = [])
    {
        return static::withoutEvents(
            fn () => $this->incrementOrDecrement($column, $amount, $extra, 'decrement')
        );
    }

    /**
     * Save the model and all of its relationships.
     *
     * @return bool
     */
    public function push()
    {
        return $this->withoutRecursion(function () {
            if (! $this->save()) {
                return false;
            }

            // To sync all of the relationships to the database, we will simply spin through
            // the relationships and save each model via this "push" method, which allows
            // us to recurse into all of these nested relations for the model instance.
            foreach ($this->relations as $models) {
                $models = $models instanceof Collection
                    ? $models->all()
                    : [$models];

                foreach (array_filter($models) as $model) {
                    if (! $model->push()) {
                        return false;
                    }
                }
            }

            return true;
        }, true);
    }

    /**
     * Save the model and all of its relationships without raising any events to the parent model.
     *
     * @return bool
     */
    public function pushQuietly()
    {
        return static::withoutEvents(fn () => $this->push());
    }

    /**
     * Save the model to the database without raising any events.
     *
     * @param  array  $options
     * @return bool
     */
    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(fn () => $this->save($options));
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->mergeAttributesFromCachedCasts();

        $query = $this->newModelQuery();

        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists) {
            $saved = $this->isDirty() ?
                $this->performUpdate($query) : true;
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($query);

            if (! $this->getConnectionName() &&
                $connection = $query->getConnection()) {
                $this->setConnection($connection->getName());
            }
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    /**
     * Save the model to the database within a transaction.
     *
     * @param  array  $options
     * @return bool
     *
     * @throws \Throwable
     */
    public function saveOrFail(array $options = [])
    {
        return $this->getConnection()->transaction(fn () => $this->save($options));
    }

    /**
     * Perform any actions that are necessary after the model is saved.
     *
     * @param  array  $options
     * @return void
     */
    protected function finishSave(array $options)
    {
        $this->fireModelEvent('saved', false);

        if ($this->isDirty() && ($options['touch'] ?? true)) {
            $this->touchOwners();
        }

        $this->syncOriginal();
    }

    /**
     * Perform a model update operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return bool
     */
    protected function performUpdate(Builder $query)
    {
        // If the updating event returns false, we will cancel the update operation so
        // developers can hook Validation systems into their models and cancel this
        // operation if the model does not pass validation. Otherwise, we update.
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        // First we need to create a fresh query instance and touch the creation and
        // update timestamp on the model which are maintained by us for developer
        // convenience. Then we will just continue saving the model instances.
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        // Once we have run the update operation, we will fire the "updated" event for
        // this model instance. This will allow developers to hook into these after
        // models are updated, giving them a chance to do any special processing.
        $dirty = $this->getDirtyForUpdate();

        if (count($dirty) > 0) {
            $this->setKeysForSaveQuery($query)->update($dirty);

            $this->syncChanges();

            $this->fireModelEvent('updated', false);
        }

        return true;
    }

    /**
     * Set the keys for a select query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    protected function setKeysForSelectQuery($query)
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSelectQuery());

        return $query;
    }

    /**
     * Get the primary key value for a select query.
     *
     * @return mixed
     */
    protected function getKeyForSelectQuery()
    {
        return $this->original[$this->getKeyName()] ?? $this->getKey();
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    protected function setKeysForSaveQuery($query)
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
        return $this->original[$this->getKeyName()] ?? $this->getKey();
    }

    /**
     * Perform a model insert operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return bool
     */
    protected function performInsert(Builder $query)
    {
        if ($this->usesUniqueIds()) {
            $this->setUniqueIds();
        }

        if ($this->fireModelEvent('creating') === false) {
            return false;
        }

        // First we'll need to create a fresh query instance and touch the creation and
        // update timestamps on this model, which are maintained by us for developer
        // convenience. After, we will just continue saving these model instances.
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        // If the model has an incrementing key, we can use the "insertGetId" method on
        // the query builder, which will give us back the final inserted ID for this
        // table from the database. Not all tables have to be incrementing though.
        $attributes = $this->getAttributesForInsert();

        if ($this->getIncrementing()) {
            $this->insertAndSetId($query, $attributes);
        }

        // If the table isn't incrementing we'll simply insert these attributes as they
        // are. These attribute arrays must contain an "id" column previously placed
        // there by the developer as the manually determined key for these models.
        else {
            if (empty($attributes)) {
                return true;
            }

            $query->insert($attributes);
        }

        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created', false);

        return true;
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @param  array<string, mixed>  $attributes
     * @return void
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        $this->setAttribute($keyName, $id);
    }

    /**
     * Destroy the models for the given IDs.
     *
     * @param  \Illuminate\Support\Collection|array|int|string  $ids
     * @return int
     */
    public static function destroy($ids)
    {
        if ($ids instanceof EloquentCollection) {
            $ids = $ids->modelKeys();
        }

        if ($ids instanceof BaseCollection) {
            $ids = $ids->all();
        }

        $ids = is_array($ids) ? $ids : func_get_args();

        if (count($ids) === 0) {
            return 0;
        }

        // We will actually pull the models from the database table and call delete on
        // each of them individually so that their events get fired properly with a
        // correct set of attributes in case the developers wants to check these.
        $key = ($instance = new static)->getKeyName();

        $count = 0;

        foreach ($instance->whereIn($key, $ids)->get() as $model) {
            if ($model->delete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \LogicException
     */
    public function delete()
    {
        $this->mergeAttributesFromCachedCasts();

        if (is_null($this->getKeyName())) {
            throw new LogicException('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to delete so we'll just return
        // immediately and not do anything else. Otherwise, we will continue with a
        // deletion process on the model, firing the proper events, and so forth.
        if (! $this->exists) {
            return;
        }

        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // Here, we'll touch the owning models, verifying these timestamps get updated
        // for the models. This will allow any caching to get broken on the parents
        // by the timestamp. Then we will go ahead and delete the model instance.
        $this->touchOwners();

        $this->performDeleteOnModel();

        // Once the model has been deleted, we will fire off the deleted event so that
        // the developers may hook into post-delete operations. We will then return
        // a boolean true as the delete is presumably successful on the database.
        $this->fireModelEvent('deleted', false);

        return true;
    }

    /**
     * Delete the model from the database without raising any events.
     *
     * @return bool
     */
    public function deleteQuietly()
    {
        return static::withoutEvents(fn () => $this->delete());
    }

    /**
     * Delete the model from the database within a transaction.
     *
     * @return bool|null
     *
     * @throws \Throwable
     */
    public function deleteOrFail()
    {
        if (! $this->exists) {
            return false;
        }

        return $this->getConnection()->transaction(fn () => $this->delete());
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when the trait is missing.
     *
     * @return bool|null
     */
    public function forceDelete()
    {
        return $this->delete();
    }

    /**
     * Force a hard destroy on a soft deleted model.
     *
     * This method protects developers from running forceDestroy when the trait is missing.
     *
     * @param  \Illuminate\Support\Collection|array|int|string  $ids
     * @return bool|null
     */
    public static function forceDestroy($ids)
    {
        return static::destroy($ids);
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        $this->setKeysForSaveQuery($this->newModelQuery())->delete();

        $this->exists = false;
    }

    /**
     * Begin querying the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public static function query()
    {
        return (new static)->newQuery();
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function newQuery()
    {
        return $this->registerGlobalScopes($this->newQueryWithoutScopes());
    }

    /**
     * Get a new query builder that doesn't have any global scopes or eager loading.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function newModelQuery()
    {
        return $this->newEloquentBuilder(
            $this->newBaseQueryBuilder()
        )->setModel($this);
    }

    /**
     * Get a new query builder with no relationships loaded.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function newQueryWithoutRelationships()
    {
        return $this->registerGlobalScopes($this->newModelQuery());
    }

    /**
     * Register the global scopes for this builder instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $builder
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function registerGlobalScopes($builder)
    {
        foreach ($this->getGlobalScopes() as $identifier => $scope) {
            $builder->withGlobalScope($identifier, $scope);
        }

        return $builder;
    }

    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function newQueryWithoutScopes()
    {
        return $this->newModelQuery()
            ->with($this->with)
            ->withCount($this->withCount);
    }

    /**
     * Get a new query instance without a given scope.
     *
     * @param  \Illuminate\Database\Eloquent\Scope|string  $scope
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function newQueryWithoutScope($scope)
    {
        return $this->newQuery()->withoutGlobalScope($scope);
    }

    /**
     * Get a new query to restore one or more models by their queueable IDs.
     *
     * @param  array|int  $ids
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function newQueryForRestoration($ids)
    {
        return $this->newQueryWithoutScopes()->whereKey($ids);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder<*>
     */
    public function newEloquentBuilder($query)
    {
        $builderClass = $this->resolveCustomBuilderClass();

        if ($builderClass && is_subclass_of($builderClass, Builder::class)) {
            return new $builderClass($query);
        }

        return new static::$builder($query);
    }

    /**
     * Resolve the custom Eloquent builder class from the model attributes.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Builder>|false
     */
    protected function resolveCustomBuilderClass()
    {
        $attributes = (new ReflectionClass($this))
            ->getAttributes(UseEloquentBuilder::class);

        return ! empty($attributes)
            ? $attributes[0]->newInstance()->builderClass
            : false;
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        return $this->getConnection()->query();
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array<string, mixed>  $attributes
     * @param  string  $table
     * @param  bool  $exists
     * @param  string|null  $using
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(self $parent, array $attributes, $table, $exists, $using = null)
    {
        return $using ? $using::fromRawAttributes($parent, $attributes, $table, $exists)
            : Pivot::fromAttributes($parent, $attributes, $table, $exists);
    }

    /**
     * Determine if the model has a given scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function hasNamedScope($scope)
    {
        return method_exists($this, 'scope'.ucfirst($scope)) ||
            static::isScopeMethodWithAttribute($scope);
    }

    /**
     * Apply the given named scope if possible.
     *
     * @param  string  $scope
     * @param  array  $parameters
     * @return mixed
     */
    public function callNamedScope($scope, array $parameters = [])
    {
        if ($this->isScopeMethodWithAttribute($scope)) {
            return $this->{$scope}(...$parameters);
        }

        return $this->{'scope'.ucfirst($scope)}(...$parameters);
    }

    /**
     * Determine if the given method has a scope attribute.
     *
     * @param  string  $method
     * @return bool
     */
    protected static function isScopeMethodWithAttribute(string $method)
    {
        if (method_exists(static::class, $method)) {
            $reflectionClass = new ReflectionMethod(static::class, $method);

            return ! $reflectionClass->isPrivate() && $reflectionClass->getAttributes(LocalScope::class) !== [];
        }

        return false;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->withoutRecursion(
            fn () => array_merge($this->attributesToArray(), $this->relationsToArray()),
            fn () => $this->attributesToArray(),
        );
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        try {
            $json = json_encode($this->jsonSerialize(), $options | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw JsonEncodingException::forModel($this, $e->getMessage());
        }

        return $json;
    }

    /**
     * Convert the model instance to pretty print formatted JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toPrettyJson(int $options = 0)
    {
        return $this->toJson(JSON_PRETTY_PRINT | $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Reload a fresh model instance from the database.
     *
     * @param  array|string  $with
     * @return static|null
     */
    public function fresh($with = [])
    {
        if (! $this->exists) {
            return;
        }

        return $this->setKeysForSelectQuery($this->newQueryWithoutScopes())
            ->useWritePdo()
            ->with(is_string($with) ? func_get_args() : $with)
            ->first();
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    public function refresh()
    {
        if (! $this->exists) {
            return $this;
        }

        $this->setRawAttributes(
            $this->setKeysForSelectQuery($this->newQueryWithoutScopes())
                ->useWritePdo()
                ->firstOrFail()
                ->attributes
        );

        $this->load((new BaseCollection($this->relations))->reject(
            fn ($relation) => $relation instanceof Pivot
                || (is_object($relation) && in_array(AsPivot::class, class_uses_recursive($relation), true))
        )->keys()->all());

        $this->syncOriginal();

        return $this;
    }

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array|null  $except
     * @return static
     */
    public function replicate(?array $except = null)
    {
        $defaults = array_values(array_filter([
            $this->getKeyName(),
            $this->getCreatedAtColumn(),
            $this->getUpdatedAtColumn(),
            ...$this->uniqueIds(),
            'laravel_through_key',
        ]));

        $attributes = Arr::except(
            $this->getAttributes(), $except ? array_unique(array_merge($except, $defaults)) : $defaults
        );

        return tap(new static, function ($instance) use ($attributes) {
            $instance->setRawAttributes($attributes);

            $instance->setRelations($this->relations);

            $instance->fireModelEvent('replicating', false);
        });
    }

    /**
     * Clone the model into a new, non-existing instance without raising any events.
     *
     * @param  array|null  $except
     * @return static
     */
    public function replicateQuietly(?array $except = null)
    {
        return static::withoutEvents(fn () => $this->replicate($except));
    }

    /**
     * Determine if two models have the same ID and belong to the same table.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return bool
     */
    public function is($model)
    {
        return ! is_null($model) &&
            $this->getKey() === $model->getKey() &&
            $this->getTable() === $model->getTable() &&
            $this->getConnectionName() === $model->getConnectionName();
    }

    /**
     * Determine if two models are not the same.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return bool
     */
    public function isNot($model)
    {
        return ! $this->is($model);
    }

    /**
     * Get the database connection for the model.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return static::resolveConnection($this->getConnectionName());
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return enum_value($this->connection);
    }

    /**
     * Set the connection associated with the model.
     *
     * @param  \UnitEnum|string|null  $name
     * @return $this
     */
    public function setConnection($name)
    {
        $this->connection = $name;

        return $this;
    }

    /**
     * Resolve a connection instance.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return \Illuminate\Database\Connection
     */
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface|null
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
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
    }

    /**
     * Set the table associated with the model.
     *
     * @param  string  $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
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
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->qualifyColumn($this->getKeyName());
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return $this->keyType;
    }

    /**
     * Set the data type for the primary key.
     *
     * @param  string  $type
     * @return $this
     */
    public function setKeyType($type)
    {
        $this->keyType = $type;

        return $this;
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
     * @return $this
     */
    public function setIncrementing($value)
    {
        $this->incrementing = $value;

        return $this;
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
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId()
    {
        return $this->getKey();
    }

    /**
     * Get the queueable relationships for the entity.
     *
     * @return array
     */
    public function getQueueableRelations()
    {
        return $this->withoutRecursion(function () {
            $relations = [];

            foreach ($this->getRelations() as $key => $relation) {
                if (! method_exists($this, $key)) {
                    continue;
                }

                $relations[] = $key;

                if ($relation instanceof QueueableCollection) {
                    foreach ($relation->getQueueableRelations() as $collectionValue) {
                        $relations[] = $key.'.'.$collectionValue;
                    }
                }

                if ($relation instanceof QueueableEntity) {
                    foreach ($relation->getQueueableRelations() as $entityValue) {
                        $relations[] = $key.'.'.$entityValue;
                    }
                }
            }

            return array_unique($relations);
        }, []);
    }

    /**
     * Get the queueable connection for the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        return $this->getConnectionName();
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->resolveRouteBindingQuery($this, $value, $field)->first();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        return $this->resolveRouteBindingQuery($this, $value, $field)->withTrashed()->first();
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->resolveChildRouteBindingQuery($childType, $value, $field)->first();
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveSoftDeletableChildRouteBinding($childType, $value, $field)
    {
        return $this->resolveChildRouteBindingQuery($childType, $value, $field)->withTrashed()->first();
    }

    /**
     * Retrieve the child model query for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, $this, *>
     */
    protected function resolveChildRouteBindingQuery($childType, $value, $field)
    {
        $relationship = $this->{$this->childRouteBindingRelationshipName($childType)}();

        $field = $field ?: $relationship->getRelated()->getRouteKeyName();

        if ($relationship instanceof HasManyThrough ||
            $relationship instanceof BelongsToMany) {
            $field = $relationship->getRelated()->qualifyColumn($field);
        }

        return $relationship instanceof Model
            ? $relationship->resolveRouteBindingQuery($relationship, $value, $field)
            : $relationship->getRelated()->resolveRouteBindingQuery($relationship, $value, $field);
    }

    /**
     * Retrieve the child route model binding relationship name for the given child type.
     *
     * @param  string  $childType
     * @return string
     */
    protected function childRouteBindingRelationshipName($childType)
    {
        return Str::plural(Str::camel($childType));
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return $query->where($field ?? $this->getRouteKeyName(), $value);
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return Str::snake(class_basename($this)).'_'.$this->getKeyName();
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
     * @param  int  $perPage
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Determine if the model is soft deletable.
     */
    public static function isSoftDeletable(): bool
    {
        return static::$isSoftDeletable[static::class] ??= in_array(SoftDeletes::class, class_uses_recursive(static::class));
    }

    /**
     * Determine if the model is prunable.
     */
    protected function isPrunable(): bool
    {
        return self::$isPrunable[static::class] ??= in_array(Prunable::class, class_uses_recursive(static::class)) || static::isMassPrunable();
    }

    /**
     * Determine if the model is mass prunable.
     */
    protected function isMassPrunable(): bool
    {
        return self::$isMassPrunable[static::class] ??= in_array(MassPrunable::class, class_uses_recursive(static::class));
    }

    /**
     * Determine if lazy loading is disabled.
     *
     * @return bool
     */
    public static function preventsLazyLoading()
    {
        return static::$modelsShouldPreventLazyLoading;
    }

    /**
     * Determine if relationships are being automatically eager loaded when accessed.
     *
     * @return bool
     */
    public static function isAutomaticallyEagerLoadingRelationships()
    {
        return static::$modelsShouldAutomaticallyEagerLoadRelationships;
    }

    /**
     * Determine if discarding guarded attribute fills is disabled.
     *
     * @return bool
     */
    public static function preventsSilentlyDiscardingAttributes()
    {
        return static::$modelsShouldPreventSilentlyDiscardingAttributes;
    }

    /**
     * Determine if accessing missing attributes is disabled.
     *
     * @return bool
     */
    public static function preventsAccessingMissingAttributes()
    {
        return static::$modelsShouldPreventAccessingMissingAttributes;
    }

    /**
     * Get the broadcast channel route definition that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannelRoute()
    {
        return str_replace('\\', '.', get_class($this)).'.{'.Str::camel(class_basename($this)).'}';
    }

    /**
     * Get the broadcast channel name that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannel()
    {
        return str_replace('\\', '.', get_class($this)).'.'.$this->getKey();
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
     * @param  mixed  $value
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
    public function offsetExists($offset): bool
    {
        $shouldPrevent = static::$modelsShouldPreventAccessingMissingAttributes;

        static::$modelsShouldPreventAccessingMissingAttributes = false;

        try {
            return ! is_null($this->getAttribute($offset));
        } finally {
            static::$modelsShouldPreventAccessingMissingAttributes = $shouldPrevent;
        }
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset(
            $this->attributes[$offset],
            $this->relations[$offset],
            $this->attributeCastCache[$offset],
            $this->classCastCache[$offset]
        );
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement', 'incrementQuietly', 'decrementQuietly'])) {
            return $this->$method(...$parameters);
        }

        if ($resolver = $this->relationResolver(static::class, $method)) {
            return $resolver($this);
        }

        if (Str::startsWith($method, 'through') &&
            method_exists($this, $relationMethod = (new SupportStringable($method))->after('through')->lcfirst()->toString())) {
            return $this->through($relationMethod);
        }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if (static::isScopeMethodWithAttribute($method)) {
            return static::query()->$method(...$parameters);
        }

        return (new static)->$method(...$parameters);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->escapeWhenCastingToString
            ? e($this->toJson())
            : $this->toJson();
    }

    /**
     * Indicate that the object's string representation should be escaped when __toString is invoked.
     *
     * @param  bool  $escape
     * @return $this
     */
    public function escapeWhenCastingToString($escape = true)
    {
        $this->escapeWhenCastingToString = $escape;

        return $this;
    }

    /**
     * Prepare the object for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $this->mergeAttributesFromCachedCasts();

        $this->classCastCache = [];
        $this->attributeCastCache = [];
        $this->relationAutoloadCallback = null;
        $this->relationAutoloadContext = null;

        $keys = get_object_vars($this);

        if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
            foreach ((new ReflectionClass($this))->getProperties() as $property) {
                if ($property->hasHooks()) {
                    unset($keys[$property->getName()]);
                }
            }
        }

        return array_keys($keys);
    }

    /**
     * When a model is being unserialized, check if it needs to be booted.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

        if (static::isAutomaticallyEagerLoadingRelationships()) {
            $this->withRelationshipAutoloading();
        }
    }
}
