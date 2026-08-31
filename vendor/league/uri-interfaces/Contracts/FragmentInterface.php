<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Contracts;

/**
 * @method self normalize() returns the normalized string representation of the component
 */
interface FragmentInterface extends UriComponentInterface
{
    /**
     * Returns the decoded fragment.
     */
    public function decoded(): ?string;
}
