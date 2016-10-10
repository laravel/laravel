<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This code is partially based on the Rack-Cache library by Ryan Tomayko,
 * which is released under the MIT license.
 * (based on commit 02d2b48d75bcb63cf1c0c7149c077ad256542801)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpFoundation\Response;

/**
 * EsiResponseCacheStrategyInterface implementations know how to compute the
 * Response cache HTTP header based on the different ESI response cache headers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface EsiResponseCacheStrategyInterface
{
    /**
     * Adds a Response.
     *
     * @param Response $response
     */
    public function add(Response $response);

    /**
     * Updates the Response HTTP headers based on the embedded Responses.
     *
     * @param Response $response
     */
    public function update(Response $response);
}
