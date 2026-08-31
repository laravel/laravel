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
 * A v8 UUID has no explicit requirements except embedding its version + variant bits.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class UuidV8 extends Uuid
{
    protected const TYPE = 8;

    public function __construct(string $uuid)
    {
        parent::__construct($uuid, true);
    }
}
