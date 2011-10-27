<?php use Laravel\Routing\Filter;

class RouteFilterTest extends PHPUnit_Framework_TestCase {

	public function test_simple_filters_can_be_called()
	{
		$filters = array(
			'simple' => function()
			{
				return 'simple';
			},

			'parameters' => function($one, $two, $three = null)
			{
				return $one.'|'.$two.'|'.$three;
			},
		);

		Filter::register($filters);

		$this->assertEquals(Filter::run(array('simple'), array(), true), 'simple');
		$this->assertEquals(Filter::run(array('parameters:1,2'), array(), true), '1|2|');
		$this->assertEquals(Filter::run(array('parameters:1,2'), array(3), true), '3|1|2');
	}

}