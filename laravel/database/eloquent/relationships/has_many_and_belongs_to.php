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
	 * The columns on the joining table that should be fetched.
	 *
	 * @var array
	 */
	protected $with = array('id');

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

		// If the Pivot table is timestamped, we'll set the timestamp columns to be
		// fetched when the pivot table models are fetched by the developer else
		// the ID will be the only "extra" column fetched in by default.
		if (Pivot::$timestamps)
		{
			$this->with[] = 'created_at';

			$this->with[] = 'updated_at';
		}

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
	 * Detach a record from the joining table of the association.
	 *
	 * @param  int   $ids
	 * @return bool
	 */
	public function detach($ids)
	{
		if ( ! is_array($ids)) $ids = array($ids);

		return $this->pivot()->where_in($this->other_key(), $ids)->delete();
	}

	/**
	 * Sync the joining table with the array of given IDs.
	 *
	 * @param  array  $ids
	 * @return bool
	 */
	public function sync($ids)
	{
		$current = $this->pivot()->lists($this->other_key());

		// First we need to attach any of the associated models that are not currently
		// in the joining table. We'll spin through the given IDs, checking to see
		// if they exist in the array of current ones, and if not we insert.
		foreach ($ids as $id)
		{
			if ( ! in_array($id, $current))
			{
				$this->attach($id);
			}
		}

		// Next we will take the difference of the current and given IDs and detach
		// all of the entities that exists in the current array but are not in
		// the array of IDs given to the method, finishing the sync.
		$detach = array_diff($current, $ids);

		if (count($detach) > 0)
		{
			$this->detach(array_diff($current, $ids));
		}
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
		return $this->pivot()->delete();
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
		if (Pivot::$timestamps)
		{
			$attributes['created_at'] = new \DateTime;

			$attributes['updated_at'] = $attributes['created_at'];
		}

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
		// needs we allow an extra array of columns to be specified that will be
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
		$this->table->where_in($this->joining.'.'.$this->foreign_key(), $this->keys($results));
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

		$dictionary = array();

		foreach ($children as $child)
		{
			$dictionary[$child->pivot->$foreign][] = $child;
		}

		foreach ($parents as &$parent)
		{
			$parent_key = $parent->get_key();

			if (isset($dictionary[$parent_key]))
			{
				$parent->relationships[$relationship] = $dictionary[$parent_key];
			}
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
			$pivot = new Pivot($this->joining, $this->model->connection());

			// If the attribute key starts with "pivot_", we know this is a column on
			// the pivot table, so we will move it to the Pivot model and purge it
			// from the model since it actually belongs to the pivot model.
			foreach ($result->attributes as $key => $value)
			{
				if (starts_with($key, 'pivot_'))
				{
					$pivot->{substr($key, 6)} = $value;

					$result->purge($key);
				}
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

		// The "with" array contains a couple of columns by default, so we will just
		// merge in the developer specified columns here, and we will make sure
		// the values of the array are unique to avoid duplicates.
		$this->with = array_unique(array_merge($this->with, $columns));

		$this->set_select($this->foreign_key(), $this->other_key());

		return $this;
	}

	/**
	 * Get a relationship instance of the pivot table.
	 *
	 * @return Has_Many
	 */
	public function pivot()
	{
		$pivot = new Pivot($this->joining, $this->model->connection());

		return new Has_Many($this->base, $pivot, $this->foreign_key());
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