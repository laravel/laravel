<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Csrf;

use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Default implementation of {@link CsrfTokenManagerInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CsrfTokenManager implements CsrfTokenManagerInterface
{
    /**
     * @var TokenGeneratorInterface
     */
    private $generator;

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * Creates a new CSRF provider using PHP's native session storage.
     *
     * @param TokenGeneratorInterface|null $generator The token generator
     * @param TokenStorageInterface|null   $storage   The storage for storing
     *                                                generated CSRF tokens
     */
    public function __construct(TokenGeneratorInterface $generator = null, TokenStorageInterface $storage = null)
    {
        $this->generator = $generator ?: new UriSafeTokenGenerator();
        $this->storage = $storage ?: new NativeSessionTokenStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId)
    {
        if ($this->storage->hasToken($tokenId)) {
            $value = $this->storage->getToken($tokenId);
        } else {
            $value = $this->generator->generateToken();

            $this->storage->setToken($tokenId, $value);
        }

        return new CsrfToken($tokenId, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken($tokenId)
    {
        $value = $this->generator->generateToken();

        $this->storage->setToken($tokenId, $value);

        return new CsrfToken($tokenId, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken($tokenId)
    {
        return $this->storage->removeToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenValid(CsrfToken $token)
    {
        if (!$this->storage->hasToken($token->getId())) {
            return false;
        }

        return StringUtils::equals($this->storage->getToken($token->getId()), $token->getValue());
    }
}
