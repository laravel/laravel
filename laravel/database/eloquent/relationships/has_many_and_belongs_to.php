<?php namespace Laravel\Database\Eloquent\Relationships;

use Laravel\Str;
use Laravel\Database\Eloquent\Model;
use Laravel\Database\Eloquent\Pivot;

class Has_Many_And_Belongs_To extends Relationship {

	/**
	 * The name of the intermediate, joining table.
	 *
	 * @var string
	 */
	protected $joining;

	/**
	 * The other or "associated" key. This is the foreign key of the related model.
	 *
	 * @var string
	 */
	protected $other;

	/**
	 * The columns on the joining tbale that should be fetched.
	 *
	 * @var array
	 */
	protected $with = array('id', 'created_at', 'updated_at');

	/**
	 * Create a new many to many relationship instance.
	 *
	 * @param  Model   $model
	 * @param  string  $associated
	 * @param  string  $table
	 * @param  string  $foreign
	 * @param  string  $other
	 * @return void
	 */
	public function __construct($model, $associated, $table, $foreign, $other)
	{
		$this->other = $other;

		$this->joining = $table ?: $this->joining($model, $associated);

		parent::__construct($model, $associated, $foreign);
	}

	/**
	 * Determine the joining table name for the relationship.
	 *
	 * By default, the name is the models sorted and joined with underscores.
	 *
	 * @return string
	 */
	protected function joining($model, $associated)
	{
		$models = array(class_basename($model), class_basename($associated));

		sort($models);

		return strtolower($models[0].'_'.$models[1]);
	}

	/**
	 * Get the properly hydrated results for the relationship.
	 *
	 * @return array
	 */
	public function results()
	{
		return parent::get();
	}

	/**
	 * Insert a new record into the joining table of the association.
	 *
	 * @param  int    $id
	 * @param  array  $joining
	 * @return bool
	 */
	public function attach($id, $attributes = array())
	{
		$joining = array_merge($this->join_record($id), $attributes);

		return $this->insert_joining($joining);
	}

	/**
	 * Insert a new record for the association.
	 *
	 * @param  Model|array  $attributes
	 * @param  array        $joining
	 * @return bool
	 */
	public function insert($attributes, $joining = array())
	{
		// If the attributes are actually an instance of a model, we'll just grab the
		// array of attributes off of the model for saving, allowing the developer
		// to easily validate the joining models before inserting them.
		if ($attributes instanceof Model)
		{
			$attributes = $attributes->attributes;
		}

		$model = $this->model->create($attributes);

		// If the insert was successful, we'll insert a record into the joining table
		// using the new ID that was just inserted into the related table, allowing
		// the developer to not worry about maintaining the join table.
		if ($model instanceof Model)
		{
			$joining = array_merge($this->join_record($model->get_key()), $joining);

			$result = $this->insert_joining($joining);
		}

		return $model instanceof Model and $result;
	}

	/**
	 * Delete all of the records from the joining table for the model.
	 *
	 * @return int
	 */
	public function delete()
	{
		$id = $this->base->get_key();

		return $this->joining_table()->where($this->foreign_key(), '=', $id)->delete();
	}

	/**
	 * Create an array representing a new joining record for the association.
	 *
	 * @param  int    $id
	 * @return array
	 */
	protected function join_record($id)
	{
		return array($this->foreign_key() => $this->base->get_key(), $this->other_key() => $id);
	}

	/**
	 * Insert a new record into the joining table of the association.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	protected function insert_joining($attributes)
	{
		$attributes['created_at'] = $this->model->get_timestamp();

		$attributes['updated_at'] = $attributes['created_at'];

		return $this->joining_table()->insert($attributes);
	}

	/**
	 * Get a fluent query for the joining table of the relationship.
	 *
	 * @return Query
	 */
	protected function joining_table()
	{
		return $this->connection()->table($this->joining);
	}

	/**
	 * Set the proper constraints on the relationship table.
	 *
	 * @return void
	 */
	protected function constrain()
	{
		$other = $this->other_key();

		$foreign = $this->foreign_key();

		$this->set_select($foreign, $other)->set_join($other)->set_where($foreign);
	}

