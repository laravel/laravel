# Controllers

## Contents

- [The Basics](#the-basics)
- [Controller Routing](#controller-routing)
- [Bundle Controllers](#bundle-controllers)
- [Action Filters](#action-filters)
- [Nested Controllers](#nested-controllers)
- [Controller Layouts](#controller-layouts)
- [RESTful Controllers](#restful-controllers)
- [Dependency Injection](#dependency-injection)
- [Controller Factory](#controller-factory)

<a name="the-basics"></a>
## The Basics

Controllers are classes that are responsible for accepting user input and managing interactions between models, libraries, and views. Typically, they will ask a model for data, and then return a view that presents that data to the user.

The usage of controllers is the most common method of implementing application logic in modern web-development. However, Laravel also empowers developers to implement their application logic within routing declarations. This is explored in detail in the [routing document](/docs/routing). New users are encouraged to start with controllers. There is nothing that route-based application logic can do that controllers can't.

Controller classes should be stored in **application/controllers** and should extend the Base\_Controller class. A Home\_Controller class is included with Laravel.

#### Creating a simple controller:

	class Admin_Controller extends Base_Controller
	{

		public function action_index()
		{
			//
		}

	}

**Actions** are the name of controller methods that are intended to be web-accessible.  Actions should be prefixed with "action\_". All other methods, regardless of scope, will not be web-accessible.

> **Note:** The Base\_Controller class extends the main Laravel Controller class, and gives you a convenient place to put methods that are common to many controllers.

<a name="controller-routing"></a>
## Controller Routing

It is important to be aware that all routes in Laravel must be explicitly defined, including routes to controllers.

This means that controller methods that have not been exposed through route registration **cannot** be accessed. It's possible to automatically expose all methods within a controller using controller route registration. Controller route registrations are typically defined in **application/routes.php**.

Check [the routing page](/docs/routing#controller-routing) for more information on routing to controllers.

<a name="bundle-controllers"></a>
## Bundle Controllers

Bundles are Laravel's modular package system. Bundles can be easily configured to handle requests to your application. We'll be going over [bundles in more detail](/docs/bundles) in another document.

Creating controllers that belong to bundles is almost identical to creating your application controllers. Just prefix the controller class name with the name of the bundle, so if your bundle is named "admin", your controller classes would look like this:

#### Creating a bundle controller class:

	class Admin_Home_Controller extends Base_Controller
	{

		public function action_index()
		{
			return "Hello Admin!";
		}

	}

But, how do you register a bundle controller with the router? It's simple. Here's what it looks like:

#### Registering a bundle's controller with the router:

	Route::controller('admin::home');

Great! Now we can access our "admin" bundle's home controller from the web!

> **Note:** Throughout Laravel the double-colon syntax is used to denote bundles.  More information on bundles can be found in the [bundle documentation](/docs/bundles).

<a name="action-filters"></a>
## Action Filters

Action filters are methods that can be run before or after a controller action.  With Laravel you don't only have control over which filters are assigned to which actions.  But, you can also choose which http verbs (post, get, put, and delete) will activate a filter.

You can assign "before" and "after" filters to controller actions within the controller's constructor.

#### Attaching a filter to all actions:

	$this->filter('before', 'auth');

In this example the 'auth' filter will be run before every action within this controller.  The auth action comes out-of-the-box with Laravel and can be found in **application/routes.php**.  The auth filter verifies that a user is logged in and redirects them to 'login' if they are not.

#### Attaching a filter to only some actions:

	$this->filter('before', 'auth')->only(array('index', 'list'));

In this example the auth filter will be run before the action_index() or action_list() methods are run.  Users must be logged in before having access to these pages.  However, no other actions within this controller require an authenticated session.

#### Attaching a filter to all except a few actions:

	$this->filter('before', 'auth')->except(array('add', 'posts'));

Much like the previous example, this declaration ensures that the auth filter is run on only some of this controller's actions.  Instead of declaring to which actions the filter applies we are instead declaring the actions that will not require authenticated sessions.  It can sometimes be safer to use the 'except' method as it's possible to add new actions to this controller and to forget to add them to only().  This could potentially lead to your controller's action being unintentionally accessible by users who haven't been authenticated.

#### Attaching a filter to run on POST:

	$this->filter('before', 'csrf')->on('post');

This example shows how a filter can be run only on a specific http verb.  In this case we're running the csrf filter only when a form post is made.  The csrf filter is designed to prevent form posts from other systems (spam bots for example) and comes by default with Laravel.  You can find the csrf filter in **application/routes.php**.

*Further Reading:*

- *[Route Filters](/docs/routing#filters)*

<a name="nested-controllers"></a>
## Nested Controllers

Controllers may be located within any number of sub-directories within the main **application/controllers** folder.

Define the controller class and store it in **controllers/admin/panel.php**.

	class Admin_Panel_Controller extends Base_Controller
	{

		public function action_index()
		{
			//
		}

	}

#### Register the nested controller with the router using "dot" syntax:

	Route::controller('admin.panel');

> **Note:** When using nested controllers, always register your controllers from most nested to least nested in order to avoid shadowing controller routes.

#### Access the "index" action of the controller:

	http://localhost/admin/panel

<a name="controller-layouts"></a>
## Controller Layouts

Full documentation on using layouts with Controllers [can be found on the Templating page](/docs/views/templating).

<a name="restful-controllers"></a>
## RESTful Controllers

Instead of prefixing controller actions with "action_", you may prefix them with the HTTP verb they should respond to.

#### Adding the RESTful property to the controller:

	class Home_Controller extends Base_Controller
	{

		public $restful = true;

	}

#### Building RESTful controller actions:

	class Home_Controller extends Base_Controller
	{

		public $restful = true;

		public function get_index()
		{
			//
		}

		public function post_index()
		{
			//
		}

	}

This is particularly useful when building CRUD methods as you can separate the logic which populates and renders a form from the logic that validates and stores the results.

<a name="dependency-injection"></a>
## Dependency Injection

If you are focusing on writing testable code, you will probably want to inject dependencies into the constructor of your controller. No problem. Just register your controller in the [IoC container](/docs/ioc). When registering the controller with the container, prefix the key with **controller**. So, in our **application/start.php** file, we could register our user controller like so:

	IoC::register('controller: user', function()
	{
		return new User_Controller;
	});

When a request to a controller enters your application, Laravel will automatically determine if the controller is registered in the container, and if it is, will use the container to resolve an instance of the controller.

> **Note:** Before diving into controller dependency injection, you may wish to read the documentation on Laravel's beautiful [IoC container](/docs/ioc).

<a name="controller-factory"></a>
## Controller Factory

If you want even more control over the instantiation of your controllers, such as using a third-party IoC container, you'll need to use the Laravel controller factory.

**Register an event to handle controller instantiation:**

	Event::listen(Controller::factory, function($controller)
	{
		return new $controller;
	});

The event will receive the class name of the controller that needs to be resolved. All you need to do is return an instance of the controller.
