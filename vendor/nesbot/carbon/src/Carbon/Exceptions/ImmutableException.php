<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Exceptions;

use RuntimeException as BaseRuntimeException;
use Throwable;

/**
 * @final
 */
class ImmutableException extends BaseRuntimeException implements RuntimeException
{
    protected string $value;

    /**
     * Constructor.
     *
     * @param string         $value    the immutable message
     * @param int            $code
     * @param Throwable|null $previous
     *
     * @deprecated Use ImmutableException::fromClass() or ImmutableException::fromMethod() to construct an
     *             ImmutableException instance.
     */
    public function __construct($message, int $code = 0, ?Throwable $previous = null, ?string $value = null)
    {
        $this->value ??= $value ?? $message;

        parent::__construct($message, $code, $previous);
    }

    public static function fromClass(string $class, int $code = 0, ?Throwable $previous = null): self
    {
        return new self("$class class is immutable.", $code, $previous, "$class class");
    }

    public static function fromMethod(string $class, string $method, int $code = 0, ?Throwable $previous = null): self
    {
        return new self("$method not allowed on $class.", $code, $previous, "$class::$method method");
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
