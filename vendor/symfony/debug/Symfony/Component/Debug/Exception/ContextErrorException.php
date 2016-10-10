<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Exception;

/**
 * Error Exception with Variable Context.
 *
 * @author Christian Sciberras <uuf6429@gmail.com>
 */
class ContextErrorException extends \ErrorException
{
    private $context = array();

    public function __construct($message, $code, $severity, $filename, $lineno, $context = array())
    {
        parent::__construct($message, $code, $severity, $filename, $lineno);
        $this->context = $context;
    }

    /**
     * @return array Array of variables that existed when the exception occurred
     */
    public function getContext()
    {
        return $this->context;
    }
}
