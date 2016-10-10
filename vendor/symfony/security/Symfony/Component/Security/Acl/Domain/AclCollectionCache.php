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

use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This service caches ACLs for an entire collection of objects.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AclCollectionCache
{
    private $aclProvider;
    private $objectIdentityRetrievalStrategy;
    private $securityIdentityRetrievalStrategy;

    /**
     * Constructor.
     *
     * @param AclProviderInterface                       $aclProvider
     * @param ObjectIdentityRetrievalStrategyInterface   $oidRetrievalStrategy
     * @param SecurityIdentityRetrievalStrategyInterface $sidRetrievalStrategy
     */
    public function __construct(AclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy, SecurityIdentityRetrievalStrategyInterface $sidRetrievalStrategy)
    {
        $this->aclProvider = $aclProvider;
        $this->objectIdentityRetrievalStrategy = $oidRetrievalStrategy;
        $this->securityIdentityRetrievalStrategy = $sidRetrievalStrategy;
    }

    /**
     * Batch loads ACLs for an entire collection; thus, it reduces the number
     * of required queries considerably.
     *
     * @param mixed            $collection anything that can be passed to foreach()
     * @param TokenInterface[] $tokens     an array of TokenInterface implementations
     */
    public function cache($collection, array $tokens = array())
    {
        $sids = array();
        foreach ($tokens as $token) {
            $sids = array_merge($sids, $this->securityIdentityRetrievalStrategy->getSecurityIdentities($token));
        }

        $oids = array();
        foreach ($collection as $domainObject) {
            $oids[] = $this->objectIdentityRetrievalStrategy->getObjectIdentity($domainObject);
        }

        $this->aclProvider->findAcls($oids, $sids);
    }
}
