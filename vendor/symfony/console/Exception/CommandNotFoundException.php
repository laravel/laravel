<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Exception;

/**
 * Represents an incorrect command name typed in the console.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class CommandNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @param string          $message      Exception message to throw
     * @param string[]        $alternatives List of similar defined names
     * @param int             $code         Exception code
     * @param \Throwable|null $previous     Previous exception used for the exception chaining
     */
    public function __construct(
        string $message,
        private array $alternatives = [],
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string[]
     */
    public function getAlternatives(): array
    {
        return $this->alternatives;
    }
}