	/**
	 * Set the SELECT clause on the query builder for the relationship.
	 *
	 * @param  string  $foreign
	 * @param  string  $other
	 * @return void
	 */
	protected function set_select($foreign, $other)
	{
		$columns = array($this->model->table().'.*');

		$this->with = array_merge($this->with, array($foreign, $other));

		// Since pivot tables may have extra information on them that the developer
		// needs, we allow an extra array of columns to be specified that will be
		// fetched from the pivot table and hydrate into the pivot model.
		foreach ($this->with as $column)
		{
			$columns[] = $this->joining.'.'.$column.' as pivot_'.$column;
		}

		$this->table->select($columns);

		return $this;
	}

	/**
	 * Set the JOIN clause on the query builder for the relationship.
	 *
	 * @param  string  $other
	 * @return void
	 */
	protected function set_join($other)
	{
		$this->table->join($this->joining, $this->associated_key(), '=', $this->joining.'.'.$other);

		return $this;
	}

	/**
	 * Set the WHERE clause on the query builder for the relationship.
	 *
	 * @param  string  $foreign
	 * @return void
	 */
	protected function set_where($foreign)
	{
		$this->table->where($this->joining.'.'.$foreign, '=', $this->base->get_key());

		return $this;
	}

	/**
	 * Initialize a relationship on an array of parent models.
	 *
	 * @param  array   $parents
	 * @param  string  $relationship
	 * @return void
	 */
	public function initialize(&$parents, $relationship)
	{
		foreach ($parents as &$parent)
		{
			$parent->relationships[$relationship] = array();
		}
	}

	/**
	 * Set the proper constraints on the relationship table for an eager load.
	 *
	 * @param  array  $results
	 * @return void
	 */
	public function eagerly_constrain($results)
	{
		$this->table->where_in($this->joining.'.'.$this->foreign_key(), array_keys($results));
	}

	/**
	 * Match eagerly loaded child models to their parent models.
	 *
	 * @param  array  $parents
	 * @param  array  $children
	 * @return void
	 */
	public function match($relationship, &$parents, $children)
	{
		$foreign = $this->foreign_key();

		foreach ($children as $key => $child)
		{
			$parents[$child->pivot->$foreign]->relationships[$relationship][$child->{$child->key()}] = $child;
		}
	}

	/**
	 * Hydrate the Pivot model on an array of results.
	 *
	 * @param  array  $results
	 * @return void
	 */
	protected function hydrate_pivot(&$results)
	{
		foreach ($results as &$result)
		{
			// Every model result for a many-to-many relationship needs a Pivot instance
			// to represent the pivot table's columns. Sometimes extra columns are on
			// the pivot table that may need to be accessed by the developer.
			$pivot = new Pivot($this->joining);

			$attributes = array_filter($result->attributes, function($attribute)
			{
				return starts_with($attribute, 'pivot_');
			});

			// If the attribute key starts with "pivot_", we know this is a column on
			// the pivot table, so we will move it to the Pivot model and purge it
			// from the model since it actually belongs to the pivot.
			foreach ($attributes as $key => $value)
			{
				$pivot->{substr($key, 6)} = $value;

				$result->purge($key);
			}

			// Once we have completed hydrating the pivot model instance, we'll set
			// it on the result model's relationships array so the developer can
			// quickly and easily access any pivot table information.
			$result->relationships['pivot'] = $pivot;

			$pivot->sync() and $result->sync();
		}
	}

	/**
	 * Set the columns on the joining table that should be fetched.
	 *
	 * @param  array         $column
	 * @return Relationship
	 */
	public function with($columns)
	{
		$columns = (is_array($columns)) ? $columns : func_get_args();

		// The "with" array contains a couple of columns by default, so we will
		// just merge in the developer specified columns here, and we'll make
		// sure the values of the array are unique.
		$this->with = array_unique(array_merge($this->with, $columns));

		$this->set_select($this->foreign_key(), $this->other_key());

		return $this;
	}

	/**
	 * Get a model instance of the pivot table for the relationship.
	 *
	 * @return Pivot
	 */
	public function pivot()
	{
		$key = $this->base->get_key();

		$foreign = $this->foreign_key();

		return with(new Pivot($this->joining))->where($foreign, '=', $key);
	}

	/**
	 * Get the other or associated key for the relationship.
	 *
	 * @return string
	 */
	protected function other_key()
	{
		return Relationship::foreign($this->model, $this->other);
	}

	/**
	 * Get the fully qualified associated table's primary key.
	 *
	 * @return string
	 */
	protected function associated_key()
	{
		return $this->model->table().'.'.$this->model->key();
	}

}