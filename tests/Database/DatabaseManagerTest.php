<?php

use Laravel\Database\Manager;

class DatabaseManagerTest extends PHPUnit_Framework_TestCase {

	public function testWhenCallingConnectionMethodForNonEstablishedConnectionNewConnectionIsReturned()
	{
		$manager = new Manager($this->getConfig());

		$connection = $manager->connection();

		$this->assertInstanceOf('PDOStub', $connection->pdo);
		$this->assertInstanceOf('Laravel\\Database\\Connection', $connection);
	}

	public function testConnectionMethodsReturnsSingletonConnections()
	{
		$manager = new Manager($this->getConfig());

		$connection = $manager->connection();

		$this->assertTrue($connection === $manager->connection());
	}

	public function testConnectionMethodOverridesDefaultWhenConnectionNameIsGiven()
	{
		$config = $this->getConfig();

		$config['connectors']['something'] = function($config) {return new AnotherPDOStub;};

		$manager = new Manager($config);

		$this->assertInstanceOf('AnotherPDOStub', $manager->connection('something')->pdo);
	}

	public function testConfigurationArrayIsPassedToConnector()
	{
		$manager = new Manager($this->getConfig());

		$this->assertEquals($manager->connection()->pdo->config, $this->getConfig());
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionIsThrownIfConnectorIsNotDefined()
	{
		$manager = new Manager($this->getConfig());

		$manager->connection('something');
	}

	public function testTableMethodCallsTableMethodOnConnection()
	{
		$manager = new Manager($this->getConfig());

		$this->assertEquals($manager->table('users'), 'table');
	}

	// ---------------------------------------------------------------------
	// Support Functions
	// ---------------------------------------------------------------------

	private function getConfig()
	{
		return array('default' => 'test', 'connectors' => array('test' => function($config) {return new PDOStub($config);}));
	}

}

// ---------------------------------------------------------------------
// Stubs
// ---------------------------------------------------------------------

class PDOStub extends PDO {

	public $config;

	public function __construct($config = array()) { $this->config = $config; }

	public function table()
	{
		return 'table';
	}

}

class AnotherPDOStub extends PDO {
	
	public function __construct() {}

	public function table()
	{
		return 'anotherTable';
	}

}