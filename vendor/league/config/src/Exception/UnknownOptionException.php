<?php

declare(strict_types=1);

/*
 * This file is part of the league/config package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Config\Exception;

use Throwable;

final class UnknownOptionException extends \InvalidArgumentException implements ConfigurationExceptionInterface
{
    private string $path;

    public function __construct(string $message, string $path, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
