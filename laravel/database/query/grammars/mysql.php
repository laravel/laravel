<?php namespace Laravel\Database\Query\Grammars;

class MySQL extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '`%s`';

}