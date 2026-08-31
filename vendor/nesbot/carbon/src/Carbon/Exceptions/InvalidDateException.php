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

class InvalidDateException extends BaseInvalidArgumentException implements InvalidArgumentException
{
    /**
     * The invalid field.
     *
     * @var string
     */
    private $field;

    /**
     * The invalid value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor.
     *
     * @param string         $field
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($field, $value, $code = 0, ?Throwable $previous = null)
    {
        $this->field = $field;
        $this->value = $value;
        parent::__construct($field.' : '.$value.' is not a valid value.', $code, $previous);
    }

    /**
     * Get the invalid field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get the invalid value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
