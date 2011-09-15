<?php

class SessionResolverTest extends PHPUnit_Framework_TestCase {

	public function testDriversCanBeResolved()
	{
		IoC::resolve('laravel.config')->set('application.key', 'something');

		$this->assertInstanceOf('Laravel\\Session\\Manager', IoC::resolve('laravel.session.manager'));
		$this->assertInstanceOf('Laravel\\Session\\Drivers\\APC', IoC::resolve('laravel.session.apc'));
		$this->assertInstanceOf('Laravel\\Session\\Drivers\\Cookie', IoC::resolve('laravel.session.cookie'));
		$this->assertInstanceOf('Laravel\\Session\\Drivers\\Database', IoC::resolve('laravel.session.database'));
		$this->assertInstanceOf('Laravel\\Session\\Drivers\\File', IoC::resolve('laravel.session.file'));
		$this->assertInstanceOf('Laravel\\Session\\Drivers\\Memcached', IoC::resolve('laravel.session.memcached'));
	}

}