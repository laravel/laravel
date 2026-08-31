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

namespace Ramsey\Uuid\Provider;

use Ramsey\Uuid\Type\Time;

/**
 * A time provider retrieves the current time
 */
interface TimeProviderInterface
{
    /**
     * Returns a time object
     */
    public function getTime(): Time;
}
