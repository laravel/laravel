<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication\RememberMe;

use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * Interface for TokenProviders
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface TokenProviderInterface
{
    /**
     * Loads the active token for the given series.
     *
     * @param string $series
     *
     * @return PersistentTokenInterface
     *
     * @throws TokenNotFoundException if the token is not found
     */
    public function loadTokenBySeries($series);

    /**
     * Deletes all tokens belonging to series.
     *
     * @param string $series
     */
    public function deleteTokenBySeries($series);

    /**
     * Updates the token according to this data.
     *
     * @param string    $series
     * @param string    $tokenValue
     * @param \DateTime $lastUsed
     * @throws TokenNotFoundException if the token is not found
     */
    public function updateToken($series, $tokenValue, \DateTime $lastUsed);

    /**
     * Creates a new token.
     *
     * @param PersistentTokenInterface $token
     */
    public function createNewToken(PersistentTokenInterface $token);
}
