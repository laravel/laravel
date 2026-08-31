<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Exception;

/**
 * The resource was found but the request method is not allowed.
 *
 * This exception should trigger an HTTP 405 response in your application code.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class MethodNotAllowedException extends \RuntimeException implements ExceptionInterface
{
    protected array $allowedMethods = [];

    /**
     * @param string[] $allowedMethods
     */
    public function __construct(array $allowedMethods, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->allowedMethods = array_map('strtoupper', $allowedMethods);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the allowed HTTP methods.
     *
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
