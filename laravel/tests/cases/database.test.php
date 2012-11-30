<?php

class DatabaseTest extends PHPUnit_Framework_TestCase {

	/**
	 * Set up the test environment.
	 */
	public function setUp()
	{
		DB::$connections = array();
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		DB::$connections = array();
	}

	/**
	 * Test the DB::connection method.
	 *
	 * @group laravel
	 */
	public function testConnectionMethodReturnsConnection()
	{
		$connection = DatabaseConnectStub::connection();
		$this->assertTrue(isset(DB::$connections[Config::get('database.default')]));

		$connection = DatabaseConnectStub::connection('mysql');
		$this->assertTrue(isset(DB::$connections['mysql']));
		$this->assertEquals(DB::$connections['mysql']->pdo->laravel_config, Config::get('database.connections.mysql'));
	}

	/**
	 * Test the DB::profile method.
	 *
	 * @group laravel
	 */
	public function testProfileMethodReturnsQueries()
	{
		Laravel\Database\Connection::$queries = array('Taylor');
		$this->assertEquals(array('Taylor'), DB::profile());
		Laravel\Database\Connection::$queries = array();
	}

	/**
	 * Test the __callStatic method.
	 *
	 * @group laravel
	 */
	public function testConnectionMethodsCanBeCalledStaticly()
	{
		$this->assertEquals('sqlite', DB::driver());
	}

}

class DatabaseConnectStub extends Laravel\Database {

	protected static function connect($config) { return new PDOStub($config); }

}

class PDOStub extends PDO {
	
	public $laravel_config;

	public function __construct($config) { $this->laravel_config = $config; }

	public function foo() { return 'foo'; }

}