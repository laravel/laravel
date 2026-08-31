<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use function implode, preg_replace_callback;


final class Message
{
	/** variables: {value: mixed, expected: string} */
	public const TypeMismatch = 'schema.typeMismatch';

	/** variables: {value: mixed, expected: string} */
	public const ValueOutOfRange = 'schema.valueOutOfRange';

	/** variables: {value: mixed, length: int, expected: string} */
	public const LengthOutOfRange = 'schema.lengthOutOfRange';

	/** variables: {value: string, pattern: string} */
	public const PatternMismatch = 'schema.patternMismatch';

	/** variables: {value: mixed, assertion: string} */
	public const FailedAssertion = 'schema.failedAssertion';

	/** no variables */
	public const MissingItem = 'schema.missingItem';

	/** variables: {hint: string} */
	public const UnexpectedItem = 'schema.unexpectedItem';

	/** no variables */
	public const Deprecated = 'schema.deprecated';

	/** @deprecated use Message::TypeMismatch */
	public const TYPE_MISMATCH = self::TypeMismatch;

	/** @deprecated use Message::ValueOutOfRange */
	public const VALUE_OUT_OF_RANGE = self::ValueOutOfRange;

	/** @deprecated use Message::LengthOutOfRange */
	public const LENGTH_OUT_OF_RANGE = self::LengthOutOfRange;

	/** @deprecated use Message::PatternMismatch */
	public const PATTERN_MISMATCH = self::PatternMismatch;

	/** @deprecated use Message::FailedAssertion */
	public const FAILED_ASSERTION = self::FailedAssertion;

	/** @deprecated use Message::MissingItem */
	public const MISSING_ITEM = self::MissingItem;

	/** @deprecated use Message::UnexpectedItem */
	public const UNEXPECTED_ITEM = self::UnexpectedItem;

	/** @deprecated use Message::Deprecated */
	public const DEPRECATED = self::Deprecated;


	public function __construct(
		public string $message,
		public string $code,
		/** @var list<int|string> */
		public array $path,
		/** @var array<string, mixed> */
		public array $variables = [],
	) {
	}


	public function toString(): string
	{
		$vars = $this->variables;
		$vars['label'] = empty($vars['isKey']) ? 'item' : 'key of item';
		$vars['path'] = $this->path
			? "'" . implode("\u{a0}›\u{a0}", $this->path) . "'"
			: null;
		$vars['value'] = Helpers::formatValue($vars['value'] ?? null);

		return preg_replace_callback('~( ?)%(\w+)%~', function ($m) use ($vars) {
			[, $space, $key] = $m;
			return $vars[$key] === null ? '' : $space . $vars[$key];
		}, $this->message);
	}
}
