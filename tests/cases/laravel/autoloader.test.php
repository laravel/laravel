<?php

class AutoloaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Autoloader::map method.
	 *
	 * @group laravel
	 */
	public function testMapsCanBeRegistered()
	{
		Autoloader::map(array(
			'Foo' => APP_PATH.'models/foo.php',
		));

		$this->assertEquals(APP_PATH.'models/foo.php', Autoloader::$mappings['Foo']);
	}

	/**
	 * Test the Autoloader::alias method.
	 *
	 * @group laravel
	 */
	public function testAliasesCanBeRegistered()
	{
		Autoloader::alias('Foo\\Bar', 'Foo');

		$this->assertEquals('Foo\\Bar', Autoloader::$aliases['Foo']);
	}

	/**
	 * Test the Autoloader::psr method.
	 *
	 * @group laravel
	 */
	public function testPsrDirectoriesCanBeRegistered()
	{
		Autoloader::psr(array(
			APP_PATH.'foo'.DS.'bar',
			APP_PATH.'foo'.DS.'baz'.DS.DS,
		));

		$this->assertTrue(in_array(APP_PATH.'foo'.DS.'bar'.DS, Autoloader::$psr));
		$this->assertTrue(in_array(APP_PATH.'foo'.DS.'baz'.DS, Autoloader::$psr));
	}

	/**
	 * Test the Autoloader::namespaces method.
	 *
	 * @group laravel
	 */
	public function testNamespacesCanBeRegistered()
	{
		Autoloader::namespaces(array(
			'Autoloader_1' => BUNDLE_PATH.'autoload'.DS.'models',
			'Autoloader_2' => BUNDLE_PATH.'autoload'.DS.'libraries'.DS.DS,
		));

		$this->assertEquals(BUNDLE_PATH.'autoload'.DS.'models'.DS, Autoloader::$namespaces['Autoloader_1']);
		$this->assertEquals(BUNDLE_PATH.'autoload'.DS.'libraries'.DS, Autoloader::$namespaces['Autoloader_2']);
	}

	/**
	 * Test the loading of PSR-0 models and libraries.
	 *
	 * @group laravel
	 */
	public function testPsrLibrariesAndModelsCanBeLoaded()
	{
		$this->assertInstanceOf('User', new User);
		$this->assertInstanceOf('Repositories\\User', new Repositories\User);
	}

	/**
	 * Test the loading of hard-coded classes.
	 *
	 * @group laravel
	 */
	public function testHardcodedClassesCanBeLoaded()
	{
		Autoloader::map(array(
			'Autoloader_HardCoded' => APP_PATH.'models'.DS.'autoloader.php',
		));

		$this->assertInstanceOf('Autoloader_HardCoded', new Autoloader_HardCoded);
	}

	/**
	 * Test the loading of classes mapped by namespaces.
	 *
	 * @group laravel
	 */
	public function testClassesMappedByNamespaceCanBeLoaded()
	{
		Autoloader::namespaces(array(
			'Dashboard' => APP_PATH.'dashboard',
		));

		$this->assertInstanceOf('Dashboard\\Repository', new Dashboard\Repository);
	}

}