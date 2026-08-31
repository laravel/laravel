<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Exceptions;

use BackedEnum;
use League\Uri\Idna\Error;
use League\Uri\Idna\Result;
use Stringable;

final class ConversionFailed extends SyntaxError
{
    private function __construct(
        string $message,
        private readonly string $host,
        private readonly Result $result
    ) {
        parent::__construct($message);
    }

    public static function dueToIdnError(BackedEnum|Stringable|string $host, Result $result): self
    {
        $reasons = array_map(fn (Error $error): string => $error->description(), $result->errors());

        if ($host instanceof BackedEnum) {
            $host = (string) $host->value;
        }

        return new self('Host `'.$host.'` is invalid: '.implode('; ', $reasons).'.', (string) $host, $result);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getResult(): Result
    {
        return $this->result;
    }
}
