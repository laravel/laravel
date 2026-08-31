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

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Type\Time;

use function gettimeofday;

/**
 * SystemTimeProvider retrieves the current time using built-in PHP functions
 */
class SystemTimeProvider implements TimeProviderInterface
{
    public function getTime(): Time
    {
        $time = gettimeofday();

        return new Time($time['sec'], $time['usec']);
    }
}
