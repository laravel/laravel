<?php use Illuminate\Support\Facades\Facade;

class Test extends Facade {

	protected static function getFacadeAccessor() { return 'test'; }

}

App::bind('test', function() { return new \Misc\Test; });



class Anotha extends Facade {

	protected static function getFacadeAccessor() { return 'anotha'; }
	
}

App::bind('anotha', function() { return new \Misc\Anotha; });
