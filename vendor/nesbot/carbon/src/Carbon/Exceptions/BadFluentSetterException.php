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

use BadMethodCallException as BaseBadMethodCallException;
use Throwable;

class BadFluentSetterException extends BaseBadMethodCallException implements BadMethodCallException
{
    /**
     * The setter.
     *
     * @var string
     */
    protected $setter;

    /**
     * Constructor.
     *
     * @param string         $setter
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($setter, $code = 0, ?Throwable $previous = null)
    {
        $this->setter = $setter;

        parent::__construct(\sprintf("Unknown fluent setter '%s'", $setter), $code, $previous);
    }

    /**
     * Get the setter.
     *
     * @return string
     */
    public function getSetter(): string
    {
        return $this->setter;
    }
}
