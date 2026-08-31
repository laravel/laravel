<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Represents a backtrace as returned by debug_backtrace() or Exception->getTrace().
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TraceStub extends Stub
{
    public function __construct(
        array $trace,
        public bool $keepArgs = true,
        public int $sliceOffset = 0,
        public ?int $sliceLength = null,
        public int $numberingOffset = 0,
    ) {
        $this->value = $trace;
    }
}
