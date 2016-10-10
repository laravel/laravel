<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster\Distribution;

/**
 * Exception class that identifies empty rings.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class EmptyRingException extends \Exception
{
}
