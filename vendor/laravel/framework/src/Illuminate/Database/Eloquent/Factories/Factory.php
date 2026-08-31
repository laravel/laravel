<?php

namespace Illuminate\Database\Eloquent\Factories;

use Closure;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use Throwable;
use UnitEnum;

use function Illuminate\Support\enum_value;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @method $this trashed()
 */
abstract class Factory
{
    use Conditionable, ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model;

    /**
     * The number of models that should be generated.
     *
     * @var int|null
     */
    protected $count;

    /**
     * The state transformations that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $states;

    /**
     * The parent relationships that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $has;

    /**
     * The child relationships that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $for;

    /**
     * The model instances to always use when creating relationships.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $recycle;

    /**
     * The "after making" callbacks that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $afterMaking;

    /**
     * The "after creating" callbacks that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $afterCreating;

    /**
     * Whether relationships should not be automatically created.
     *
     * @var bool
     */
    protected $expandRelationships = true;

    /**
     * The relationships that should not be automatically created.
     *
     * @var array
     */
    protected $excludeRelationships = [];

    /**
     * The name of the database connection that will be used to create the models.
     *
     * @var \UnitEnum|string|null
     */
    protected $connection;

    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * The default namespace where factories reside.
     *
     * @var string
     */
    public static $namespace = 'Database\\Factories\\';

    /**
     * @deprecated use $modelNameResolvers
     *
     * @var callable(self): class-string<TModel>
     */
    protected static $modelNameResolver;

    /**
     * The default model name resolvers.
     *
     * @var array<class-string, callable(self): class-string<TModel>>
     */
    protected static $modelNameResolvers = [];

    /**
     * The factory name resolver.
     *
     * @var callable
     */
    protected static $factoryNameResolver;

    /**
     * Whether to expand relationships by default.
     *
     * @var bool
     */
    protected static $expandRelationshipsByDefault = true;

