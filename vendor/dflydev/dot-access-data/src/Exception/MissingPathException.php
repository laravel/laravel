<?php

declare(strict_types=1);

/*
 * This file is a part of dflydev/dot-access-data.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dflydev\DotAccessData\Exception;

use Throwable;

/**
 * Thrown when trying to access a path that does not exist
 */
class MissingPathException extends DataException
{
    /** @var string */
    protected $path;

    public function __construct(string $path, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->path = $path;

        parent::__construct($message, $code, $previous);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
