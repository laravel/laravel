<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpTransportException extends TransportException
{
    public function __construct(
        string $message,
        private ResponseInterface $response,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