    /**
     * Create a new factory instance.
     *
     * @param  int|null  $count
     * @param  \Illuminate\Support\Collection|null  $states
     * @param  \Illuminate\Support\Collection|null  $has
     * @param  \Illuminate\Support\Collection|null  $for
     * @param  \Illuminate\Support\Collection|null  $afterMaking
     * @param  \Illuminate\Support\Collection|null  $afterCreating
     * @param  \UnitEnum|string|null  $connection
     * @param  \Illuminate\Support\Collection|null  $recycle
     * @param  bool|null  $expandRelationships
     * @param  array  $excludeRelationships
     */
    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null,
        ?Collection $recycle = null,
        ?bool $expandRelationships = null,
        array $excludeRelationships = [],
    ) {
        $this->count = $count;
        $this->states = $states ?? new Collection;
        $this->has = $has ?? new Collection;
        $this->for = $for ?? new Collection;
        $this->afterMaking = $afterMaking ?? new Collection;
        $this->afterCreating = $afterCreating ?? new Collection;
        $this->connection = $connection;
        $this->recycle = $recycle ?? new Collection;
        $this->faker = $this->withFaker();
        $this->expandRelationships = $expandRelationships ?? self::$expandRelationshipsByDefault;
        $this->excludeRelationships = $excludeRelationships;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    abstract public function definition();

    /**
     * Get a new factory instance for the given attributes.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @return static
     */
    public static function new($attributes = [])
    {
        return (new static)->state($attributes)->configure();
    }

    /**
     * Get a new factory instance for the given number of models.
     *
     * @param  int  $count
     * @return static
     */
    public static function times(int $count)
    {
        return static::new()->count($count);
    }

    /**
     * Configure the factory.
     *
     * @return static
     */
    public function configure()
    {
        return $this;
    }

    /**
     * Get the raw attributes generated by the factory.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return array<int|string, mixed>
     */
    public function raw($attributes = [], ?Model $parent = null)
    {
        if ($this->count === null) {
            return $this->state($attributes)->getExpandedAttributes($parent);
        }

        return array_map(function () use ($attributes, $parent) {
            return $this->state($attributes)->getExpandedAttributes($parent);
        }, range(1, $this->count));
    }

    /**
     * Create a single model and persist it to the database.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @return TModel
     */
    public function createOne($attributes = [])
    {
        return $this->count(null)->create($attributes);
    }

    /**
     * Create a single model and persist it to the database without dispatching any model events.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @return TModel
     */
    public function createOneQuietly($attributes = [])
    {
        return $this->count(null)->createQuietly($attributes);
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param  int|null|iterable<int, array<string, mixed>>  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function createMany(int|iterable|null $records = null)
    {
        $records ??= ($this->count ?? 1);

        $this->count = null;

        if (is_numeric($records)) {
            $records = array_fill(0, $records, []);
        }

        return new EloquentCollection(
            (new Collection($records))->map(function ($record) {
                return $this->state($record)->create();
            })
        );
    }

    /**
     * Create a collection of models and persist them to the database without dispatching any model events.
     *
     * @param  int|null|iterable<int, array<string, mixed>>  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function createManyQuietly(int|iterable|null $records = null)
    {
        return Model::withoutEvents(fn () => $this->createMany($records));
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>|TModel
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        if (! empty($attributes)) {
            return $this->state($attributes)->create([], $parent);
        }

        $results = $this->make($attributes, $parent);

        if ($results instanceof Model) {
            $this->store(new Collection([$results]));

            $this->callAfterCreating(new Collection([$results]), $parent);
        } else {
            $this->store($results);

            $this->callAfterCreating($results, $parent);
        }

        return $results;
    }

    /**
     * Create a collection of models and persist them to the database without dispatching any model events.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>|TModel
     */
    public function createQuietly($attributes = [], ?Model $parent = null)
    {
        return Model::withoutEvents(fn () => $this->create($attributes, $parent));
    }

    /**
     * Create a callback that persists a model in the database when invoked.
     *
     * @param  array<string, mixed>  $attributes
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return \Closure(): (\Illuminate\Database\Eloquent\Collection<int, TModel>|TModel)
     */
    public function lazy(array $attributes = [], ?Model $parent = null)
    {
        return fn () => $this->create($attributes, $parent);
    }

    /**
     * Set the connection name on the results and store them.
     *
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>  $results
     * @return void
     */
    protected function store(Collection $results)
    {
        $results->each(function ($model) {
            if (! isset($this->connection)) {
                $model->setConnection($model->newQueryWithoutScopes()->getConnection()->getName());
            }

            $model->save();

            foreach ($model->getRelations() as $name => $items) {
                if ($items instanceof Enumerable && $items->isEmpty()) {
                    $model->unsetRelation($name);
                }
            }

            $this->createChildren($model);
        });
    }

    /**
     * Create the children for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    protected function createChildren(Model $model)
    {
        Model::unguarded(function () use ($model) {
            $this->has->each(function ($has) use ($model) {
                $has->recycle($this->recycle)->createFor($model);
            });
        });
    }

    /**
     * Make a single instance of the model.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @return TModel
     */
    public function makeOne($attributes = [])
    {
        return $this->count(null)->make($attributes);
    }

    /**
     * Create a collection of models.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>|TModel
     */
    public function make($attributes = [], ?Model $parent = null)
    {
        $autoEagerLoadingEnabled = Model::isAutomaticallyEagerLoadingRelationships();

        if ($autoEagerLoadingEnabled) {
            Model::automaticallyEagerLoadRelationships(false);
        }

        try {
            if (! empty($attributes)) {
                return $this->state($attributes)->make([], $parent);
            }

            if ($this->count === null) {
                return tap($this->makeInstance($parent), function ($instance) {
                    $this->callAfterMaking(new Collection([$instance]));
                });
            }

            if ($this->count < 1) {
                return $this->newModel()->newCollection();
            }

            $instances = $this->newModel()->newCollection(array_map(function () use ($parent) {
                return $this->makeInstance($parent);
            }, range(1, $this->count)));

            $this->callAfterMaking($instances);

            return $instances;
        } finally {
            Model::automaticallyEagerLoadRelationships($autoEagerLoadingEnabled);
        }
    }

    /**
     * Create a collection of models.
     *
     * @param  iterable<int, array<string, mixed>>|int|null  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function makeMany(iterable|int|null $records = null)
    {
        $records ??= ($this->count ?? 1);

        $this->count = null;

        if (is_numeric($records)) {
            $records = array_fill(0, $records, []);
        }

        return new EloquentCollection(
            (new Collection($records))->map(function ($record) {
                return $this->state($record)->make();
            })
        );
    }

    /**
     * Insert the model records in bulk. No model events are emitted.
     *
     * @param  array<string, mixed>  $attributes
     * @param  Model|null  $parent
     * @return void
     */
    public function insert(array $attributes = [], ?Model $parent = null): void
    {
        $made = $this->make($attributes, $parent);

        $madeCollection = $made instanceof Collection
            ? $made
            : $this->newModel()->newCollection([$made]);

        $model = $madeCollection->first();

        if (isset($this->connection)) {
            $model->setConnection($this->connection);
        }

        $query = $model->newQueryWithoutScopes();

        $query->fillAndInsert(
            $madeCollection->withoutAppends()
                ->setHidden([])
                ->map(static fn (Model $model) => $model->attributesToArray())
                ->all()
        );
    }

    /**
     * Make an instance of the model with the given attributes.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function makeInstance(?Model $parent)
    {
        return Model::unguarded(function () use ($parent) {
            return tap($this->newModel($this->getExpandedAttributes($parent)), function ($instance) {
                if (isset($this->connection)) {
                    $instance->setConnection($this->connection);
                }
            });
        });
    }

    /**
     * Get a raw attributes array for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return mixed
     */
    protected function getExpandedAttributes(?Model $parent)
    {
        return $this->expandAttributes($this->getRawAttributes($parent));
    }

    /**
     * Get the raw attributes for the model as an array.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return array
     */
    protected function getRawAttributes(?Model $parent)
    {
        return $this->states->pipe(function ($states) {
            return $this->for->isEmpty() ? $states : new Collection(array_merge([function () {
                return $this->parentResolvers();
            }], $states->all()));
        })->reduce(function ($carry, $state) use ($parent) {
            if ($state instanceof Closure) {
                $state = $state->bindTo($this);
            }

            return array_merge($carry, $state($carry, $parent));
        }, $this->definition());
    }

    /**
     * Create the parent relationship resolvers (as deferred Closures).
     *
     * @return array
     */
    protected function parentResolvers()
    {
        return $this->for
            ->map(fn (BelongsToRelationship $for) => $for->recycle($this->recycle)->attributesFor($this->newModel()))
            ->collapse()
            ->all();
    }

    /**
     * Expand all attributes to their underlying values.
     *
     * @param  array  $definition
     * @return array
     */
    protected function expandAttributes(array $definition)
    {
        return (new Collection($definition))
            ->map($evaluateRelations = function ($attribute, $key) {
                if (! $this->expandRelationships && $attribute instanceof self) {
                    $attribute = null;
                } elseif ($attribute instanceof self &&
                    array_intersect([$attribute->modelName(), $key], $this->excludeRelationships)) {
                    $attribute = null;
                } elseif ($attribute instanceof self) {
                    $attribute = $this->getRandomRecycledModel($attribute->modelName())?->getKey()
                        ?? $attribute->recycle($this->recycle)->create()->getKey();
                } elseif ($attribute instanceof Model) {
                    $attribute = $attribute->getKey();
                }

                return $attribute;
            })
            ->map(function ($attribute, $key) use (&$definition, $evaluateRelations) {
                if (is_callable($attribute) && ! is_string($attribute) && ! is_array($attribute)) {
                    $attribute = $attribute($definition);
                }

                $attribute = $evaluateRelations($attribute, $key);

                $definition[$key] = $attribute;

                return $attribute;
            })
            ->all();
    }

    /**
     * Add a new state transformation to the model definition.
     *
     * @param  (callable(array<string, mixed>, Model|null): array<string, mixed>)|array<string, mixed>  $state
     * @return static
     */
    public function state($state)
    {
        return $this->newInstance([
            'states' => $this->states->concat([
                is_callable($state) ? $state : fn () => $state,
            ]),
        ]);
    }

    /**
     * Prepend a new state transformation to the model definition.
     *
     * @param  (callable(array<string, mixed>, Model|null): array<string, mixed>)|array<string, mixed>  $state
     * @return static
     */
    public function prependState($state)
    {
        return $this->newInstance([
            'states' => $this->states->prepend(
                is_callable($state) ? $state : fn () => $state,
            ),
        ]);
    }

    /**
     * Set a single model attribute.
     *
     * @param  string|int  $key
     * @param  mixed  $value
     * @return static
     */
    public function set($key, $value)
    {
        return $this->state([$key => $value]);
    }

    /**
     * Add a new sequenced state transformation to the model definition.
     *
     * @param  mixed  ...$sequence
     * @return static
     */
    public function sequence(...$sequence)
    {
        return $this->state(new Sequence(...$sequence));
    }

    /**
     * Add a new sequenced state transformation to the model definition and update the pending creation count to the size of the sequence.
     *
     * @param  array  ...$sequence
     * @return static
     */
    public function forEachSequence(...$sequence)
    {
        return $this->state(new Sequence(...$sequence))->count(count($sequence));
    }

    /**
     * Add a new cross joined sequenced state transformation to the model definition.
     *
     * @param  array  ...$sequence
     * @return static
     */
    public function crossJoinSequence(...$sequence)
    {
        return $this->state(new CrossJoinSequence(...$sequence));
    }

    /**
     * Define a child relationship for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Factories\Factory  $factory
     * @param  string|null  $relationship
     * @return static
     */
    public function has(self $factory, $relationship = null)
    {
        return $this->newInstance([
            'has' => $this->has->concat([new Relationship(
                $factory, $relationship ?? $this->guessRelationship($factory->modelName())
            )]),
        ]);
    }

    /**
     * Attempt to guess the relationship name for a "has" relationship.
     *
     * @param  string  $related
     * @return string
     */
    protected function guessRelationship(string $related)
    {
        $guess = Str::camel(Str::plural(class_basename($related)));

        return method_exists($this->modelName(), $guess) ? $guess : Str::singular($guess);
    }

    /**
     * Define an attached relationship for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Factories\Factory|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array  $factory
     * @param  (callable(): array<string, mixed>)|array<string, mixed>  $pivot
     * @param  string|null  $relationship
     * @return static
     */
    public function hasAttached($factory, $pivot = [], $relationship = null)
    {
        return $this->newInstance([
            'has' => $this->has->concat([new BelongsToManyRelationship(
                $factory,
                $pivot,
                $relationship ?? Str::camel(Str::plural(class_basename(
                    $factory instanceof Factory
                        ? $factory->modelName()
                        : Collection::wrap($factory)->first()
                )))
            )]),
        ]);
    }

    /**
     * Define a parent relationship for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Factories\Factory|\Illuminate\Database\Eloquent\Model  $factory
     * @param  string|null  $relationship
     * @return static
     */
    public function for($factory, $relationship = null)
    {
        return $this->newInstance(['for' => $this->for->concat([new BelongsToRelationship(
            $factory,
            $relationship ?? Str::camel(class_basename(
                $factory instanceof Factory ? $factory->modelName() : $factory
            ))
        )])]);
    }

    /**
     * Provide model instances to use instead of any nested factory calls when creating relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|array  $model
     * @return static
     */
    public function recycle($model)
    {
        // Group provided models by the type and merge them into existing recycle collection
        return $this->newInstance([
            'recycle' => $this->recycle
                ->flatten()
                ->merge(
                    Collection::wrap($model instanceof Model ? func_get_args() : $model)
                        ->flatten()
                )->groupBy(fn ($model) => get_class($model)),
        ]);
    }

    /**
     * Retrieve a random model of a given type from previously provided models to recycle.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelClassName
     * @return TClass|null
     */
    public function getRandomRecycledModel($modelClassName)
    {
        return $this->recycle->get($modelClassName)?->random();
    }

    /**
     * Add a new "after making" callback to the model definition.
     *
     * @param  \Closure(TModel): mixed  $callback
     * @return static
     */
    public function afterMaking(Closure $callback)
    {
        return $this->newInstance(['afterMaking' => $this->afterMaking->concat([$callback])]);
    }

    /**
     * Add a new "after creating" callback to the model definition.
     *
     * @param  \Closure(TModel, \Illuminate\Database\Eloquent\Model|null): mixed  $callback
     * @return static
     */
    public function afterCreating(Closure $callback)
    {
        return $this->newInstance(['afterCreating' => $this->afterCreating->concat([$callback])]);
    }

    /**
     * Remove the "after making" callbacks from the factory.
     *
     * @return static
     */
    public function withoutAfterMaking()
    {
        return $this->newInstance(['afterMaking' => new Collection]);
    }

    /**
     * Remove the "after creating" callbacks from the factory.
     *
     * @return static
     */
    public function withoutAfterCreating()
    {
        return $this->newInstance(['afterCreating' => new Collection]);
    }

    /**
     * Call the "after making" callbacks for the given model instances.
     *
     * @param  \Illuminate\Support\Collection  $instances
     * @return void
     */
    protected function callAfterMaking(Collection $instances)
    {
        $instances->each(function ($model) {
            $this->afterMaking->each(function ($callback) use ($model) {
                $callback($model);
            });
        });
    }

    /**
     * Call the "after creating" callbacks for the given model instances.
     *
     * @param  \Illuminate\Support\Collection  $instances
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return void
     */
    protected function callAfterCreating(Collection $instances, ?Model $parent = null)
    {
        $instances->each(function ($model) use ($parent) {
            $this->afterCreating->each(function ($callback) use ($model, $parent) {
                $callback($model, $parent);
            });
        });
    }

    /**
     * Specify how many models should be generated.
     *
     * @param  int|null  $count
     * @return static
     */
    public function count(?int $count)
    {
        return $this->newInstance(['count' => $count]);
    }

    /**
     * Indicate that related parent models should not be created.
     *
     * @param  array<string|class-string<Model>>  $parents
     * @return static
     */
    public function withoutParents($parents = [])
    {
        return $this->newInstance(! $parents ? ['expandRelationships' => false] : ['excludeRelationships' => $parents]);
    }

    /**
     * Get the name of the database connection that is used to generate models.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return enum_value($this->connection);
    }

    /**
     * Specify the database connection that should be used to generate models.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return static
     */
    public function connection(UnitEnum|string|null $connection)
    {
        return $this->newInstance(['connection' => $connection]);
    }

    /**
     * Create a new instance of the factory builder with the given mutated properties.
     *
     * @param  array  $arguments
     * @return static
     */
    protected function newInstance(array $arguments = [])
    {
        return new static(...array_values(array_merge([
            'count' => $this->count,
            'states' => $this->states,
            'has' => $this->has,
            'for' => $this->for,
            'afterMaking' => $this->afterMaking,
            'afterCreating' => $this->afterCreating,
            'connection' => $this->connection,
            'recycle' => $this->recycle,
            'expandRelationships' => $this->expandRelationships,
            'excludeRelationships' => $this->excludeRelationships,
        ], $arguments)));
    }

    /**
     * Get a new model instance.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function newModel(array $attributes = [])
    {
        $model = $this->modelName();

        return new $model($attributes);
    }

    /**
     * Get the name of the model that is generated by the factory.
     *
     * @return class-string<TModel>
     */
    public function modelName()
    {
        if ($this->model !== null) {
            return $this->model;
        }

        $resolver = static::$modelNameResolvers[static::class] ?? static::$modelNameResolvers[self::class] ?? static::$modelNameResolver ?? function (self $factory) {
            $namespacedFactoryBasename = Str::replaceLast(
                'Factory', '', Str::replaceFirst(static::$namespace, '', $factory::class)
            );

            $factoryBasename = Str::replaceLast('Factory', '', class_basename($factory));

            $appNamespace = static::appNamespace();

            return class_exists($appNamespace.'Models\\'.$namespacedFactoryBasename)
                ? $appNamespace.'Models\\'.$namespacedFactoryBasename
                : $appNamespace.$factoryBasename;
        };

        return $resolver($this);
    }

    /**
     * Specify the callback that should be invoked to guess model names based on factory names.
     *
     * @param  callable(self): class-string<TModel>  $callback
     * @return void
     */
    public static function guessModelNamesUsing(callable $callback)
    {
        static::$modelNameResolvers[static::class] = $callback;
    }

    /**
     * Specify the default namespace that contains the application's model factories.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function useNamespace(string $namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Get a new factory instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return \Illuminate\Database\Eloquent\Factories\Factory<TClass>
     */
    public static function factoryForModel(string $modelName)
    {
        $factory = static::resolveFactoryName($modelName);

        return $factory::new();
    }

    /**
     * Specify the callback that should be invoked to guess factory names based on dynamic relationship names.
     *
     * @param  callable(class-string<\Illuminate\Database\Eloquent\Model>): class-string<\Illuminate\Database\Eloquent\Factories\Factory>  $callback
     * @return void
     */
    public static function guessFactoryNamesUsing(callable $callback)
    {
        static::$factoryNameResolver = $callback;
    }

    /**
     * Specify that relationships should create parent relationships by default.
     *
     * @return void
     */
    public static function expandRelationshipsByDefault()
    {
        static::$expandRelationshipsByDefault = true;
    }

    /**
     * Specify that relationships should not create parent relationships by default.
     *
     * @return void
     */
    public static function dontExpandRelationshipsByDefault()
    {
        static::$expandRelationshipsByDefault = false;
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator|null
     */
    protected function withFaker()
    {
        if (! class_exists(Generator::class)) {
            return;
        }

        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Get the factory name for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return class-string<\Illuminate\Database\Eloquent\Factories\Factory<TClass>>
     */
    public static function resolveFactoryName(string $modelName)
    {
        $resolver = static::$factoryNameResolver ?? function (string $modelName) {
            $appNamespace = static::appNamespace();

            $modelName = Str::startsWith($modelName, $appNamespace.'Models\\')
                ? Str::after($modelName, $appNamespace.'Models\\')
                : Str::after($modelName, $appNamespace);

            return static::$namespace.$modelName.'Factory';
        };

        return $resolver($modelName);
    }

    /**
     * Get the application namespace for the application.
     *
     * @return string
     */
    protected static function appNamespace()
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }

    /**
     * Flush the factory's global state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$modelNameResolver = null;
        static::$modelNameResolvers = [];
        static::$factoryNameResolver = null;
        static::$namespace = 'Database\\Factories\\';
        static::$expandRelationshipsByDefault = true;
    }

    /**
     * Proxy dynamic factory methods onto their proper methods.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($method === 'trashed' && $this->modelName()::isSoftDeletable()) {
            return $this->state([
                $this->newModel()->getDeletedAtColumn() => $parameters[0] ?? Carbon::now()->subDay(),
            ]);
        }

        if (! Str::startsWith($method, ['for', 'has'])) {
            static::throwBadMethodCallException($method);
        }

        $relationship = Str::camel(Str::substr($method, 3));

        $relatedModel = get_class($this->newModel()->{$relationship}()->getRelated());

        if (method_exists($relatedModel, 'newFactory')) {
            $factory = $relatedModel::newFactory() ?? static::factoryForModel($relatedModel);
        } else {
            $factory = static::factoryForModel($relatedModel);
        }

        if (str_starts_with($method, 'for')) {
            return $this->for($factory->state($parameters[0] ?? []), $relationship);
        } elseif (str_starts_with($method, 'has')) {
            return $this->has(
                $factory
                    ->count(is_numeric($parameters[0] ?? null) ? $parameters[0] : 1)
                    ->state((is_callable($parameters[0] ?? null) || is_array($parameters[0] ?? null)) ? $parameters[0] : ($parameters[1] ?? [])),
                $relationship
            );
        }
    }
}
