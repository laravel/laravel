<?php

class SessionManagerTest extends PHPUnit_Framework_TestCase {

	public function testDriverMethodReturnsDriverWhenOneIsRegistered()
	{
		$dependencies = array(
			'laravel.session.test' => array('resolver' => function($container)
			{
				return new stdClass;
			})
		);

		$manager = new Laravel\Session\Manager(new Laravel\Container($dependencies));

		$this->assertInstanceOf('stdClass', $manager->driver('test'));
	}

	/**
	 * @expectedException Exception
	 */
	public function testDriverMethodThrowsExceptionForUndefinedDriver()
	{
		$manager = new Laravel\Session\Manager(new Laravel\Container(array()));

		$manager->driver('test');
	}

}