<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Header;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\RfcComplianceException;

/**
 * An ID MIME Header for something like Message-ID or Content-ID (one or more addresses).
 *
 * @author Chris Corbyn
 */
final class IdentificationHeader extends AbstractHeader
{
    private array $ids = [];
    private array $idsAsAddresses = [];

    public function __construct(string $name, string|array $ids)
    {
        parent::__construct($name);

        $this->setId($ids);
    }

    /**
     * @param string|string[] $body a string ID or an array of IDs
     *
     * @throws RfcComplianceException
     */
    public function setBody(mixed $body): void
    {
        $this->setId($body);
    }

    public function getBody(): array
    {
        return $this->getIds();
    }

    /**
     * Set the ID used in the value of this header.
     *
     * @param string|string[] $id
     *
     * @throws RfcComplianceException
     */
    public function setId(string|array $id): void
    {
        $this->setIds(\is_array($id) ? $id : [$id]);
    }

    /**
     * Get the ID used in the value of this Header.
     *
     * If multiple IDs are set only the first is returned.
     */
    public function getId(): ?string
    {
        return $this->ids[0] ?? null;
    }

    /**
     * Set a collection of IDs to use in the value of this Header.
     *
     * @param string[] $ids
     *
     * @throws RfcComplianceException
     */
    public function setIds(array $ids): void
    {
        $this->ids = [];
        $this->idsAsAddresses = [];
        foreach ($ids as $id) {
            $this->idsAsAddresses[] = new Address($id);
            $this->ids[] = $id;
        }
    }

    /**
     * Get the list of IDs used in this Header.
     *
     * @return string[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function getBodyAsString(): string
    {
        $addrs = [];
        foreach ($this->idsAsAddresses as $address) {
            $addrs[] = '<'.$address->toString().'>';
        }

        return implode(' ', $addrs);
    }
}
