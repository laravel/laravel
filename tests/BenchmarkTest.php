<?php

class BenchmarkTest extends PHPUnit_Framework_TestCase {

	public function testStartMethodCreatesMark()
	{
		Benchmark::start('test');

		$this->assertTrue(is_float(Benchmark::check('test')));
		$this->assertGreaterThan(0.0, Benchmark::check('test'));
	}

}