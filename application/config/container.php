<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Inversion of Control Container
	|--------------------------------------------------------------------------
	|
	| Here you may define resolvers for the Laravel inversion of control (IoC)
	| container. An IoC container provides the ability to create more flexible
	| and testable applications, as well as a convenient method of managing
	| the instantiation of complex objects.
	|
	| To register a resolver in the container, simple create add an item to
	| the array for the object with a closure that returns an instance of
	| the object.
	|
	| For example, here's how to register a resolver for a Mailer class:
	|
	|		'mailer' => function($c)
	|		{
	|			return new Mailer($sender, $key);
	|		}
	|
	| Note that the container instance itself is passed into the resolver,
	| allowing you to continue to resolve dependencies within the resolver
	| itself. This allows you to easily resolve nested dependencies.
	|
	| When creating controller instances, Laravel will check to see if a
	| resolver has been registered for the controller. If it has, it will
	| be used to create the controller instance. All controller resolvers
	| should be registered beginning using a {controllers}.{name} naming
	| convention. For example:
	|
	|		'controllers.user' => function($c)
	|		{
	|			return new User_Controller($c->resolve('repository'));
	|		}
	|
	| Of course, sometimes you may wish to register an object as a singleton
	| Singletons are resolved by the controller the first time they are
	| resolved; however, that same resolved instance will continue to be
	| returned by the container each time it is requested. Registering an
	| object as a singleton couldn't be simpler:
	|
	|		'mailer' => array('singleton' => true, 'resolver' => function($c)
	|		{
	|			return new Mailer($sender, $key);
	|		})
	|
	*/

);