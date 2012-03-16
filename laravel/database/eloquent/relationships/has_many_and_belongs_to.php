<?php namespace Laravel\Database\Eloquent\Relationships;

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

		$this->joining = $table;

		parent::__construct($model, $associated, $foreign);
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
	 * @param  int   $id
	 * @return bool
	 */
	public function add($id)
	{
		return $this->insert_joining($this->join_record($id));
	}

	/**
	 * Insert a new record for the association.
	 *
	 * @param  array  $attributes
	 * @return bool
	 */
	public function insert($attributes)
	{
		$id = $this->table->insert_get_id($attributes, $this->model->sequence());

		$result = $this->insert_joining($this->join_record($id));

		return is_numeric($id) and $result;
	}

	/**
	 * Delete all of the records from the joining table for the model.
	 *
	 * @return int
	 */
	public function delete()
	{
		return $this->joining_table()->where($this->foreign_key(), '=', $this->base->get_key())->delete();
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
		$foreign = $this->foreign_key();

		$this->set_select($foreign)->set_join($this->other_key())->set_where($foreign);
	}

	/**
	 * Set the SELECT clause on the query builder for the relationship.
	 *
	 * @param  string  $foreign
	 * @return void
	 */
	protected function set_select($foreign)
	{
		$foreign = $this->joining.'.'.$foreign.' as pivot_foreign_key';

		$this->table->select(array($this->model->table().'.*', $foreign));

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
		$foreign = 'pivot_foreign_key';

		foreach ($children as $key => $child)
		{
			$parents[$child->$foreign]->relationships[$relationship][$child->{$child->key()}] = $child;

			// After matching the child model with its parent, we can remove the foreign key
			// from the model, as it was only necessary to allow us to know which parent
			// the child belongs to for eager loading and isn't necessary otherwise.
			unset($child->attributes[$foreign]);

			unset($child->original[$foreign]);
		}
	}

	/**
	 * Clean-up any pivot columns that are on the results.
	 *
	 * @param  array  $results
	 * @return void
	 */
	protected function clean(&$results)
	{
		foreach ($results as &$result)
		{
			
		}
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