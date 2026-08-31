<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;


/**
 * Validation error.
 */
class ValidationException extends Nette\InvalidStateException
{
	public function __construct(
		?string $message,
		/** @var list<Message> */
		private array $messages = [],
	) {
		parent::__construct($message ?? $messages[0]->toString());
	}


	/** @return list<string> */
	public function getMessages(): array
	{
		$res = [];
		foreach ($this->messages as $message) {
			$res[] = $message->toString();
		}

		return $res;
	}


	/** @return list<Message> */
	public function getMessageObjects(): array
	{
		return $this->messages;
	}
}
