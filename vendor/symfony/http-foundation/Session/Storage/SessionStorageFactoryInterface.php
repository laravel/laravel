<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
interface SessionStorageFactoryInterface
{
    /**
     * Creates a new instance of SessionStorageInterface.
     */
    public function createStorage(?Request $request): SessionStorageInterface;
}
