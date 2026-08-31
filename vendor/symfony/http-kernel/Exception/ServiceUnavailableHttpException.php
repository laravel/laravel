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
class ServiceUnavailableHttpException extends HttpException
{
    /**
     * @param int|string|null $retryAfter The number of seconds or HTTP-date after which the request may be retried
     */
    public function __construct(int|string|null $retryAfter = null, string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        if ($retryAfter) {
            $headers['Retry-After'] = $retryAfter;
        }

        parent::__construct(503, $message, $previous, $headers, $code);
    }
}
