<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Domain;

use Symfony\Component\Security\Core\Util\ClassUtils;
use Symfony\Component\Security\Acl\Exception\InvalidDomainObjectException;
use Symfony\Component\Security\Acl\Model\DomainObjectInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;

/**
 * ObjectIdentity implementation
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class ObjectIdentity implements ObjectIdentityInterface
{
    private $identifier;
    private $type;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($identifier, $type)
    {
        if (empty($identifier)) {
            throw new \InvalidArgumentException('$identifier cannot be empty.');
        }
        if (empty($type)) {
            throw new \InvalidArgumentException('$type cannot be empty.');
        }

        $this->identifier = $identifier;
        $this->type = $type;
    }

    /**
     * Constructs an ObjectIdentity for the given domain object
     *
     * @param object $domainObject
     * @throws InvalidDomainObjectException
     * @return ObjectIdentity
     */
    public static function fromDomainObject($domainObject)
    {
        if (!is_object($domainObject)) {
            throw new InvalidDomainObjectException('$domainObject must be an object.');
        }

        try {
            if ($domainObject instanceof DomainObjectInterface) {
                return new self($domainObject->getObjectIdentifier(), ClassUtils::getRealClass($domainObject));
            } elseif (method_exists($domainObject, 'getId')) {
                return new self($domainObject->getId(), ClassUtils::getRealClass($domainObject));
            }
        } catch (\InvalidArgumentException $invalid) {
            throw new InvalidDomainObjectException($invalid->getMessage(), 0, $invalid);
        }

        throw new InvalidDomainObjectException('$domainObject must either implement the DomainObjectInterface, or have a method named "getId".');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ObjectIdentityInterface $identity)
    {
        // comparing the identifier with === might lead to problems, so we
        // waive this restriction
        return $this->identifier == $identity->getIdentifier()
               && $this->type === $identity->getType();
    }

    /**
     * Returns a textual representation of this object identity
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('ObjectIdentity(%s, %s)', $this->identifier, $this->type);
    }
}
