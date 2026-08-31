<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Exception;

/**
 * @author Ben Ramsey <ben@benramsey.com>
 */
class ConflictHttpException extends HttpException
{
    public function __construct(string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(409, $message, $previous, $headers, $code);
    }
}
