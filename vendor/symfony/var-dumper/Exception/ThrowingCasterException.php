<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Exception;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ThrowingCasterException extends \Exception
{
    /**
     * @param \Throwable $prev The exception thrown from the caster
     */
    public function __construct(\Throwable $prev)
    {
        parent::__construct('Unexpected '.$prev::class.' thrown from a caster: '.$prev->getMessage(), 0, $prev);
    }
}
