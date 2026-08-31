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
 * A v3 UUID contains an MD5 hash of another UUID and a name.
 *
 * Use Uuid::v3() to compute one.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class UuidV3 extends Uuid
{
    protected const TYPE = 3;

    public function __construct(string $uuid)
    {
        parent::__construct($uuid, true);
    }
}
