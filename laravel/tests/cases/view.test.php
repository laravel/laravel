<?php

class ViewTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the testing environment.
	 */
	public function tearDown()
	{
		View::$shared = array();
		unset(Event::$events['composing: test.basic']);
	}

	/**
	 * Test the View::make method.
	 *
	 * @group laravel
	 */
	public function testMakeMethodReturnsAViewInstance()
	{
		$this->assertInstanceOf('Laravel\\View', View::make('home.index'));
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testViewNameIsSetByConstrutor()
	{
		$view = new View('home.index');

		$this->assertEquals('home.index', $view->view);
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testViewIsCreatedWithCorrectPath()
	{
		$view = new View('home.index');
		
		$this->assertEquals(
			str_replace(DS, '/', path('app')).'views/home/index.php',
			str_replace(DS, '/', $view->path)
		);
	}

	/**
	 * Test the View class constructor for bundles.
	 *
	 * @group laravel
	 */
	public function testBundleViewIsCreatedWithCorrectPath()
	{
		$view = new View('home.index');

		$this->assertEquals(
			str_replace(DS, '/', Bundle::path(DEFAULT_BUNDLE)).'views/home/index.php',
			str_replace(DS, '/', $view->path)
		);
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testDataIsSetOnViewByConstructor()
	{
		$view = new View('home.index', array('name' => 'Taylor'));

		$this->assertEquals('Taylor', $view->data['name']);
	}

	/**
	 * Test the View::name method.
	 *
	 * @group laravel
	 */
	public function testNameMethodRegistersAViewName()
	{
		View::name('home.index', 'home');

		$this->assertEquals('home.index', View::$names['home']);
	}

	/**
	 * Test the View::shared method.
	 *
	 * @group laravel
	 */
	public function testSharedMethodAddsDataToSharedArray()
	{
		View::share('comment', 'Taylor');

		$this->assertEquals('Taylor', View::$shared['comment']);
	}

	/**
	 * Test the View::with method.
	 *
	 * @group laravel
	 */
	public function testViewDataCanBeSetUsingWithMethod()
	{
		$view = View::make('home.index')->with('comment', 'Taylor');

		$this->assertEquals('Taylor', $view->data['comment']);
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testEmptyMessageContainerSetOnViewWhenNoErrorsInSession()
	{
		$view = new View('home.index');

		$this->assertInstanceOf('Laravel\\Messages', $view->data['errors']);
	}

	/**
	 * Test the View __set method.
	 *
	 * @group laravel
	 */
	public function testDataCanBeSetOnViewsThroughMagicMethods()
	{
		$view = new View('home.index');

		$view->comment = 'Taylor';

		$this->assertEquals('Taylor', $view->data['comment']);
	}

	/**
	 * Test the View __get method.
	 *
	 * @group laravel
	 */
	public function testDataCanBeRetrievedFromViewsThroughMagicMethods()
	{
		$view = new View('home.index');

		$view->comment = 'Taylor';

		$this->assertEquals('Taylor', $view->comment);
	}

	/**
	 * Test the View's ArrayAccess implementation.
	 *
	 * @group laravel
	 */
	public function testDataCanBeSetOnTheViewThroughArrayAccess()
	{
		$view = new View('home.index');

		$view['comment'] = 'Taylor';

		$this->assertEquals('Taylor', $view->data['comment']);
	}

	/**
	 * Test the View's ArrayAccess implementation.
	 *
	 * @group laravel
	 */
	public function testDataCanBeRetrievedThroughArrayAccess()
	{
		$view = new View('home.index');

		$view['comment'] = 'Taylor';

		$this->assertEquals('Taylor', $view['comment']);
	}

	/**
	 * Test the View::nest method.
	 *
	 * @group laravel
	 */
	public function testNestMethodSetsViewInstanceInData()
	{
		$view = View::make('home.index')->nest('partial', 'tests.basic');

		$this->assertEquals('tests.basic', $view->data['partial']->view);

		$this->assertInstanceOf('Laravel\\View', $view->data['partial']);
	}

	/**
	 * Test that the registered data is passed to the view correctly.
	 *
	 * @group laravel
	 */
	public function testDataIsPassedToViewCorrectly()
	{
		View::share('name', 'Taylor');

		$view = View::make('tests.basic')->with('age', 25)->render();

		$this->assertEquals('Taylor is 25', $view);
	}

	/**
	 * Test that the View class renders nested views.
	 *
	 * @group laravel
	 */
	public function testNestedViewsAreRendered()
	{
		$view = View::make('tests.basic')
								->with('age', 25)
								->nest('name', 'tests.nested');

		$this->assertEquals('Taylor is 25', $view->render());
	}

	/**
	 * Test that the View class renders nested responses.
	 *
	 * @group laravel
	 */
	public function testNestedResponsesAreRendered()
	{
		$view = View::make('tests.basic')
								->with('age', 25)
								->with('name', Response::view('tests.nested'));

		$this->assertEquals('Taylor is 25', $view->render());
	}

	/**
	 * Test the View class raises a composer event.
	 *
	 * @group laravel
	 */
	public function testComposerEventIsCalledWhenViewIsRendering()
	{
		View::composer('tests.basic', function($view)
		{
			$view->data = array('name' => 'Taylor', 'age' => 25);
		});

		$view = View::make('tests.basic')->render();

		$this->assertEquals('Taylor is 25', $view);
	}

}