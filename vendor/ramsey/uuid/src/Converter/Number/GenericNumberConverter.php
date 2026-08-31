<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;

/**
 * GenericNumberConverter uses the provided calculator to convert decimal numbers to and from hexadecimal values
 *
 * @immutable
 */
class GenericNumberConverter implements NumberConverterInterface
{
    public function __construct(private CalculatorInterface $calculator)
    {
    }

    /**
     * @pure
     */
    public function fromHex(string $hex): string
    {
        /** @var non-empty-string $hex */
        return $this->calculator->fromBase($hex, 16)->toString();
    }

    /**
     * @pure
     */
    public function toHex(string $number): string
    {
        /** @phpstan-ignore return.type, possiblyImpure.new */
        return $this->calculator->toBase(new IntegerObject($number), 16);
    }
}
