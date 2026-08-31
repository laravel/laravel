<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid;

/**
 * A v4 UUID contains a 122-bit random number.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class UuidV4 extends Uuid
{
    protected const TYPE = 4;

    public function __construct(?string $uuid = null)
    {
        if (null === $uuid) {
            // Generate 36 random hex characters (144 bits)
            // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
            $uuid = bin2hex(random_bytes(18));
            // Insert dashes to match the UUID format
            // xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
            $uuid[8] = $uuid[13] = $uuid[18] = $uuid[23] = '-';
            // Set the UUID version to 4
            // xxxxxxxx-xxxx-4xxx-xxxx-xxxxxxxxxxxx
            $uuid[14] = '4';
            // Set the UUID variant: the 19th char must be in [8, 9, a, b]
            // xxxxxxxx-xxxx-4xxx-?xxx-xxxxxxxxxxxx
            $uuid[19] = ['8', '9', 'a', 'b', '8', '9', 'a', 'b', 'c' => '8', 'd' => '9', 'e' => 'a', 'f' => 'b'][$uuid[19]] ?? $uuid[19];
            $this->uid = $uuid;
        } else {
            parent::__construct($uuid, true);
        }
    }
}
