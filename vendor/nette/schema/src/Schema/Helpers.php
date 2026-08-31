<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;
use Nette\Utils\Reflection;
use function count, explode, get_debug_type, implode, in_array, is_array, is_float, is_int, is_object, is_scalar, is_string, method_exists, preg_match, preg_quote, preg_replace, preg_replace_callback, settype, str_replace, strlen, trim, var_export;


/**
 * @internal
 */
final class Helpers
{
	use Nette\StaticClass;

	public const PreventMerging = '_prevent_merging';


	/**
	 * Merges dataset. Left has higher priority than right one.
	 */
	public static function merge(mixed $value, mixed $base): mixed
	{
		if (is_array($value) && isset($value[self::PreventMerging])) {
			unset($value[self::PreventMerging]);
			return $value;
		}

		if (is_array($value) && is_array($base)) {
			$index = 0;
			foreach ($value as $key => $val) {
				if ($key === $index) {
					$base[] = $val;
					$index++;
				} else {
					$base[$key] = static::merge($val, $base[$key] ?? null);
				}
			}

			return $base;

		} elseif ($value === null && is_array($base)) {
			return $base;

		} else {
			return $value;
		}
	}


	public static function getPropertyType(\ReflectionProperty|\ReflectionParameter $prop): ?string
	{
		if ($type = Nette\Utils\Type::fromReflection($prop)) {
			return (string) $type;
		} elseif (
			($prop instanceof \ReflectionProperty)
			&& ($type = preg_replace('#\s.*#', '', (string) self::parseAnnotation($prop, 'var')))
		) {
			$class = Reflection::getPropertyDeclaringClass($prop);
			return preg_replace_callback('#[\w\\\]+#', fn($m) => Reflection::expandClassName($m[0], $class), $type);
		}

		return null;
	}


	/**
	 * Returns an annotation value.
	 * @param  \ReflectionClass<object>|\ReflectionProperty  $ref
	 */
	public static function parseAnnotation(\ReflectionClass|\ReflectionProperty $ref, string $name): ?string
	{
		if (!Reflection::areCommentsAvailable()) {
			throw new Nette\InvalidStateException('You have to enable phpDoc comments in opcode cache.');
		}

		$re = '#[\s*]@' . preg_quote($name, '#') . '(?=\s|$)(?:[ \t]+([^@\s]\S*))?#';
		if ($ref->getDocComment() && preg_match($re, trim($ref->getDocComment(), '/*'), $m)) {
			return $m[1] ?? '';
		}

		return null;
	}


	public static function formatValue(mixed $value): string
	{
		if ($value instanceof DynamicParameter) {
			return 'dynamic';
		} elseif (is_object($value)) {
			return 'object ' . $value::class;
		} elseif (is_string($value)) {
			return "'" . Nette\Utils\Strings::truncate($value, 15, '...') . "'";
		} elseif (is_scalar($value)) {
			return var_export($value, return: true);
		} else {
			return get_debug_type($value);
		}
	}


	public static function validateType(mixed $value, string $expected, Context $context): void
	{
		if (!Nette\Utils\Validators::is($value, $expected)) {
			$expected = str_replace(DynamicParameter::class . '|', '', $expected);
			$expected = str_replace(['|', ':'], [' or ', ' in range '], $expected);
			$context->addError(
				'The %label% %path% expects to be %expected%, %value% given.',
				Message::TypeMismatch,
				['value' => $value, 'expected' => $expected],
			);
		}
	}


	/** @param  array{?float, ?float}  $range */
	public static function validateRange(mixed $value, array $range, Context $context, string $types = ''): void
	{
		if (is_array($value) || is_string($value)) {
			[$length, $label] = is_array($value)
				? [count($value), 'items']
				: (in_array('unicode', explode('|', $types), strict: true)
					? [Nette\Utils\Strings::length($value), 'characters']
					: [strlen($value), 'bytes']);

			if (!self::isInRange($length, $range)) {
				$context->addError(
					"The length of %label% %path% expects to be in range %expected%, %length% $label given.",
					Message::LengthOutOfRange,
					['value' => $value, 'length' => $length, 'expected' => implode('..', $range)],
				);
			}
		} elseif ((is_int($value) || is_float($value)) && !self::isInRange($value, $range)) {
			$context->addError(
				'The %label% %path% expects to be in range %expected%, %value% given.',
				Message::ValueOutOfRange,
				['value' => $value, 'expected' => implode('..', $range)],
			);
		}
	}


	/** @param  array{?float, ?float}  $range */
	public static function isInRange(mixed $value, array $range): bool
	{
		return ($range[0] === null || $value >= $range[0])
			&& ($range[1] === null || $value <= $range[1]);
	}


	public static function validatePattern(string $value, string $pattern, Context $context): void
	{
		if (!preg_match("\x01^(?:$pattern)$\x01Du", $value)) {
			$context->addError(
				"The %label% %path% expects to match pattern '%pattern%', %value% given.",
				Message::PatternMismatch,
				['value' => $value, 'pattern' => $pattern],
			);
		}
	}


	/** @return \Closure(mixed): mixed */
	public static function getCastStrategy(string $type): \Closure
	{
		if (Nette\Utils\Validators::isBuiltinType($type)) {
			return static function ($value) use ($type) {
				settype($value, $type);
				return $value;
			};
		} elseif (method_exists($type, '__construct')) {
			return static fn($value) => is_array($value) || $value instanceof \stdClass
				? new $type(...(array) $value)
				: new $type($value);
		} else {
			return static fn($value) => Nette\Utils\Arrays::toObject((array) $value, new $type);
		}
	}
}
