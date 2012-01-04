<?php namespace Laravel\Database\Schema\Columns;

abstract class Column {

	/**
	 * Indicates if the column should be a unique index.
	 *
	 * @var bool
	 */
	public $unique = false;

	/**
	 * Indicates if the column should have a full-text index.
	 *
	 * @var bool
	 */
	public $fulltext = false;

	/**
	 * Indicates if the column should be nullable.
	 *
	 * @var bool
	 */
	public $nullable = false;

	/**
	 * Indicates if the column should auto-increment.
	 *
	 * @var bool
	 */
	public $incrementing = false;

}