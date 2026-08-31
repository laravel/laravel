<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Exception;

use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[WithHttpStatus(403)]
final class ExpiredSignedUriException extends SignedUriException
{
    /**
     * @internal
     */
    public function __construct()
    {
        parent::__construct('The URI has expired.');
    }
}
