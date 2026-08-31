<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;
use Nette\Schema\Elements\AnyOf;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Elements\Type;
use function is_object;


/**
 * Schema generator.
 *
 * @method static Type scalar($default = null)
 * @method static Type string($default = null)
 * @method static Type int($default = null)
 * @method static Type float($default = null)
 * @method static Type bool($default = null)
 * @method static Type null()
 * @method static Type list($default = [])
 * @method static Type mixed($default = null)
 * @method static Type email($default = null)
 * @method static Type unicode($default = null)
 */
final class Expect
{
	/** @param  list<mixed>  $args */
	public static function __callStatic(string $name, array $args): Type
	{
		$type = new Type($name);
		if ($args) {
			$type->default($args[0]);
		}

		return $type;
	}


	public static function type(string $type): Type
	{
		return new Type($type);
	}


	public static function anyOf(mixed ...$set): AnyOf
	{
		return new AnyOf(...$set);
	}


	/** @param Schema[]  $shape */
	public static function structure(array $shape): Structure
	{
		return new Structure($shape);
	}


	/** @param  array<string, Schema>  $items */
	public static function from(object $object, array $items = []): Structure
	{
		$ro = new \ReflectionObject($object);
		$props = $ro->hasMethod('__construct')
			? $ro->getMethod('__construct')->getParameters()
			: $ro->getProperties();

		foreach ($props as $prop) {
			$name = $prop->getName();
			if (!isset($items[$name])) {
				$type = Helpers::getPropertyType($prop) ?? 'mixed';
				$item = new Type($type);
				if ($prop instanceof \ReflectionProperty ? $prop->isInitialized($object) : $prop->isOptional()) {
					$def = ($prop instanceof \ReflectionProperty ? $prop->getValue($object) : $prop->getDefaultValue());
					if (is_object($def)) {
						$item = static::from($def);
					} elseif ($def === null && !Nette\Utils\Validators::is(null, $type)) {
						$item->required();
					} else {
						$item->default($def);
					}
				} else {
					$item->required();
				}
				$items[$name] = $item;
			}
		}

		return (new Structure($items))->castTo($ro->getName());
	}


	/**
	 * @param  mixed[]  $shape
	 */
	public static function array(?array $shape = []): Structure|Type
	{
		$shape ??= [];
		return Nette\Utils\Arrays::first($shape) instanceof Schema
			? (new Structure($shape))->castTo('array')
			: (new Type('array'))->default($shape);
	}


	public static function arrayOf(string|Schema $valueType, string|Schema|null $keyType = null): Type
	{
		return (new Type('array'))->items($valueType, $keyType);
	}


	public static function listOf(string|Schema $type): Type
	{
		return (new Type('list'))->items($type);
	}
}
