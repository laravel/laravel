<?php

class PostTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		Artisan::call('migrate');
		/*
		 Laravel should do this automatically, or you
		 should explain this problem in the doc
		///
		Post::flushEventListeners();
		Post::boot();
		*/
	}

	public function tearDown()
	{
		parent::tearDown();
		Artisan::call('migrate:reset');
	}

	public function testBootMethod()
	{
		Post::create(['comments_count' => 2]);
		$this->assertEquals(1, Post::count());
		$this->assertEquals(6, Post::first()->comments_count);
	}

	public function testBootMethodAgain()
	{
		Post::create(['comments_count' => 2]);
		$this->assertEquals(1, Post::count());
		$this->assertEquals(6, Post::first()->comments_count);
	}

	public function testBootMethodAgainAndAgain()
	{
		Post::create(['comments_count' => 2]);
		$this->assertEquals(1, Post::count());
		$this->assertEquals(6, Post::first()->comments_count);
	}
}
