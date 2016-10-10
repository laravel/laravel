<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Csrf\TokenGenerator;

use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Generates CSRF tokens.
 *
 * @since  2.4
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 */
class UriSafeTokenGenerator implements TokenGeneratorInterface
{
    /**
     * The generator for random values.
     *
     * @var SecureRandomInterface
     */
    private $random;

    /**
     * The amount of entropy collected for each token (in bits).
     *
     * @var int
     */
    private $entropy;

    /**
     * Generates URI-safe CSRF tokens.
     *
     * @param SecureRandomInterface|null $random  The random value generator used for
     *                                            generating entropy
     * @param int                        $entropy The amount of entropy collected for
     *                                            each token (in bits)
     */
    public function __construct(SecureRandomInterface $random = null, $entropy = 256)
    {
        $this->random = $random ?: new SecureRandom();
        $this->entropy = $entropy;
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken()
    {
        // Generate an URI safe base64 encoded string that does not contain "+",
        // "/" or "=" which need to be URL encoded and make URLs unnecessarily
        // longer.
        $bytes = $this->random->nextBytes($this->entropy / 8);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
