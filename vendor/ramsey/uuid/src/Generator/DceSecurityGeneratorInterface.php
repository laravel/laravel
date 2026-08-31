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

use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;

/**
 * A DCE Security generator generates strings of binary data based on a local domain, local identifier, node ID, clock
 * sequence, and the current time
 *
 * @see UuidV2
 */
interface DceSecurityGeneratorInterface
{
    /**
     * Generate a binary string from a local domain, local identifier, node ID, clock sequence, and current time
     *
     * @param int $localDomain The local domain to use when generating bytes, according to DCE Security
     * @param IntegerObject | null $localIdentifier The local identifier for the given domain; this may be a UID or GID
     *     on POSIX systems if the local domain is "person" or "group," or it may be a site-defined identifier if the
     *     local domain is "org"
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return string A binary string
     */
    public function generate(
        int $localDomain,
        ?IntegerObject $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null,
    ): string;
}
