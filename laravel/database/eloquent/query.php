<?php namespace Laravel\Database\Eloquent;

use Laravel\Event;
use Laravel\Database;
use Laravel\Database\Eloquent\Relationships\Has_Many_And_Belongs_To;

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
		'delete', 'decrement', 'count', 'min', 'max', 'avg', 'sum',
	);

	/**
	 * Create a new query instance for a model.
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function __construct($model)
	{
		$this->model = ($model instanceof Model) ? $model : new $model;

		$this->table = $this->table();
	}

	/**
	 * Get the first model result for the query.
	 *
	 * @param  array  $columns
	 * @return mixed
	 */
	public function first($columns = array('*'))
	{
		$results = $this->hydrate($this->model, $this->table->take(1)->get($columns));

		return (count($results) > 0) ? head($results) : null;
	}

	/**
	 * Get all of the model results for the query.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public function get($columns = array('*'))
	{
		return $this->hydrate($this->model, $this->table->get($columns));
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
	public function hydrate($model, $results)
	{
		$class = get_class($model);

		$models = array();

		// We'll spin through the array of database results and hydrate a model
		// for each one of the records. We will also set the "exists" flag to
		// "true" so that the model will be updated when it is saved.
		foreach ((array) $results as $result)
		{
			$result = (array) $result;

			$new = new $class(array(), true);

			// We need to set the attributes manually in case the accessible property is
			// set on the array which will prevent the mass assignment of attributes if
			// we were to pass them in using the constructor or fill methods.
			$new->fill_raw($result);

			$models[] = $new;
		}

		if (count($results) > 0)
		{
			foreach ($this->model_includes() as $relationship => $constraints)
			{
				// If the relationship is nested, we will skip loading it here and let
				// the load method parse and set the nested eager loads on the right
				// relationship when it is getting ready to eager load.
				if (str_contains($relationship, '.'))
				{
					continue;
				}

				$this->load($models, $relationship, $constraints);
			}
		}

		// The many to many relationships may have pivot table columns on them
		// so we will call the "clean" method on the relationship to remove
		// any pivot columns that are on the model.
		if ($this instanceof Relationships\Has_Many_And_Belongs_To)
		{
			$this->hydrate_pivot($models);
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
			// the relationship with a dot, then we will strip off the leading
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
		$relationships = array_keys($this->model->includes);
		$implicits = array();

		foreach ($relationships as $relationship)
		{
			$parts = explode('.', $relationship);

			$prefix = '';
			foreach ($parts as $part)
			{
				$implicits[$prefix.$part] = NULL;
				$prefix .= $part.'.';
			}
		}

		// Add all implicit includes to the explicit ones
		return $this->model->includes + $implicits;
	}

	/**
	 * Get a fluent query builder for the model.
	 *
	 * @return Query
	 */
	protected function table()
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
		// builder such as the aggregate methods. If the called method is
		// one of these, we will just return the result straight away.
		if (in_array($method, $this->passthru))
		{
			return $result;
		}

		return $this;
	}

}
