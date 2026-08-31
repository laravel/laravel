<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function array_map, array_search, array_splice, array_values, count, explode, implode, is_a, is_resource, is_string, strcasecmp, strtolower, substr, trim;


/**
 * PHP type reflection.
 */
final readonly class Type
{
	/** @var list<string|self> */
	private array $types;
	private ?string $singleName;
	private string $kind; // | &


	/**
	 * Creates a Type object based on reflection. Resolves self, static and parent to the actual class name.
	 * If the subject has no type, it returns null.
	 */
	public static function fromReflection(
		\ReflectionFunctionAbstract|\ReflectionParameter|\ReflectionProperty $reflection,
	): ?self
	{
		$type = $reflection instanceof \ReflectionFunctionAbstract
			? $reflection->getReturnType() ?? ($reflection instanceof \ReflectionMethod ? $reflection->getTentativeReturnType() : null)
			: $reflection->getType();

		return $type ? self::fromReflectionType($type, $reflection, asObject: true) : null;
	}


	/** @return ($asObject is true ? self : self|string) */
	private static function fromReflectionType(
		\ReflectionType $type,
		\ReflectionFunctionAbstract|\ReflectionParameter|\ReflectionProperty $of,
		bool $asObject,
	): self|string
	{
		if ($type instanceof \ReflectionNamedType) {
			$name = self::resolve($type->getName(), $of);
			return $asObject
				? new self($type->allowsNull() && $name !== 'mixed' ? [$name, 'null'] : [$name])
				: $name;

		} elseif ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
			return new self(
				array_map(fn($t) => self::fromReflectionType($t, $of, asObject: false), $type->getTypes()),
				$type instanceof \ReflectionUnionType ? '|' : '&',
			);

		} else {
			throw new Nette\InvalidStateException('Unexpected type of ' . Reflection::toString($of));
		}
	}


	/**
	 * Creates the Type object according to the text notation.
	 */
	public static function fromString(string $type): self
	{
		if (!Validators::isTypeDeclaration($type)) {
			throw new Nette\InvalidArgumentException("Invalid type '$type'.");
		}

		if ($type[0] === '?') {
			return new self([substr($type, 1), 'null']);
		}

		$unions = [];
		foreach (explode('|', $type) as $part) {
			$part = explode('&', trim($part, '()'));
			$unions[] = count($part) === 1 ? $part[0] : new self($part, '&');
		}

		return count($unions) === 1 && $unions[0] instanceof self
			? $unions[0]
			: new self($unions);
	}


	/**
	 * Creates a Type object based on the actual type of value.
	 */
	public static function fromValue(mixed $value): self
	{
		$type = get_debug_type($value);
		if (is_resource($value)) {
			$type = 'mixed';
		} elseif (str_ends_with($type, '@anonymous')) {
			$parent = substr($type, 0, -10);
			$type = $parent === 'class' ? 'object' : $parent;
		}

		return new self([$type]);
	}


	/**
	 * Resolves 'self', 'static' and 'parent' to the actual class name.
	 */
	public static function resolve(
		string $type,
		\ReflectionFunction|\ReflectionMethod|\ReflectionParameter|\ReflectionProperty $of,
	): string
	{
		$lower = strtolower($type);
		if ($of instanceof \ReflectionFunction) {
			return $type;
		}

		$class = $of->getDeclaringClass();
		if ($class === null) {
			return $type;
		} elseif ($lower === 'self') {
			return $class->name;
		} elseif ($lower === 'static') {
			return ($of instanceof ReflectionMethod ? $of->getOriginalClass() : $class)->name;
		} elseif ($lower === 'parent' && $class->getParentClass()) {
			return $class->getParentClass()->name;
		} else {
			return $type;
		}
	}


	/** @param  array<string|self>  $types */
	private function __construct(array $types, string $kind = '|')
	{
		$o = array_search('null', $types, strict: true);
		if ($o !== false) { // null as last
			array_splice($types, (int) $o, 1);
			$types[] = 'null';
		}

		$this->types = array_values($types);
		$this->singleName = is_string($types[0]) && ($types[1] ?? 'null') === 'null' ? $types[0] : null;
		$this->kind = count($types) > 1 ? $kind : '';
	}


	public function __toString(): string
	{
		$multi = count($this->types) > 1;
		if ($this->singleName !== null) {
			return ($multi ? '?' : '') . $this->singleName;
		}

		$res = [];
		foreach ($this->types as $type) {
			$res[] = $type instanceof self && $multi ? "($type)" : $type;
		}
		return implode($this->kind, $res);
	}


	/**
	 * Returns a union type that accepts both the current type and the given type.
	 */
	public function with(string|self $type): self
	{
		$type = is_string($type) ? self::fromString($type) : $type;
		return match (true) {
			$this->allows($type) => $this,
			$type->allows($this) => $type,
			default => new self(array_unique(
				array_merge($this->isIntersection() ? [$this] : $this->types, $type->isIntersection() ? [$type] : $type->types),
				SORT_REGULAR,
			), '|'),
		};
	}


	/**
	 * Returns the array of subtypes that make up the compound type as strings.
	 * @return list<string|array<string|array<mixed>>>
	 */
	public function getNames(): array
	{
		return array_map(fn($t) => $t instanceof self ? $t->getNames() : $t, $this->types);
	}


	/**
	 * Returns the array of subtypes that make up the compound type as Type objects.
	 * @return list<self>
	 */
	public function getTypes(): array
	{
		return array_map(fn($t) => $t instanceof self ? $t : new self([$t]), $this->types);
	}


	/**
	 * Returns the type name for simple types, otherwise null.
	 */
	public function getSingleName(): ?string
	{
		return $this->singleName;
	}


	/**
	 * Returns true whether it is a union type.
	 */
	public function isUnion(): bool
	{
		return $this->kind === '|';
	}


	/**
	 * Returns true whether it is an intersection type.
	 */
	public function isIntersection(): bool
	{
		return $this->kind === '&';
	}


	/**
	 * Checks whether it is a simple (non-compound) type. Single nullable types such as ?int are also considered simple.
	 */
	public function isSimple(): bool
	{
		return $this->singleName !== null;
	}


	#[\Deprecated('use isSimple()')]
	public function isSingle(): bool
	{
		return $this->singleName !== null;
	}


	/**
	 * Checks whether it is a simple PHP built-in type (int, string, bool, etc.).
	 */
	public function isBuiltin(): bool
	{
		return $this->singleName !== null && Validators::isBuiltinType($this->singleName);
	}


	/**
	 * Checks whether it is a simple class or interface name (not a built-in type).
	 */
	public function isClass(): bool
	{
		return $this->singleName !== null && !Validators::isBuiltinType($this->singleName);
	}


	/**
	 * Determines if type is special class name self/parent/static.
	 */
	public function isClassKeyword(): bool
	{
		return $this->singleName !== null && Validators::isClassKeyword($this->singleName);
	}


	/**
	 * Checks whether a value of the given type could be assigned to this type.
	 */
	public function allows(string|self $type): bool
	{
		if ($this->types === ['mixed']) {
			return true;
		}

		$type = is_string($type) ? self::fromString($type) : $type;
		return $type->isUnion()
			? Arrays::every($type->types, fn($t) => $this->allowsAny($t instanceof self ? $t->types : [$t]))
			: $this->allowsAny($type->types);
	}


	/** @param array<string>  $givenTypes */
	private function allowsAny(array $givenTypes): bool
	{
		return $this->isUnion()
			? Arrays::some($this->types, fn($t) => $this->allowsAll($t instanceof self ? $t->types : [$t], $givenTypes))
			: $this->allowsAll($this->types, $givenTypes);
	}


	/**
	 * @param array<string>  $ourTypes
	 * @param array<string>  $givenTypes
	 */
	private function allowsAll(array $ourTypes, array $givenTypes): bool
	{
		return Arrays::every(
			$ourTypes,
			fn(string $ourType) => Arrays::some(
				$givenTypes,
				fn(string $givenType) => Validators::isBuiltinType($ourType)
					? strcasecmp($ourType, $givenType) === 0
					: is_a($givenType, $ourType, allow_string: true),
			),
		);
	}
}
