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

namespace Ramsey\Uuid\Generator;

use function uuid_create;
use function uuid_parse;

use const UUID_TYPE_RANDOM;

/**
 * PeclUuidRandomGenerator generates strings of random binary data using ext-uuid
 *
 * @link https://pecl.php.net/package/uuid ext-uuid
 */
class PeclUuidRandomGenerator implements RandomGeneratorInterface
{
    public function generate(int $length): string
    {
        $uuid = uuid_create(UUID_TYPE_RANDOM);

        return (string) uuid_parse($uuid);
    }
}
