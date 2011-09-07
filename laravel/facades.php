<?php namespace Laravel\Facades;

use Laravel\IoC;

abstract class Facade {

	/**
	 * Magic Method for passing methods to a class registered in the IoC container.
	 * This provides a convenient method of accessing functions on classes that
	 * could not otherwise be accessed staticly.
	 *
	 * Facades allow Laravel to still have a high level of dependency injection
	 * and testability while still accomodating the common desire to conveniently
	 * use classes via static methods.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(IoC::container()->resolve('laravel.'.static::$resolve), $method), $parameters);
	}

}

class Asset extends Facade { public static $resolve = 'asset'; }
class Auth extends Facade { public static $resolve = 'auth'; }
class Cache extends Facade { public static $resolve = 'cache'; }
class Config extends Facade { public static $resolve = 'config'; }
class Cookie extends Facade { public static $resolve = 'cookie'; }
class Crypter extends Facade { public static $resolve = 'crypter'; }
class DB extends Facade { public static $resolve = 'database'; }
class Download extends Facade { public static $resolve = 'download'; }
class File extends Facade { public static $resolve = 'file'; }
class Form extends Facade { public static $resolve = 'form'; }
class Hasher extends Facade { public static $resolve = 'hasher'; }
class HTML extends Facade { public static $resolve = 'html'; }
class Input extends Facade { public static $resolve = 'input'; }
class Loader extends Facade { public static $resolve = 'loader'; }
class Package extends Facade { public static $resolve = 'package'; }
class Redirect extends Facade { public static $resolve = 'redirect'; }
class Request extends Facade { public static $resolve = 'request'; }
class Session extends Facade { public static $resolve = 'session'; }
class URL extends Facade { public static $resolve = 'url'; }
class Validator extends Facade { public static $resolve = 'validator'; }