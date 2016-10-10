<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\RememberMe;

use Symfony\Component\Security\Core\Authentication\RememberMe\TokenProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CookieTheftException;
use Symfony\Component\Security\Core\Authentication\RememberMe\PersistentToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use Psr\Log\LoggerInterface;

/**
 * Concrete implementation of the RememberMeServicesInterface which needs
 * an implementation of TokenProviderInterface for providing remember-me
 * capabilities.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PersistentTokenBasedRememberMeServices extends AbstractRememberMeServices
{
    private $tokenProvider;
    private $secureRandom;

    /**
     * Constructor.
     *
     * @param array                 $userProviders
     * @param string                $key
     * @param string                $providerKey
     * @param array                 $options
     * @param LoggerInterface       $logger
     * @param SecureRandomInterface $secureRandom
     */
    public function __construct(array $userProviders, $key, $providerKey, array $options = array(), LoggerInterface $logger = null, SecureRandomInterface $secureRandom)
    {
        parent::__construct($userProviders, $key, $providerKey, $options, $logger);

        $this->secureRandom = $secureRandom;
    }

    /**
     * Sets the token provider
     *
     * @param TokenProviderInterface $tokenProvider
     */
    public function setTokenProvider(TokenProviderInterface $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function cancelCookie(Request $request)
    {
        // Delete cookie on the client
        parent::cancelCookie($request);

        // Delete cookie from the tokenProvider
        if (null !== ($cookie = $request->cookies->get($this->options['name']))
            && count($parts = $this->decodeCookie($cookie)) === 2
        ) {
            list($series, $tokenValue) = $parts;
            $this->tokenProvider->deleteTokenBySeries($series);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        if (count($cookieParts) !== 2) {
            throw new AuthenticationException('The cookie is invalid.');
        }

        list($series, $tokenValue) = $cookieParts;
        $persistentToken = $this->tokenProvider->loadTokenBySeries($series);

        if ($persistentToken->getTokenValue() !== $tokenValue) {
            throw new CookieTheftException('This token was already used. The account is possibly compromised.');
        }

        if ($persistentToken->getLastUsed()->getTimestamp() + $this->options['lifetime'] < time()) {
            throw new AuthenticationException('The cookie has expired.');
        }

        $series = $persistentToken->getSeries();
        $tokenValue = base64_encode($this->secureRandom->nextBytes(64));
        $this->tokenProvider->updateToken($series, $tokenValue, new \DateTime());
        $request->attributes->set(self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->options['name'],
                $this->encodeCookie(array($series, $tokenValue)),
                time() + $this->options['lifetime'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'],
                $this->options['httponly']
            )
        );

        return $this->getUserProvider($persistentToken->getClass())->loadUserByUsername($persistentToken->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    protected function onLoginSuccess(Request $request, Response $response, TokenInterface $token)
    {
        $series = base64_encode($this->secureRandom->nextBytes(64));
        $tokenValue = base64_encode($this->secureRandom->nextBytes(64));

        $this->tokenProvider->createNewToken(
            new PersistentToken(
                get_class($user = $token->getUser()),
                $user->getUsername(),
                $series,
                $tokenValue,
                new \DateTime()
            )
        );

        $response->headers->setCookie(
            new Cookie(
                $this->options['name'],
                $this->encodeCookie(array($series, $tokenValue)),
                time() + $this->options['lifetime'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'],
                $this->options['httponly']
            )
        );
    }
}
