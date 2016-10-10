<?php namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BelongsToMany extends Relation {

	/**
	 * The intermediate table for the relation.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The foreign key of the parent model.
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * The associated key of the relation.
	 *
	 * @var string
	 */
	protected $otherKey;

	/**
	 * The "name" of the relationship.
	 *
	 * @var string
	 */
	protected $relationName;

	/**
	 * The pivot table columns to retrieve.
	 *
	 * @var array
	 */
	protected $pivotColumns = array();

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Model  $parent
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relationName
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $table, $foreignKey, $otherKey, $relationName = null)
	{
		$this->table = $table;
		$this->otherKey = $otherKey;
		$this->foreignKey = $foreignKey;
		$this->relationName = $relationName;

		parent::__construct($query, $parent);
	}

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	public function getResults()
	{
		return $this->get();
	}

	/**
	 * Set a where clause for a pivot table column.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $boolean
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function wherePivot($column, $operator = null, $value = null, $boolean = 'and')
	{
		return $this->where($this->table.'.'.$column, $operator, $value, $boolean);
	}

	/**
	 * Set an or where clause for a pivot table column.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $boolean
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function orWherePivot($column, $operator = null, $value = null)
	{
		return $this->wherePivot($column, $operator, $value, 'or');
	}

	/**
	 * Execute the query and get the first result.
	 *
	 * @param  array   $columns
	 * @return mixed
	 */
	public function first($columns = array('*'))
	{
		$results = $this->take(1)->get($columns);

		return count($results) > 0 ? $results->first() : null;
	}

	/**
	 * Execute the query and get the first result or throw an exception.
	 *
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Model|static
	 *
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public function firstOrFail($columns = array('*'))
	{
		if ( ! is_null($model = $this->first($columns))) return $model;

		throw new ModelNotFoundException;
	}

	/**
	 * Execute the query as a "select" statement.
	 *
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function get($columns = array('*'))
	{
		// First we'll add the proper select columns onto the query so it is run with
		// the proper columns. Then, we will get the results and hydrate out pivot
		// models with the result of those columns as a separate model relation.
		$select = $this->getSelectColumns($columns);

		$models = $this->query->addSelect($select)->getModels();

		$this->hydratePivotRelation($models);

		// If we actually found models we will also eager load any relationships that
		// have been specified as needing to be eager loaded. This will solve the
		// n + 1 query problem for the developer and also increase performance.
		if (count($models) > 0)
		{
			$models = $this->query->eagerLoadRelations($models);
		}

		return $this->related->newCollection($models);
	}

	/**
	 * Get a paginator for the "select" statement.
	 *
	 * @param  int    $perPage
	 * @param  array  $columns
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function paginate($perPage = null, $columns = array('*'))
	{
		$this->query->addSelect($this->getSelectColumns($columns));

		// When paginating results, we need to add the pivot columns to the query and
		// then hydrate into the pivot objects once the results have been gathered
		// from the database since this isn't performed by the Eloquent builder.
		$pager = $this->query->paginate($perPage, $columns);

		$this->hydratePivotRelation($pager->getItems());

		return $pager;
	}

	/**
	 * Hydrate the pivot table relationship on the models.
	 *
	 * @param  array  $models
	 * @return void
	 */
	protected function hydratePivotRelation(array $models)
	{
		// To hydrate the pivot relationship, we will just gather the pivot attributes
		// and create a new Pivot model, which is basically a dynamic model that we
		// will set the attributes, table, and connections on so it they be used.
		foreach ($models as $model)
		{
			$pivot = $this->newExistingPivot($this->cleanPivotAttributes($model));

			$model->setRelation('pivot', $pivot);
		}
	}

	/**
	 * Get the pivot attributes from a model.
	 *
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return array
	 */
	protected function cleanPivotAttributes(Model $model)
	{
		$values = array();

		foreach ($model->getAttributes() as $key => $value)
		{
			// To get the pivots attributes we will just take any of the attributes which
			// begin with "pivot_" and add those to this arrays, as well as unsetting
			// them from the parent's models since they exist in a different table.
			if (strpos($key, 'pivot_') === 0)
			{
				$values[substr($key, 6)] = $value;

				unset($model->$key);
			}
		}

		return $values;
	}

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	public function addConstraints()
	{
		$this->setJoin();

		if (static::$constraints) $this->setWhere();
	}

	/**
	 * Add the constraints for a relationship count query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Builder  $parent
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function getRelationCountQuery(Builder $query, Builder $parent)
	{
		if ($parent->getQuery()->from == $query->getQuery()->from)
		{
			return $this->getRelationCountQueryForSelfJoin($query, $parent);
		}
		else
		{
			$this->setJoin($query);

			return parent::getRelationCountQuery($query, $parent);
		}
	}

	/**
	 * Add the constraints for a relationship count query on the same table.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Builder  $parent
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function getRelationCountQueryForSelfJoin(Builder $query, Builder $parent)
	{
		$query->select(new \Illuminate\Database\Query\Expression('count(*)'));

		$tablePrefix = $this->query->getQuery()->getConnection()->getTablePrefix();

		$query->from($this->table.' as '.$tablePrefix.$hash = $this->getRelationCountHash());

		$key = $this->wrap($this->getQualifiedParentKeyName());

		return $query->where($hash.'.'.$this->foreignKey, '=', new \Illuminate\Database\Query\Expression($key));
	}

	/**
	 * Get a relationship join table hash.
	 *
	 * @return string
	 */
	public function getRelationCountHash()
	{
		return 'self_'.md5(microtime(true));
	}

	/**
	 * Set the select clause for the relation query.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	protected function getSelectColumns(array $columns = array('*'))
	{
		if ($columns == array('*'))
		{
			$columns = array($this->related->getTable().'.*');
		}

		return array_merge($columns, $this->getAliasedPivotColumns());
	}

	/**
	 * Get the pivot columns for the relation.
	 *
	 * @return array
	 */
	protected function getAliasedPivotColumns()
	{
		$defaults = array($this->foreignKey, $this->otherKey);

		// We need to alias all of the pivot columns with the "pivot_" prefix so we
		// can easily extract them out of the models and put them into the pivot
		// relationships when they are retrieved and hydrated into the models.
		$columns = array();

		foreach (array_merge($defaults, $this->pivotColumns) as $column)
		{
			$columns[] = $this->table.'.'.$column.' as pivot_'.$column;
		}

		return array_unique($columns);
	}

	/**
	 * Set the join clause for the relation query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder|null
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	protected function setJoin($query = null)
	{
		$query = $query ?: $this->query;

		// We need to join to the intermediate table on the related model's primary
		// key column with the intermediate table's foreign key for the related
		// model instance. Then we can set the "where" for the parent models.
		$baseTable = $this->related->getTable();

		$key = $baseTable.'.'.$this->related->getKeyName();

		$query->join($this->table, $key, '=', $this->getOtherKey());

		return $this;
	}

	/**
	 * Set the where clause for the relation query.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	protected function setWhere()
	{
		$foreign = $this->getForeignKey();

		$this->query->where($foreign, '=', $this->parent->getKey());

		return $this;
	}

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		$this->query->whereIn($this->getForeignKey(), $this->getKeys($models));
	}

	/**
	 * Initialize the relation on a set of models.
	 *
	 * @param  array   $models
	 * @param  string  $relation
	 * @return array
	 */
	public function initRelation(array $models, $relation)
	{
		foreach ($models as $model)
		{
			$model->setRelation($relation, $this->related->newCollection());
		}

		return $models;
	}

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $models
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	public function match(array $models, Collection $results, $relation)
	{
		$dictionary = $this->buildDictionary($results);

		// Once we have an array dictionary of child objects we can easily match the
		// children back to their parent using the dictionary and the keys on the
		// the parent models. Then we will return the hydrated models back out.
		foreach ($models as $model)
		{
			if (isset($dictionary[$key = $model->getKey()]))
			{
				$collection = $this->related->newCollection($dictionary[$key]);

				$model->setRelation($relation, $collection);
			}
		}

		return $models;
	}

	/**
	 * Build model dictionary keyed by the relation's foreign key.
	 *
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @return array
	 */
	protected function buildDictionary(Collection $results)
	{
		$foreign = $this->foreignKey;

		// First we will build a dictionary of child models keyed by the foreign key
		// of the relation so that we will easily and quickly match them to their
		// parents without having a possibly slow inner loops for every models.
		$dictionary = array();

		foreach ($results as $result)
		{
			$dictionary[$result->pivot->$foreign][] = $result;
		}

		return $dictionary;
	}

	/**
	 * Touch all of the related models for the relationship.
	 *
	 * E.g.: Touch all roles associated with this user.
	 *
	 * @return void
	 */
	public function touch()
	{
		$key = $this->getRelated()->getKeyName();

		$columns = $this->getRelatedFreshUpdate();

		// If we actually have IDs for the relation, we will run the query to update all
		// the related model's timestamps, to make sure these all reflect the changes
		// to the parent models. This will help us keep any caching synced up here.
		$ids = $this->getRelatedIds();

		if (count($ids) > 0)
		{
			$this->getRelated()->newQuery()->whereIn($key, $ids)->update($columns);
		}
	}

	/**
	 * Get all of the IDs for the related models.
	 *
	 * @return array
	 */
	public function getRelatedIds()
	{
		$related = $this->getRelated();

		$fullKey = $related->getQualifiedKeyName();

		return $this->getQuery()->select($fullKey)->lists($related->getKeyName());
	}

	/**
	 * Save a new model and attach it to the parent model.
	 *
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @param  array  $joining
	 * @param  bool   $touch
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function save(Model $model, array $joining = array(), $touch = true)
	{
		$model->save(array('touch' => false));

		$this->attach($model->getKey(), $joining, $touch);

		return $model;
	}

	/**
	 * Save an array of new models and attach them to the parent model.
	 *
	 * @param  array  $models
	 * @param  array  $joinings
	 * @return array
	 */
	public function saveMany(array $models, array $joinings = array())
	{
		foreach ($models as $key => $model)
		{
			$this->save($model, (array) array_get($joinings, $key), false);
		}

		$this->touchIfTouching();

		return $models;
	}

	/**
	 * Create a new instance of the related model.
	 *
	 * @param  array  $attributes
	 * @param  array  $joining
	 * @param  bool   $touch
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $attributes, array $joining = array(), $touch = true)
	{
		$instance = $this->related->newInstance($attributes);

		// Once we save the related model, we need to attach it to the base model via
		// through intermediate table so we'll use the existing "attach" method to
		// accomplish this which will insert the record and any more attributes.
		$instance->save(array('touch' => false));

		$this->attach($instance->getKey(), $joining, $touch);

		return $instance;
	}

	/**
	 * Create an array of new instances of the related models.
	 *
	 * @param  array  $records
	 * @param  array  $joinings
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createMany(array $records, array $joinings = array())
	{
		$instances = array();

		foreach ($records as $key => $record)
		{
			$instances[] = $this->create($record, (array) array_get($joinings, $key), false);
		}

		$this->touchIfTouching();

		return $instances;
	}

	/**
	 * Sync the intermediate tables with a list of IDs or collection of models.
	 *
	 * @param  $ids
	 * @param  bool   $detaching
	 * @return array
	 */
	public function sync($ids, $detaching = true)
	{
		$changes = array(
			'attached' => array(), 'detached' => array(), 'updated' => array()
		);

		if ($ids instanceof Collection) $ids = $ids->modelKeys();

		// First we need to attach any of the associated models that are not currently
		// in this joining table. We'll spin through the given IDs, checking to see
		// if they exist in the array of current ones, and if not we will insert.
		$current = $this->newPivotQuery()->lists($this->otherKey);

		$records = $this->formatSyncList($ids);

		$detach = array_diff($current, array_keys($records));

		// Next, we will take the differences of the currents and given IDs and detach
		// all of the entities that exist in the "current" array but are not in the
		// the array of the IDs given to the method which will complete the sync.
		if ($detaching && count($detach) > 0)
		{
			$this->detach($detach);

			$changes['detached'] = (array) array_map('intval', $detach);
		}

		// Now we are finally ready to attach the new records. Note that we'll disable
		// touching until after the entire operation is complete so we don't fire a
		// ton of touch operations until we are totally done syncing the records.
		$changes = array_merge(
			$changes, $this->attachNew($records, $current, false)
		);

		if (count($changes['attached']) || count($changes['updated']))
		{
			$this->touchIfTouching();
		}

		return $changes;
	}

	/**
	 * Format the sync list so that it is keyed by ID.
	 *
	 * @param  array  $records
	 * @return array
	 */
	protected function formatSyncList(array $records)
	{
		$results = array();

		foreach ($records as $id => $attributes)
		{
			if ( ! is_array($attributes))
			{
				list($id, $attributes) = array($attributes, array());
			}

			$results[$id] = $attributes;
		}

		return $results;
	}

	/**
	 * Attach all of the IDs that aren't in the current array.
	 *
	 * @param  array  $records
	 * @param  array  $current
	 * @param  bool   $touch
	 * @return array
	 */
	protected function attachNew(array $records, array $current, $touch = true)
	{
		$changes = array('attached' => array(), 'updated' => array());

		foreach ($records as $id => $attributes)
		{
			// If the ID is not in the list of existing pivot IDs, we will insert a new pivot
			// record, otherwise, we will just update this existing record on this joining
			// table, so that the developers will easily update these records pain free.
			if ( ! in_array($id, $current))
			{
				$this->attach($id, $attributes, $touch);

				$changes['attached'][] = (int) $id;
			}

			// Now we'll try to update an existing pivot record with the attributes that were
			// given to the method. If the model is actually updated we will add it to the
			// list of updated pivot records so we return them back out to the consumer.
			elseif (count($attributes) > 0)
			{
				if ($this->updateExistingPivot($id, $attributes, $touch))
				{
					$changes['updated'][] = (int) $id;
				}
			}
		}
		return $changes;
	}

	/**
	 * Update an existing pivot record on the table.
	 *
	 * @param  mixed  $id
	 * @param  array  $attributes
	 * @param  bool   $touch
	 * @return void
	 */
	public function updateExistingPivot($id, array $attributes, $touch)
	{
		if (in_array($this->updatedAt(), $this->pivotColumns))
		{
			$attributes = $this->setTimestampsOnAttach($attributes, true);
		}

		$updated = $this->newPivotStatementForId($id)->update($attributes);

		if ($touch) $this->touchIfTouching();

		return $updated;
	}

	/**
	 * Attach a model to the parent.
	 *
	 * @param  mixed  $id
	 * @param  array  $attributes
	 * @param  bool   $touch
	 * @return void
	 */
	public function attach($id, array $attributes = array(), $touch = true)
	{
		if ($id instanceof Model) $id = $id->getKey();

		$query = $this->newPivotStatement();

		$query->insert($this->createAttachRecords((array) $id, $attributes));

		if ($touch) $this->touchIfTouching();
	}

	/**
	 * Create an array of records to insert into the pivot table.
	 *
	 * @param  array  $ids
	 * @return array
	 */
	protected function createAttachRecords($ids, array $attributes)
	{
		$records = array();

		$timed = in_array($this->createdAt(), $this->pivotColumns);

		// To create the attachment records, we will simply spin through the IDs given
		// and create a new record to insert for each ID. Each ID may actually be a
		// key in the array, with extra attributes to be placed in other columns.
		foreach ($ids as $key => $value)
		{
			$records[] = $this->attacher($key, $value, $attributes, $timed);
		}

		return $records;
	}

	/**
	 * Create a full attachment record payload.
	 *
	 * @param  int    $key
	 * @param  mixed  $value
	 * @param  array  $attributes
	 * @param  bool   $timed
	 * @return array
	 */
	protected function attacher($key, $value, $attributes, $timed)
	{
		list($id, $extra) = $this->getAttachId($key, $value, $attributes);

		// To create the attachment records, we will simply spin through the IDs given
		// and create a new record to insert for each ID. Each ID may actually be a
		// key in the array, with extra attributes to be placed in other columns.
		$record = $this->createAttachRecord($id, $timed);

		return array_merge($record, $extra);
	}

	/**
	 * Get the attach record ID and extra attributes.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * @param  array  $attributes
	 * @return array
	 */
	protected function getAttachId($key, $value, array $attributes)
	{
		if (is_array($value))
		{
			return array($key, array_merge($value, $attributes));
		}
		else
		{
			return array($value, $attributes);
		}
	}

	/**
	 * Create a new pivot attachment record.
	 *
	 * @param  int   $id
	 * @param  bool  $timed
	 * @return array
	 */
	protected function createAttachRecord($id, $timed)
	{
		$record[$this->foreignKey] = $this->parent->getKey();

		$record[$this->otherKey] = $id;

		// If the record needs to have creation and update timestamps, we will make
		// them by calling the parent model's "freshTimestamp" method which will
		// provide us with a fresh timestamp in this model's preferred format.
		if ($timed)
		{
			$record = $this->setTimestampsOnAttach($record);
		}

		return $record;
	}

	/**
	 * Set the creation and update timestamps on an attach record.
	 *
	 * @param  array  $record
	 * @param  bool   $exists
	 * @return array
	 */
	protected function setTimestampsOnAttach(array $record, $exists = false)
	{
		$fresh = $this->parent->freshTimestamp();

		if ( ! $exists) $record[$this->createdAt()] = $fresh;

		$record[$this->updatedAt()] = $fresh;

		return $record;
	}

	/**
	 * Detach models from the relationship.
	 *
	 * @param  int|array  $ids
	 * @param  bool  $touch
	 * @return int
	 */
	public function detach($ids = array(), $touch = true)
	{
		if ($ids instanceof Model) $ids = (array) $ids->getKey();

		$query = $this->newPivotQuery();

		// If associated IDs were passed to the method we will only delete those
		// associations, otherwise all of the association ties will be broken.
		// We'll return the numbers of affected rows when we do the deletes.
		$ids = (array) $ids;

		if (count($ids) > 0)
		{
			$query->whereIn($this->otherKey, $ids);
		}

		if ($touch) $this->touchIfTouching();

		// Once we have all of the conditions set on the statement, we are ready
		// to run the delete on the pivot table. Then, if the touch parameter
		// is true, we will go ahead and touch all related models to sync.
		$results = $query->delete();

		return $results;
	}

	/**
	 * If we're touching the parent model, touch.
	 *
	 * @return void
	 */
	public function touchIfTouching()
	{
		if ($this->touchingParent()) $this->getParent()->touch();

		if ($this->getParent()->touches($this->relationName)) $this->touch();
	}

	/**
	 * Determine if we should touch the parent on sync.
	 *
	 * @return bool
	 */
	protected function touchingParent()
	{
		return $this->getRelated()->touches($this->guessInverseRelation());
	}

	/**
	 * Attempt to guess the name of the inverse of the relation.
	 *
	 * @return string
	 */
	protected function guessInverseRelation()
	{
		return camel_case(str_plural(class_basename($this->getParent())));
	}

	/**
	 * Create a new query builder for the pivot table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function newPivotQuery()
	{
		$query = $this->newPivotStatement();

		return $query->where($this->foreignKey, $this->parent->getKey());
	}

	/**
	 * Get a new plain query builder for the pivot table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function newPivotStatement()
	{
		return $this->query->getQuery()->newQuery()->from($this->table);
	}

	/**
	 * Get a new pivot statement for a given "other" ID.
	 *
	 * @param  mixed  $id
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function newPivotStatementForId($id)
	{
		$pivot = $this->newPivotStatement();

		$key = $this->parent->getKey();

		return $pivot->where($this->foreignKey, $key)->where($this->otherKey, $id);
	}

	/**
	 * Create a new pivot model instance.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return \Illuminate\Database\Eloquent\Relations\Pivot
	 */
	public function newPivot(array $attributes = array(), $exists = false)
	{
		$pivot = $this->related->newPivot($this->parent, $attributes, $this->table, $exists);

		return $pivot->setPivotKeys($this->foreignKey, $this->otherKey);
	}

	/**
	 * Create a new existing pivot model instance.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Relations\Pivot
	 */
	public function newExistingPivot(array $attributes = array())
	{
		return $this->newPivot($attributes, true);
	}

	/**
	 * Set the columns on the pivot table to retrieve.
	 *
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function withPivot($columns)
	{
		$columns = is_array($columns) ? $columns : func_get_args();

		$this->pivotColumns = array_merge($this->pivotColumns, $columns);

		return $this;
	}

	/**
	 * Specify that the pivot table has creation and update timestamps.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function withTimestamps($createdAt = null, $updatedAt = null)
	{
		return $this->withPivot($createdAt ?: $this->createdAt(), $updatedAt ?: $this->updatedAt());
	}

	/**
	 * Get the related model's updated at column name.
	 *
	 * @return string
	 */
	public function getRelatedFreshUpdate()
	{
		return array($this->related->getUpdatedAtColumn() => $this->related->freshTimestamp());
	}

	/**
	 * Get the key for comparing against the parent key in "has" query.
	 *
	 * @return string
	 */
	public function getHasCompareKey()
	{
		return $this->getForeignKey();
	}

	/**
	 * Get the fully qualified foreign key for the relation.
	 *
	 * @return string
	 */
	public function getForeignKey()
	{
		return $this->table.'.'.$this->foreignKey;
	}

	/**
	 * Get the fully qualified "other key" for the relation.
	 *
	 * @return string
	 */
	public function getOtherKey()
	{
		return $this->table.'.'.$this->otherKey;
	}

	/**
	 * Get the fully qualified parent key name.
	 *
	 * @return string
	 */
	protected function getQualifiedParentKeyName()
	{
		return $this->parent->getQualifiedKeyName();
	}

	/**
	 * Get the intermediate table for the relationship.
	 *
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Get the relationship name for the relationship.
	 *
	 * @return string
	 */
	public function getRelationName()
	{
		return $this->relationName;
	}

}
