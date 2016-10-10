<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

/**
 * Defines a cluster of Redis servers formed by aggregating multiple
 * connection objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ClusterConnectionInterface extends AggregatedConnectionInterface
{
}
