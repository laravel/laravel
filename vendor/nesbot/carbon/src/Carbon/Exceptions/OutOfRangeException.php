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

use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;

// This will extends OutOfRangeException instead of InvalidArgumentException since 3.0.0
// use OutOfRangeException as BaseOutOfRangeException;

class OutOfRangeException extends BaseInvalidArgumentException implements InvalidArgumentException
{
    /**
     * The unit or name of the value.
     *
     * @var string
     */
    private $unit;

    /**
     * The range minimum.
     *
     * @var mixed
     */
    private $min;

    /**
     * The range maximum.
     *
     * @var mixed
     */
    private $max;

    /**
     * The invalid value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor.
     *
     * @param string         $unit
     * @param mixed          $min
     * @param mixed          $max
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($unit, $min, $max, $value, $code = 0, ?Throwable $previous = null)
    {
        $this->unit = $unit;
        $this->min = $min;
        $this->max = $max;
        $this->value = $value;

        parent::__construct("$unit must be between $min and $max, $value given", $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
