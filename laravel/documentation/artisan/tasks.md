# Tasks

## Contents

- [The Basics](#the-basics)
- [Creating & Running Tasks](#creating-tasks)
- [Bundle Tasks](#bundle-tasks)
- [CLI Options](#cli-options)

<a name="the-basics"></a>
## The Basics

Laravel's command-line tool is called Artisan. Artisan can be used to run "tasks" such as migrations, cronjobs, unit-tests, or anything that want. 

<a name="creating-tasks"></a>
## Creating & Running Tasks

To create a task create a new class in your **application/tasks** directory. The class name should be suffixed with "_Task", and should at least have a "run" method, like this:

#### Creating a task class:

	class Notify_Task {

		public function run($arguments)
		{
			// Do awesome notifying…
		}

	}

Now you can call the "run" method of your task via the command-line. You can even pass arguments:

#### Calling a task from the command line:

	php artisan notify

#### Calling a task and passing arguments:

	php artisan notify taylor

#### Calling a task from your application:

	Command::run(array('notify'));

#### Calling a task from your application with arguements:

	Command::run(array('notify', 'taylor'));

Remember, you can call specific methods on your task, so, let's add an "urgent" method to the notify task:

#### Adding a method to the task:

	class Notify_Task {

		public function run($arguments)
		{
			// Do awesome notifying…
		}

		public function urgent($arguments)
		{
			// This is urgent!
		}

	}

Now we can call our "urgent" method:

#### Calling a specific method on a task:

	php artisan notify:urgent

<a name="bundle-tasks"></a>
## Bundle Tasks

To create a task for your bundle just prefix the bundle name to the class name of your task. So, if your bundle was named "admin", a task might look like this:

#### Creating a task class that belongs to a bundle:

	class Admin_Generate_Task {

		public function run($arguments)
		{
			// Generate the admin!
		}

	}

To run your task just use the usual Laravel double-colon syntax to indicate the bundle:

#### Running a task belonging to a bundle:

	php artisan admin::generate

#### Running a specific method on a task belonging to a bundle:

	php artisan admin::generate:list

<a name="cli-options"></a>
## CLI Options

#### Setting the Laravel environment:

	php artisan foo --env=local

#### Setting the default database connection:

	php artisan foo --database=sqlite
