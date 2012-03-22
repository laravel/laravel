<?php namespace Laravel\Database\Eloquent; use Laravel\Database;

class Query {

	/**
	 * The model instance being queried.
	 *
	 * @var Model
	 */
	public $model;

	/**
	 * The fluent query builder for the query instance.
	 *
	 * @var Query
	 */
	public $table;

	/**
	 * The relationships that should be eagerly loaded by the query.
	 *
	 * @var array
	 */
	public $includes = array();

	/**
	 * The methods that should be returned from the fluent query builder.
	 *
	 * @var array
	 */
	public $passthru = array(
		'lists', 'only', 'insert', 'insert_get_id', 'update', 'increment',
		'decrement', 'count', 'min', 'max', 'avg', 'sum',
	);

	/**
	 * Creat a new query instance for a model.
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function __construct($model)
	{
		$this->model = ($model instanceof Model) ? $model : new $model;

		$this->table = $this->query();
	}

	/**
	 * Get the first model result for the query.
	 *
	 * @param  array  $columns
	 * @return mixed
	 */
	public function first($columns = array('*'))
	{
		$results = $this->hydrate($this->model, $this->table->take(1)->get($columns, false));

		return (count($results) > 0) ? head($results) : null;
	}

	/**
	 * Get all of the model results for the query.
	 *
	 * @param  array  $columns
	 * @param  bool   $include
	 * @return array
	 */
	public function get($columns = array('*'), $include = true)
	{
		return $this->hydrate($this->model, $this->table->get($columns), $include);
	}

	/**
	 * Get an array of paginated model results.
	 *
	 * @param  int        $per_page
	 * @param  array      $columns
	 * @return Paginator
	 */
	public function paginate($per_page = null, $columns = array('*'))
	{
		$per_page = $per_page ?: $this->model->per_page();

		// First we'll grab the Paginator instance and get the results. Then we can
		// feed those raw database results into the hydrate method to get models
		// for the results, which we'll set on the paginator and return it.
		$paginator = $this->table->paginate($per_page, $columns);

		$paginator->results = $this->hydrate($this->model, $paginator->results);

		return $paginator;
	}

	/**
	 * Hydrate an array of models from the given results.
	 *
	 * @param  Model  $model
	 * @param  array  $results
	 * @return array
	 */
	public function hydrate($model, $results, $include = true)
	{
		$class = get_class($model);

		$models = array();

		// We'll spin through the array of database results and hydrate a model
		// for each one of the records. We will also set the "exists" flag to
		// "true" so that the model will be updated when it is saved.
		foreach ((array) $results as $result)
		{
			$result = (array) $result;

			$models[$result[$this->model->key()]] = new $class($result, true);
		}

		if ($include and count($results) > 0)
		{
			foreach ($this->model_includes() as $relationship => $constraints)
			{
				// If the relationship is nested, we will skip laoding it here and let
				// the load method parse and set the nested eager loads on the right
				// relationship when it is getting ready to eager laod.
				if (str_contains($relationship, '.'))
				{
					continue;
				}

				$this->load($models, $relationship, $constraints);
			}
		}

		// The many to many relationships may have pivot table column on them
		// so we will call the "clean" method on the relationship to remove
		// any pivot columns that are on the model.
		if ($this instanceof Relationships\Has_Many_And_Belongs_To)
		{
			$this->pivot($models);
		}

		return $models;
	}

	/**
	 * Hydrate an eagerly loaded relationship on the model results.
	 *
	 * @param  array       $results
	 * @param  string      $relationship
	 * @param  array|null  $constraints
	 * @return void
	 */
	protected function load(&$results, $relationship, $constraints)
	{
		$query = $this->model->$relationship();

		$query->model->includes = $this->nested_includes($relationship);

		// We'll remove any of the where clauses from the relationship to give
		// the relationship the opportunity to set the constraints for an
		// eager relationship using a separate, specific method.
		$query->table->reset_where();

		$query->eagerly_constrain($results);

		// Constraints may be specified in-line for the eager load by passing
		// a Closure as the value portion of the eager load. We can use the
		// query builder's nested query support to add the constraints.
		if ( ! is_null($constraints))
		{
			$query->table->where_nested($constraints);
		}

		// Before matching the models, we will initialize the relationship
		// to either null for single-value relationships or an array for
		// the multi-value relationships as their baseline value.
		$query->initialize($results, $relationship);

		$query->match($relationship, $results, $query->get());
	}

	/**
	 * Gather the nested includes for a given relationship.
	 *
	 * @param  string  $relationship
	 * @return array
	 */
	protected function nested_includes($relationship)
	{
		$nested = array();

		foreach ($this->model_includes() as $include => $constraints)
		{
			// To get the nested includes, we want to find any includes that begin
			// the relationship and a dot, then we will strip off the leading
			// nesting indicator and set the include in the array.
			if (starts_with($include, $relationship.'.'))
			{
				$nested[substr($include, strlen($relationship.'.'))] = $constraints;
			}
		}

		return $nested;
	}

	/**
	 * Get the eagerly loaded relationships for the model.
	 *
	 * @return array
	 */
	protected function model_includes()
	{
		$includes = array();

		foreach ($this->model->includes as $relationship => $constraints)
		{
			// When eager loading relationships, constraints may be set on the eager
			// load definition; however, is none are set, we need to swap the key
			// and the value of the array since there are no constraints.
			if (is_numeric($relationship))
			{
				list($relationship, $constraints) = array($constraints, null);
			}

			$includes[$relationship] = $constraints;
		}

		return $includes;
	}

	/**
	 * Get a fluent query builder for the model.
	 *
	 * @return Query
	 */
	protected function query()
	{
		return $this->connection()->table($this->model->table());
	}

	/**
	 * Get the database connection for the model.
	 *
	 * @return Connection
	 */
	public function connection()
	{
		return Database::connection($this->model->connection());
	}

	/**
	 * Handle dynamic method calls to the query.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$result = call_user_func_array(array($this->table, $method), $parameters);

		// Some methods may get their results straight from the fluent query
		// builder, such as the aggregate methods. If the called method is
		// one of these, we will return the result straight away.
		if (in_array($method, $this->passthru))
		{
			return $result;
		}

		return $this;
	}

}