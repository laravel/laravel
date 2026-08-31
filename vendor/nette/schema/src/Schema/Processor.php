<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;


/**
 * Schema validator.
 */
final class Processor
{
	/** @var list<\Closure(Context): void> */
	public array $onNewContext = [];
	private Context $context;
	private bool $skipDefaults = false;


	public function skipDefaults(bool $value = true): void
	{
		$this->skipDefaults = $value;
	}


	/**
	 * Normalizes and validates data. Result is a clean completed data.
	 * @throws ValidationException
	 */
	public function process(Schema $schema, mixed $data): mixed
	{
		$this->createContext();
		$data = $schema->normalize($data, $this->context);
		$this->throwsErrors();
		$data = $schema->complete($data, $this->context);
		$this->throwsErrors();
		return $data;
	}


	/**
	 * Normalizes and validates and merges multiple data. Result is a clean completed data.
	 * @param  list<mixed>  $dataset
	 * @throws ValidationException
	 */
	public function processMultiple(Schema $schema, array $dataset): mixed
	{
		$this->createContext();
		$flatten = null;
		$first = true;
		foreach ($dataset as $data) {
			$data = $schema->normalize($data, $this->context);
			$this->throwsErrors();
			$flatten = $first ? $data : $schema->merge($data, $flatten);
			$first = false;
		}

		$data = $schema->complete($flatten, $this->context);
		$this->throwsErrors();
		return $data;
	}


	/** @return list<string> */
	public function getWarnings(): array
	{
		$res = [];
		foreach ($this->context->warnings as $message) {
			$res[] = $message->toString();
		}

		return $res;
	}


	private function throwsErrors(): void
	{
		if ($this->context->errors) {
			throw new ValidationException(null, $this->context->errors);
		}
	}


	private function createContext(): void
	{
		$this->context = new Context;
		$this->context->skipDefaults = $this->skipDefaults;
		Nette\Utils\Arrays::invoke($this->onNewContext, $this->context);
	}
}
