# Runtime Configuration

## Contents

- [The Basics](#the-basics)
- [Retrieving Options](#retrieving-options)
- [Setting Options](#setting-options)

<a name="the-basics"></a>
## The Basics

Sometimes you may need to get and set configuration options at runtime. For this you'll use the **Config** class, which utilizes Laravel's "dot" syntax for accessing configuration files and items.

<a name="retrieving-options"></a>
##  Retrieving Options

#### Retrieve a configuration option:

	$value = Config::get('application.url');

#### Return a default value if the option doesn't exist:

	$value = Config::get('application.timezone', 'UTC');

#### Retrieve an entire configuration array:

	$options = Config::get('database');

<a name="setting-options"></a>
## Setting Options

#### Set a configuration option:

	Config::set('cache.driver', 'apc');