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

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

use function dechex;
use function hexdec;
use function str_pad;
use function substr;

use const STR_PAD_LEFT;

/**
 * StaticNodeProvider provides a static node value with the multicast bit set
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.10 RFC 9562, 6.10. UUIDs That Do Not Identify the Host
 */
class StaticNodeProvider implements NodeProviderInterface
{
    private Hexadecimal $node;

    /**
     * @param Hexadecimal $node The static node value to use
     */
    public function __construct(Hexadecimal $node)
    {
        if (strlen($node->toString()) > 12) {
            throw new InvalidArgumentException('Static node value cannot be greater than 12 hexadecimal characters');
        }

        $this->node = $this->setMulticastBit($node);
    }

    public function getNode(): Hexadecimal
    {
        return $this->node;
    }

    /**
     * Set the multicast bit for the static node value
     */
    private function setMulticastBit(Hexadecimal $node): Hexadecimal
    {
        $nodeHex = str_pad($node->toString(), 12, '0', STR_PAD_LEFT);
        $firstOctet = substr($nodeHex, 0, 2);
        $firstOctet = str_pad(dechex(hexdec($firstOctet) | 0x01), 2, '0', STR_PAD_LEFT);

        return new Hexadecimal($firstOctet . substr($nodeHex, 2));
    }
}
