<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function constant, current, defined, end, explode, file_get_contents, implode, ltrim, next, ord, strrchr, strtolower, substr;
use const T_AS, T_CLASS, T_COMMENT, T_CURLY_OPEN, T_DOC_COMMENT, T_DOLLAR_OPEN_CURLY_BRACES, T_ENUM, T_INTERFACE, T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED, T_NAMESPACE, T_NS_SEPARATOR, T_STRING, T_TRAIT, T_USE, T_WHITESPACE, TOKEN_PARSE;


/**
 * PHP reflection helpers.
 */
final class Reflection
{
	use Nette\StaticClass;

	/** @deprecated use Nette\Utils\Validators::isBuiltinType() */
	public static function isBuiltinType(string $type): bool
	{
		return Validators::isBuiltinType($type);
	}


	#[\Deprecated('use Nette\Utils\Validators::isClassKeyword()')]
	public static function isClassKeyword(string $name): bool
	{
		return Validators::isClassKeyword($name);
	}


	/**
	 * Returns the default value of a parameter. Resolves constants and class constants used as default values.
	 * @throws \ReflectionException if the constant cannot be resolved
	 */
	public static function getParameterDefaultValue(\ReflectionParameter $param): mixed
	{
		if ($param->isDefaultValueConstant()) {
			$const = $orig = $param->getDefaultValueConstantName() ?? throw new Nette\ShouldNotHappenException;
			$pair = explode('::', $const);
			if (isset($pair[1])) {
				$pair[0] = Type::resolve($pair[0], $param);
				try {
					$rcc = new \ReflectionClassConstant($pair[0], $pair[1]);
				} catch (\ReflectionException $e) {
					$name = self::toString($param);
					throw new \ReflectionException("Unable to resolve constant $orig used as default value of $name.", 0, $e);
				}

				return $rcc->getValue();

			} elseif (!defined($const)) {
				$const = substr((string) strrchr($const, '\\'), 1);
				if (!defined($const)) {
					$name = self::toString($param);
					throw new \ReflectionException("Unable to resolve constant $orig used as default value of $name.");
				}
			}

			return constant($const);
		}

		return $param->getDefaultValue();
	}


	/**
	 * Returns a reflection of a class or trait that contains a declaration of given property. Property can also be declared in the trait.
	 * @return \ReflectionClass<object>
	 */
	public static function getPropertyDeclaringClass(\ReflectionProperty $prop): \ReflectionClass
	{
		foreach ($prop->getDeclaringClass()->getTraits() as $trait) {
			if ($trait->hasProperty($prop->name)
				// doc-comment guessing as workaround for insufficient PHP reflection
				&& $trait->getProperty($prop->name)->getDocComment() === $prop->getDocComment()
			) {
				return self::getPropertyDeclaringClass($trait->getProperty($prop->name));
			}
		}

		return $prop->getDeclaringClass();
	}


	/**
	 * Returns a reflection of a method that contains a declaration of $method.
	 * Usually, each method is its own declaration, but the body of the method can also be in the trait and under a different name.
	 */
	public static function getMethodDeclaringMethod(\ReflectionMethod $method): \ReflectionMethod
	{
		// file & line guessing as workaround for insufficient PHP reflection
		$decl = $method->getDeclaringClass();
		if ($decl->getFileName() === $method->getFileName()
			&& $decl->getStartLine() <= $method->getStartLine()
			&& $decl->getEndLine() >= $method->getEndLine()
		) {
			return $method;
		}

		$hash = [$method->getFileName(), $method->getStartLine(), $method->getEndLine()];
		if (($alias = $decl->getTraitAliases()[$method->name] ?? null)
			&& ($m = new \ReflectionMethod(...explode('::', $alias, 2)))
			&& $hash === [$m->getFileName(), $m->getStartLine(), $m->getEndLine()]
		) {
			return self::getMethodDeclaringMethod($m);
		}

		foreach ($decl->getTraits() as $trait) {
			if ($trait->hasMethod($method->name)
				&& ($m = $trait->getMethod($method->name))
				&& $hash === [$m->getFileName(), $m->getStartLine(), $m->getEndLine()]
			) {
				return self::getMethodDeclaringMethod($m);
			}
		}

		return $method;
	}


	/**
	 * Finds out if reflection has access to PHPdoc comments. Comments may not be available due to the opcode cache.
	 */
	public static function areCommentsAvailable(): bool
	{
		static $res;
		return $res ?? $res = (bool) (new \ReflectionMethod(self::class, __FUNCTION__))->getDocComment();
	}


	/**
	 * Returns a human-readable string representation of a reflection object.
	 */
	public static function toString(\Reflector $ref): string
	{
		if ($ref instanceof \ReflectionClass) {
			return $ref->name;
		} elseif ($ref instanceof \ReflectionMethod) {
			return $ref->getDeclaringClass()->name . '::' . $ref->name . '()';
		} elseif ($ref instanceof \ReflectionFunction) {
			return $ref->isAnonymous() ? '{closure}()' : $ref->name . '()';
		} elseif ($ref instanceof \ReflectionProperty) {
			return self::getPropertyDeclaringClass($ref)->name . '::$' . $ref->name;
		} elseif ($ref instanceof \ReflectionParameter) {
			return '$' . $ref->name . ' in ' . self::toString($ref->getDeclaringFunction());
		} else {
			throw new Nette\InvalidArgumentException;
		}
	}


