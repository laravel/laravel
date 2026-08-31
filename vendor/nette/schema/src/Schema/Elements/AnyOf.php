<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema\Elements;

use Nette;
use Nette\Schema\Context;
use Nette\Schema\Helpers;
use Nette\Schema\Schema;
use function array_merge, array_unique, implode, is_array;


final class AnyOf implements Schema
{
	use Base;

	/** @var mixed[] */
	private array $set;


	public function __construct(mixed ...$set)
	{
		if (!$set) {
			throw new Nette\InvalidStateException('The enumeration must not be empty.');
		}

		$this->set = $set;
	}


	public function firstIsDefault(): self
	{
		$this->default = $this->set[0];
		return $this;
	}


	public function nullable(): self
	{
		$this->set[] = null;
		return $this;
	}


	public function dynamic(): self
	{
		$this->set[] = new Type(Nette\Schema\DynamicParameter::class);
		return $this;
	}


	/********************* processing ****************d*g**/


	public function normalize(mixed $value, Context $context): mixed
	{
		return $this->doNormalize($value, $context);
	}


	public function merge(mixed $value, mixed $base): mixed
	{
		if (is_array($value) && isset($value[Helpers::PreventMerging])) {
			unset($value[Helpers::PreventMerging]);
			return $value;
		}

		return Helpers::merge($value, $base);
	}


	public function complete(mixed $value, Context $context): mixed
	{
		$isOk = $context->createChecker();
		$value = $this->findAlternative($value, $context);
		$isOk() && $value = $this->doTransform($value, $context);
		return $isOk() ? $value : null;
	}


	private function findAlternative(mixed $value, Context $context): mixed
	{
		$expecteds = $innerErrors = [];
		foreach ($this->set as $item) {
			if ($item instanceof Schema) {
				$dolly = new Context;
				$dolly->path = $context->path;
				$res = $item->complete($item->normalize($value, $dolly), $dolly);
				if (!$dolly->errors) {
					$context->warnings = array_merge($context->warnings, $dolly->warnings);
					return $res;
				}

				foreach ($dolly->errors as $error) {
					if ($error->path !== $context->path || empty($error->variables['expected'])) {
						$innerErrors[] = $error;
					} else {
						$expecteds[] = $error->variables['expected'];
					}
				}
			} else {
				if ($item === $value) {
					return $value;
				}

				$expecteds[] = Nette\Schema\Helpers::formatValue($item);
			}
		}

		if ($innerErrors) {
			$context->errors = array_merge($context->errors, $innerErrors);
		} else {
			$context->addError(
				'The %label% %path% expects to be %expected%, %value% given.',
				Nette\Schema\Message::TypeMismatch,
				[
					'value' => $value,
					'expected' => implode('|', array_unique($expecteds)),
				],
			);
		}

		return null;
	}


	public function completeDefault(Context $context): mixed
	{
		if ($this->required) {
			$context->addError(
				'The mandatory item %path% is missing.',
				Nette\Schema\Message::MissingItem,
			);
			return null;
		}

		if ($this->default instanceof Schema) {
			return $this->default->completeDefault($context);
		}

		return $this->default;
	}
}
