<?php use Laravel\Routing\Filter;

class RouteFilterTest extends PHPUnit_Framework_TestCase {

	public function test_simple_filters_can_be_called()
	{
		$filters = array(
			'simple' => function()
			{
				return 'simple';
			},

			'parameters' => function($one, $two)
			{
				return $one.'|'.$two;
			},
		);

		Filter::register($filters);

		$this->assertEquals(Filter::run(array('simple'), array(), true), 'simple');
		$this->assertEquals(Filter::run(array('parameters:1,2'), array(), true), '1|2');
	}

}