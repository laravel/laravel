# Class Auto Loading

## Contents

- [The Basics](#the-basics)
- [Registering Directories](#directories)
- [Registering Mappings](#mappings)
- [Registering Namespaces](#namespaces)

<a name="the-basics"></a>
## The Basics

Auto-loading allows you to lazily load class files when they are needed without explicitly *requiring* or *including* them. So, only the classes you actually need are loaded for any given request to your application, and you can just jump right in and start using any class without loading it's related file.

By default, the **models** and **libraries** directories are registered with the auto-loader in the **application/start.php** file. The loader uses a class to file name loading convention, where all file names are lower-cased. So for instance, a "User" class within the models directory should have a file name of "user.php". You may also nest classes within sub-directories. Just namespace the classes to match the directory structure. So, a "Entities\User" class would have a file name of "entities/user.php" within the models directory.

<a name="directories"></a>
## Registering Directories

As noted above, the models and libraries directories are registered with the auto-loader by default; however, you may register any directories you like to use the same class to file name loading conventions:

#### Registering directories with the auto-loader:

	Autoloader::directories(array(
		path('app').'entities',
		path('app').'repositories',
	));

<a name="mappings"></a>
## Registering Mappings

Sometimes you may wish to manually map a class to its related file. This is the most performant way of loading classes:

#### Registering a class to file mapping with the auto-loader:

	Autoloader::map(array(
		'User'    => path('app').'models/user.php',
		'Contact' => path('app').'models/contact.php',
	));

<a name="namespaces"></a>
## Registering Namespaces

Many third-party libraries use the PSR-0 standard for their structure. PSR-0 states that class names should match their file names, and directory structure is indicated by namespaces. If you are using a PSR-0 library, just register it's root namespace and directory with the auto-loader:

#### Registering a namespace with the auto-loader:

	Autoloader::namespaces(array(
		'Doctrine' => path('libraries').'Doctrine',
	));

Before namespaces were available in PHP, many projects used underscores to indicate directory structure. If you are using one of these legacy libraries, you can still easily register it with the auto-loader. For example, if you are using SwiftMailer, you may have noticed all classes begin with "Swift_". So, we'll register "Swift" with the auto-loader as the root of an underscored project.

#### Registering an "underscored" library with the auto-loader:

	Autoloader::underscored(array(
		'Swift' => path('libraries').'SwiftMailer',
	));