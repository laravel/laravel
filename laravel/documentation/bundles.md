# Bundles

## Contents

- [The Basics](#the-basics)
- [Creating Bundles](#creating-bundles)
- [Registering Bundles](#registering-bundles)
- [Bundles & Class Loading](#bundles-and-class-loading)
- [Starting Bundles](#starting-bundles)
- [Routing To Bundles](#routing-to-bundles)
- [Using Bundles](#using-bundles)
- [Bundle Assets](#bundle-assets)
- [Installing Bundles](#installing-bundles)
- [Upgrading Bundles](#upgrading-bundles)

<a name="the-basics"></a>
## The Basics

Bundles are the heart of the improvements that were made in Laravel 3.0. They are a simple way to group code into convenient "bundles". A bundle can have it's own views, configuration, routes, migrations, tasks, and more. A bundle could be everything from a database ORM to a robust authentication system. Modularity of this scope is an important aspect that has driven virtually all design decisions within Laravel. In many ways you can actually think of the application folder as the special default bundle with which Laravel is pre-programmed to load and use.

<a name="creating-and-registering"></a>
## Creating Bundles

The first step in creating a bundle is to create a folder for the bundle within your **bundles** directory. For this example, let's create an "admin" bundle, which could house the administrator back-end to our application. The **application/start.php** file provides some basic configuration that helps to define how our application will run. Likewise we'll create a **start.php** file within our new bundle folder for the same purpose. It is run every time the bundle is loaded. Let's create it:

#### Creating a bundle start.php file:

	<?php

	Autoloader::namespaces(array(
		'Admin' => Bundle::path('admin').'models',
	));

In this start file we've told the auto-loader that classes that are namespaced to "Admin" should be loaded out of our bundle's models directory. You can do anything you want in your start file, but typically it is used for registering classes with the auto-loader. **In fact, you aren't required to create a start file for your bundle.**

Next, we'll look at how to register this bundle with our application!

<a name="registering-bundles"></a>
## Registering Bundles

Now that we have our admin bundle, we need to register it with Laravel. Pull open your **application/bundles.php** file. This is where you register all bundles used by your application. Let's add ours:

#### Registering a simple bundle:

	return array('admin'),

By convention, Laravel will assume that the Admin bundle is located at the root level of the bundle directory, but we can specify another location if we wish:

#### Registering a bundle with a custom location:

	return array(

		'admin' => array('location' => 'userscape/admin'),

	);

Now Laravel will look for our bundle in **bundles/userscape/admin**.

<a name="bundles-and-class-loading"></a>
## Bundles & Class Loading

Typically, a bundle's **start.php** file only contains auto-loader registrations. So, you may want to just skip **start.php** and declare your bundle's mappings right in its registration array. Here's how:

#### Defining auto-loader mappings in a bundle registration:

	return array(

		'admin' => array(
			'autoloads' => array(
				'map' => array(
					'Admin' => '(:bundle)/admin.php',
				),
				'namespaces' => array(
					'Admin' => '(:bundle)/lib',
				),
				'directories' => array(
					'(:bundle)/models',
				),
			),
		),

	);

Notice that each of these options corresponds to a function on the Laravel [auto-loader](/docs/loading). In fact, the value of the option will automatically be passed to the corresponding function on the auto-loader.

You may have also noticed the **(:bundle)** place-holder. For convenience, this will automatically be replaced with the path to the bundle. It's a piece of cake.

<a name="starting-bundles"></a>
## Starting Bundles

So our bundle is created and registered, but we can't use it yet. First, we need to start it:

#### Starting a bundle:

	Bundle::start('admin');

This tells Laravel to run the **start.php** file for the bundle, which will register its classes in the auto-loader. The start method will also load the **routes.php** file for the bundle if it is present.

> **Note:** The bundle will only be started once. Subsequent calls to the start method will be ignored.

If you use a bundle throughout your application, you may want it to start on every request. If this is the case, you can configure the bundle to auto-start in your **application/bundles.php** file:

#### Configuration a bundle to auto-start:

	return array(

		'admin' => array('auto' => true),

	);

You do not always need to explicitly start a bundle. In fact, you can usually code as if the bundle was auto-started and Laravel will take care of the rest. For example, if you attempt to use a bundle views, configurations, languages, routes or filters, the bundle will automatically be started!

Each time a bundle is started, it fires an event. You can listen for the starting of bundles like so:

#### Listen for a bundle's start event:

	Event::listen('laravel.started: admin', function()
	{
		// The "admin" bundle has started…
	});

It is also possible to "disable" a bundle so that it will never be started.

#### Disabling a bundle so it can't be started:

	Bundle::disable('admin');

<a name="routing-to-bundles"></a>
## Routing To Bundles

Refer to the documentation on [bundle routing](/docs/routing#bundle-routes) and [bundle controllers](/docs/controllers#bundle-controllers) for more information on routing and bundles.

<a name="using-bundles"></a>
## Using Bundles

As mentioned previously, bundles can have views, configuration, language files and more. Laravel uses a double-colon syntax for loading these items. So, let's look at some examples:

#### Loading a bundle view:

	return View::make('bundle::view');

#### Loading a bundle configuration item:

	return Config::get('bundle::file.option');

#### Loading a bundle language line:

	return Lang::line('bundle::file.line');

Sometimes you may need to gather more "meta" information about a bundle, such as whether it exists, its location, or perhaps its entire configuration array. Here's how:

#### Determine whether a bundle exists:

	Bundle::exists('admin');

#### Retrieving the installation location of a bundle:

	$location = Bundle::path('admin');

#### Retrieving the configuration array for a bundle:

	$config = Bundle::get('admin');

#### Retrieving the names of all installed bundles:

	$names = Bundle::names();

<a name="bundle-assets"></a>
## Bundle Assets

If your bundle contains views, it is likely you have assets such as JavaScript and images that need to be available in the **public** directory of the application. No problem. Just create **public** folder within your bundle and place all of your assets in this folder.

Great! But, how do they get into the application's **public** folder. The Laravel "Artisan" command-line provides a simple command to copy all of your bundle's assets to the public directory. Here it is:

#### Publish bundle assets into the public directory:

	php artisan bundle:publish

This command will create a folder for the bundle's assets within the application's **public/bundles** directory. For example, if your bundle is named "admin", a **public/bundles/admin** folder will be created, which will contain all of the files in your bundle's public folder.

For more information on conveniently getting the path to your bundle assets once they are in the public directory, refer to the documentation on [asset management](/docs/views/assets#bundle-assets).

<a name="installing-bundles"></a>
## Installing Bundles

Of course, you may always install bundles manually; however, the "Artisan" CLI provides an awesome method of installing and upgrading your bundle. The framework uses simple Zip extraction to install the bundle. Here's how it works.

#### Installing a bundle via Artisan:

	php artisan bundle:install eloquent

Great! Now that you're bundle is installed, you're ready to [register it](#registering-bundles) and [publish its assets](#bundle-assets).

Need a list of available bundles? Check out the Laravel [bundle directory](http://bundles.laravel.com)

<a name="upgrading-bundles"></a>
## Upgrading Bundles

When you upgrade a bundle, Laravel will automatically remove the old bundle and install a fresh copy.

#### Upgrading a bundle via Artisan:

	php artisan bundle:upgrade eloquent

> **Note:** After upgrading the bundle, you may need to [re-publish its assets](#bundle-assets).

**Important:** Since the bundle is totally removed on an upgrade, you must be aware of any changes you have made to the bundle code before upgrading. You may need to change some configuration options in a bundle. Instead of modifying the bundle code directly, use the bundle start events to set them. Place something like this in your **application/start.php** file.

#### Listening for a bundle's start event:

	Event::listen('laravel.started: admin', function()
	{
		Config::set('admin::file.option', true);
	});