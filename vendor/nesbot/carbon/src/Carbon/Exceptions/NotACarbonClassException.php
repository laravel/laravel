<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Exceptions;

use Carbon\CarbonInterface;
use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;

class NotACarbonClassException extends BaseInvalidArgumentException implements InvalidArgumentException
{
    /**
     * The className.
     *
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string         $className
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($className, $code = 0, ?Throwable $previous = null)
    {
        $this->className = $className;

        parent::__construct(\sprintf(
            'Given class does not implement %s: %s',
            CarbonInterface::class,
            $className,
        ), $code, $previous);
    }

    /**
     * Get the className.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}
