<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function explode, func_get_args, ini_get, is_array, is_callable, is_object, is_string, preg_replace, restore_error_handler, set_error_handler, sprintf, str_contains, str_ends_with;


/**
 * PHP callable tools.
 */
final class Callback
{
	use Nette\StaticClass;

	/**
	 * Invokes internal PHP function with own error handler.
	 * @param  callable-string  $function
	 * @param  list<mixed>  $args
	 * @param  callable(string, int): (bool|void|null)  $onError
	 */
	public static function invokeSafe(string $function, array $args, callable $onError): mixed
	{
		$prev = set_error_handler(function (int $severity, string $message, string $file, int $line) use ($onError, &$prev, $function): bool {
			if ($file === __FILE__) {
				$msg = ini_get('html_errors')
					? Html::htmlToText($message)
					: $message;
				$msg = (string) preg_replace("#^$function\\(.*?\\): #", '', $msg);
				if ($onError($msg, $severity) !== false) {
					return true;
				}
			}

			return $prev ? $prev(...func_get_args()) !== false : false;
		});

		try {
			return $function(...$args);
		} finally {
			restore_error_handler();
		}
	}


	/**
	 * Checks that $callable is a valid PHP callback and returns it. With $syntax set to true, only verifies
	 * the structural validity without checking whether the class or method actually exists.
	 * @return callable
	 * @throws Nette\InvalidArgumentException
	 */
	public static function check(mixed $callable, bool $syntax = false): mixed
	{
		if (!is_callable($callable, $syntax)) {
			throw new Nette\InvalidArgumentException(
				$syntax
				? 'Given value is not a callable type.'
				: sprintf("Callback '%s' is not callable.", self::toString($callable)),
			);
		}

		return $callable;
	}


	/**
	 * Converts PHP callback to textual form. Class or method may not exists.
	 */
	public static function toString(mixed $callable): string
	{
		if ($callable instanceof \Closure) {
			$inner = self::unwrap($callable);
			return '{closure' . ($inner instanceof \Closure ? '}' : ' ' . self::toString($inner) . '}');
		} else {
			is_callable(is_object($callable) ? [$callable, '__invoke'] : $callable, true, $textual);
			return $textual;
		}
	}


	/**
	 * Returns reflection for method or function used in PHP callback.
	 * @param  callable  $callable  type check is escalated to ReflectionException
	 * @throws \ReflectionException  if callback is not valid
	 */
	public static function toReflection(mixed $callable): \ReflectionMethod|\ReflectionFunction
	{
		if ($callable instanceof \Closure) {
			$callable = self::unwrap($callable);
		}

		if (is_string($callable) && str_contains($callable, '::')) {
			return new ReflectionMethod(...explode('::', $callable, 2));
		} elseif (is_array($callable)) {
			return new ReflectionMethod($callable[0], $callable[1]);
		} elseif (is_object($callable) && !$callable instanceof \Closure) {
			return new ReflectionMethod($callable, '__invoke');
		} else {
			assert($callable instanceof \Closure || is_string($callable));
			return new \ReflectionFunction($callable);
		}
	}


	/**
	 * Checks whether PHP callback is function or static method.
	 */
	public static function isStatic(callable $callable): bool
	{
		return is_string(is_array($callable) ? $callable[0] : $callable);
	}


	/**
	 * Unwraps closure created by Closure::fromCallable().
	 * @return callable|array{object|class-string, string}|string
	 */
	public static function unwrap(\Closure $closure): callable|array|string
	{
		$r = new \ReflectionFunction($closure);
		$class = $r->getClosureScopeClass()?->name;
		if (str_ends_with($r->name, '}')) {
			return $closure;

		} elseif (($obj = $r->getClosureThis()) && $obj::class === $class) {
			return [$obj, $r->name];

		} elseif ($class) {
			return [$class, $r->name];

		} else {
			return $r->name;
		}
	}
}
