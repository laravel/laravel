<?php namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class MorphTo extends BelongsTo {

	/**
	 * The type of the polymorphic relation.
	 *
	 * @var string
	 */
	protected $morphType;

	/**
	 * The models whose relations are being eager loaded.
	 *
	 * @var \Illuminate\Database\Eloquent\Collection
	 */
	protected $models;

	/**
	 * All of the models keyed by ID.
	 *
	 * @var array
	 */
	protected $dictionary = array();

	/**
	 * Create a new belongs to relationship instance.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Model  $parent
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $type
	 * @param  string  $relation
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey, $type, $relation)
	{
		$this->morphType = $type;

		parent::__construct($query, $parent, $foreignKey, $otherKey, $relation);
	}

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		$this->buildDictionary($this->models = Collection::make($models));
	}

	/**
	 * Build a dictionary with the models.
	 *
	 * @param  \Illuminate\Database\Eloquent\Models  $models
	 * @return void
	 */
	protected function buildDictionary(Collection $models)
	{
		foreach ($models as $model)
		{
			if ($model->{$this->morphType})
			{
				$this->dictionary[$model->{$this->morphType}][$model->{$this->foreignKey}][] = $model;
			}
		}
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
		return $models;
	}

	/**
	 * Associate the model instance to the given parent.
	 *
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function associate(Model $model)
	{
		$this->parent->setAttribute($this->foreignKey, $model->getKey());

		$this->parent->setAttribute($this->morphType, get_class($model));

		return $this->parent->setRelation($this->relation, $model);
	}

	/**
	 * Get the results of the relationship.
	 *
	 * Called via eager load method of Eloquent query builder.
	 *
	 * @return mixed
	 */
	public function getEager()
	{
		foreach (array_keys($this->dictionary) as $type)
		{
			$this->matchToMorphParents($type, $this->getResultsByType($type));
		}

		return $this->models;
	}

	/**
	 * Match the results for a given type to their parents.
	 *
	 * @param  string  $type
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @return void
	 */
	protected function matchToMorphParents($type, Collection $results)
	{
		foreach ($results as $result)
		{
			if (isset($this->dictionary[$type][$result->getKey()]))
			{
				foreach ($this->dictionary[$type][$result->getKey()] as $model)
				{
					$model->setRelation($this->relation, $result);
				}
			}
		}
	}

	/**
	 * Get all of the relation results for a type.
	 *
	 * @param  string  $type
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function getResultsByType($type)
	{
		$instance = $this->createModelByType($type);

		$key = $instance->getKeyName();

		return $instance->whereIn($key, $this->gatherKeysByType($type)->all())->get();
	}

	/**
	 * Gather all of the foreign keys for a given type.
	 *
	 * @param  string  $type
	 * @return array
	 */
	protected function gatherKeysByType($type)
	{
		$foreign = $this->foreignKey;

		return BaseCollection::make($this->dictionary[$type])->map(function($models) use ($foreign)
		{
			return head($models)->{$foreign};

		})->unique();
	}

	/**
	 * Create a new model instance by type.
	 *
	 * @param  string  $type
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModelByType($type)
	{
		return new $type;
	}

	/**
	 * Get the dictionary used by the relationship.
	 *
	 * @return array
	 */
	public function getDictionary()
	{
		return $this->dictionary;
	}

}
