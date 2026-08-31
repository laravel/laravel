<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\Error;

class ClassNotFoundError extends \Error
{
    public function __construct(string $message, \Throwable $previous)
    {
        parent::__construct($message, $previous->getCode(), $previous->getPrevious());

        foreach ([
            'file' => $previous->getFile(),
            'line' => $previous->getLine(),
            'trace' => $previous->getTrace(),
        ] as $property => $value) {
            $refl = new \ReflectionProperty(\Error::class, $property);
            $refl->setValue($this, $value);
        }
    }
}