	/**
	 * Expands the name of the class to full name in the given context of given class.
	 * Thus, it returns how the PHP parser would understand $name if it were written in the body of the class $context.
	 * @param  \ReflectionClass<object>  $context
	 * @throws Nette\InvalidArgumentException
	 */
	public static function expandClassName(string $name, \ReflectionClass $context): string
	{
		$lower = strtolower($name);
		if (empty($name)) {
			throw new Nette\InvalidArgumentException('Class name must not be empty.');

		} elseif (Validators::isBuiltinType($lower)) {
			return $lower;

		} elseif ($lower === 'self' || $lower === 'static') {
			return $context->name;

		} elseif ($lower === 'parent') {
			return $context->getParentClass()
				? $context->getParentClass()->name
				: 'parent';

		} elseif ($name[0] === '\\') { // fully qualified name
			return ltrim($name, '\\');
		}

		$uses = self::getUseStatements($context);
		$parts = explode('\\', $name, 2);
		if (isset($uses[$parts[0]])) {
			$parts[0] = $uses[$parts[0]];
			return implode('\\', $parts);

		} elseif ($context->inNamespace()) {
			return $context->getNamespaceName() . '\\' . $name;

		} else {
			return $name;
		}
	}


	/**
	 * Returns the use statements from the file where the class is defined.
	 * @param  \ReflectionClass<object>  $class
	 * @return array<string, class-string>  Map of alias to fully qualified class name
	 */
	public static function getUseStatements(\ReflectionClass $class): array
	{
		if ($class->isAnonymous()) {
			throw new Nette\NotImplementedException('Anonymous classes are not supported.');
		}

		static $cache = [];
		if (!isset($cache[$name = $class->name])) {
			if ($class->isInternal()) {
				$cache[$name] = [];
			} else {
				$code = (string) file_get_contents((string) $class->getFileName());
				$cache = self::parseUseStatements($code, $name) + $cache;
			}
		}

		return $cache[$name];
	}


	/**
	 * Parses PHP code to [class => [alias => class, ...]]
	 * @return array<string, array<string, string>>
	 */
	private static function parseUseStatements(string $code, ?string $forClass = null): array
	{
		try {
			$tokens = \PhpToken::tokenize($code, TOKEN_PARSE);
		} catch (\ParseError $e) {
			trigger_error($e->getMessage());
			$tokens = [];
		}

		$namespace = $class = null;
		$classLevel = $level = 0;
		$res = $uses = [];

		$nameTokens = [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED];

		while ($token = current($tokens)) {
			next($tokens);
			switch ($token->id) {
				case T_NAMESPACE:
					$namespace = ltrim(self::fetch($tokens, $nameTokens) . '\\', '\\');
					$uses = [];
					break;

				case T_CLASS:
				case T_INTERFACE:
				case T_TRAIT:
				case T_ENUM:
					if ($name = self::fetch($tokens, T_STRING)) {
						$class = $namespace . $name;
						$classLevel = $level + 1;
						$res[$class] = $uses;
						if ($class === $forClass) {
							return $res;
						}
					}

					break;

				case T_USE:
					while (!$class && ($name = self::fetch($tokens, $nameTokens))) {
						$name = ltrim($name, '\\');
						if (self::fetch($tokens, '{')) {
							while ($suffix = self::fetch($tokens, $nameTokens)) {
								if (self::fetch($tokens, T_AS) && ($alias = self::fetch($tokens, T_STRING))) {
									$uses[$alias] = $name . $suffix;
								} else {
									$tmp = explode('\\', $suffix);
									$uses[end($tmp)] = $name . $suffix;
								}

								if (!self::fetch($tokens, ',')) {
									break;
								}
							}
						} elseif (self::fetch($tokens, T_AS) && ($alias = self::fetch($tokens, T_STRING))) {
							$uses[$alias] = $name;

						} else {
							$tmp = explode('\\', $name);
							$uses[end($tmp)] = $name;
						}

						if (!self::fetch($tokens, ',')) {
							break;
						}
					}

					break;

				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
				case ord('{'):
					$level++;
					break;

				case ord('}'):
					if ($level === $classLevel) {
						$class = $classLevel = 0;
					}

					$level--;
			}
		}

		return $res;
	}


	/**
	 * @param  \PhpToken[]  $tokens
	 * @param  string|int|int[]  $take
	 */
	private static function fetch(array &$tokens, string|int|array $take): ?string
	{
		$res = null;
		while ($token = current($tokens)) {
			if ($token->is($take)) {
				$res .= $token->text;
			} elseif (!$token->is([T_DOC_COMMENT, T_WHITESPACE, T_COMMENT])) {
				break;
			}

			next($tokens);
		}

		return $res;
	}
}
