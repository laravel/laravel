<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Exception;

class UnexpectedTargetException extends RuntimeException
{
    /** @var mixed */
    private $target;

    /**
     * @param mixed           $target
     * @param string          $message  (default: "")
     * @param int             $code     (default: 0)
     * @param \Throwable|null $previous (default: null)
     */
    public function __construct($target, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->target = $target;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }
}
