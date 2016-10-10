<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Catalogue;

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Represents an operation on catalogue(s).
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
interface OperationInterface
{
    /**
     * Returns domains affected by operation.
     *
     * @return array
     */
    public function getDomains();

    /**
     * Returns all valid messages after operation.
     *
     * @param string $domain
     *
     * @return array
     */
    public function getMessages($domain);

    /**
     * Returns new messages after operation.
     *
     * @param string $domain
     *
     * @return array
     */
    public function getNewMessages($domain);

    /**
     * Returns obsolete messages after operation.
     *
     * @param string $domain
     *
     * @return array
     */
    public function getObsoleteMessages($domain);

    /**
     * Returns resulting catalogue.
     *
     * @return MessageCatalogueInterface
     */
    public function getResult();
}
