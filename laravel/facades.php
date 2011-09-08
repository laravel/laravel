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
		return call_user_func_array(array(IoC::container()->resolve(static::$resolve), $method), $parameters);
	}

}

class Asset extends Facade { public static $resolve = 'laravel.asset'; }
class Auth extends Facade { public static $resolve = 'laravel.auth'; }
class Cache extends Facade { public static $resolve = 'laravel.cache'; }
class Config extends Facade { public static $resolve = 'laravel.config'; }
class Cookie extends Facade { public static $resolve = 'laravel.cookie'; }
class Crypter extends Facade { public static $resolve = 'laravel.crypter'; }
class DB extends Facade { public static $resolve = 'laravel.database'; }
class Download extends Facade { public static $resolve = 'laravel.download'; }
class File extends Facade { public static $resolve = 'laravel.file'; }
class Form extends Facade { public static $resolve = 'laravel.form'; }
class Hasher extends Facade { public static $resolve = 'laravel.hasher'; }
class HTML extends Facade { public static $resolve = 'laravel.html'; }
class Input extends Facade { public static $resolve = 'laravel.input'; }
class Lang extends Facade { public static $resolve = 'laravel.lang'; }
class Loader extends Facade { public static $resolve = 'laravel.loader'; }
class Package extends Facade { public static $resolve = 'laravel.package'; }
class Redirect extends Facade { public static $resolve = 'laravel.redirect'; }
class Request extends Facade { public static $resolve = 'laravel.request'; }
class Response extends Facade { public static $resolve = 'laravel.response'; }
class Session extends Facade { public static $resolve = 'laravel.session'; }
class URL extends Facade { public static $resolve = 'laravel.url'; }
class Validator extends Facade { public static $resolve = 'laravel.validator'; }
class View extends Facade { public static $resolve = 'laravel.view'; }