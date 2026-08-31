<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport\Smtp;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;
use Symfony\Component\Mailer\Transport\Smtp\Auth\AuthenticatorInterface;
use Symfony\Component\Mailer\Transport\Smtp\Stream\AbstractStream;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

/**
 * Sends Emails over SMTP with ESMTP support.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Chris Corbyn
 */
class EsmtpTransport extends SmtpTransport
{
    private array $authenticators = [];
    private string $username = '';
    private string $password = '';
    private array $capabilities;
    private bool $autoTls = true;
    private bool $requireTls = false;

    public function __construct(string $host = 'localhost', int $port = 0, ?bool $tls = null, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null, ?AbstractStream $stream = null, ?array $authenticators = null)
    {
        parent::__construct($stream, $dispatcher, $logger);

        if (null === $authenticators) {
            // fallback to default authenticators
            // order is important here (roughly most secure and popular first)
            $authenticators = [
                new Auth\CramMd5Authenticator(),
                new Auth\LoginAuthenticator(),
                new Auth\PlainAuthenticator(),
                new Auth\XOAuth2Authenticator(),
            ];
        }
        $this->setAuthenticators($authenticators);

        /** @var SocketStream $stream */
        $stream = $this->getStream();

        if (null === $tls) {
            if (465 === $port) {
                $tls = true;
            } else {
                $tls = \defined('OPENSSL_VERSION_NUMBER') && 0 === $port && 'localhost' !== $host;
            }
        }
        if (!$tls) {
            $stream->disableTls();
        }
        if (0 === $port) {
            $port = $tls ? 465 : 25;
        }

        $stream->setHost($host);
        $stream->setPort($port);
    }

    /**
     * @return $this
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return $this
     */
    public function setPassword(#[\SensitiveParameter] string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return $this
     */
    public function setAutoTls(bool $autoTls): static
    {
        $this->autoTls = $autoTls;

        return $this;
    }

    public function isAutoTls(): bool
    {
        return $this->autoTls;
    }

    /**
     * @return $this
     */
    public function setRequireTls(bool $requireTls): static
    {
        $this->requireTls = $requireTls;

        return $this;
    }

    public function isTlsRequired(): bool
    {
        return $this->requireTls;
    }

    public function setAuthenticators(array $authenticators): void
    {
        $this->authenticators = [];
        foreach ($authenticators as $authenticator) {
            $this->addAuthenticator($authenticator);
        }
    }

    public function addAuthenticator(AuthenticatorInterface $authenticator): void
    {
        $this->authenticators[] = $authenticator;
    }

    public function executeCommand(string $command, array $codes): string
    {
        return [250] === $codes && str_starts_with($command, 'HELO ') ? $this->doEhloCommand() : parent::executeCommand($command, $codes);
    }

    final protected function getCapabilities(): array
    {
        return $this->capabilities;
    }

    private function doEhloCommand(): string
    {
        try {
            $response = $this->executeCommand(\sprintf("EHLO %s\r\n", $this->getLocalDomain()), [250]);
        } catch (TransportExceptionInterface $e) {
            try {
                return parent::executeCommand(\sprintf("HELO %s\r\n", $this->getLocalDomain()), [250]);
            } catch (TransportExceptionInterface $ex) {
                if (!$ex->getCode()) {
                    throw $e;
                }

                throw $ex;
            }
        }

        $this->capabilities = $this->parseCapabilities($response);

        /** @var SocketStream $stream */
        $stream = $this->getStream();
        $tlsStarted = $stream->isTls();
        // WARNING: !$stream->isTLS() is right, 100% sure :)
        // if you think that the ! should be removed, read the code again
        // if doing so "fixes" your issue then it probably means your SMTP server behaves incorrectly or is wrongly configured
        if ($this->autoTls && !$stream->isTLS() && \defined('OPENSSL_VERSION_NUMBER') && \array_key_exists('STARTTLS', $this->capabilities)) {
            $this->executeCommand("STARTTLS\r\n", [220]);

            if (!$stream->startTLS()) {
                throw new TransportException('Unable to connect with STARTTLS.');
            }

            $tlsStarted = true;
            $response = $this->executeCommand(\sprintf("EHLO %s\r\n", $this->getLocalDomain()), [250]);
            $this->capabilities = $this->parseCapabilities($response);
        }

        if (!$tlsStarted && $this->isTlsRequired()) {
            throw new TransportException('TLS required but neither TLS or STARTTLS are in use.');
        }

        if (\array_key_exists('AUTH', $this->capabilities)) {
            $this->handleAuth($this->capabilities['AUTH']);
        }

        return $response;
    }

    private function parseCapabilities(string $ehloResponse): array
    {
        $capabilities = [];
        $lines = explode("\r\n", trim($ehloResponse));
        array_shift($lines);
        foreach ($lines as $line) {
            if (preg_match('/^[0-9]{3}[ -]([A-Z0-9-]+)((?:[ =].*)?)$/Di', $line, $matches)) {
                $value = strtoupper(ltrim($matches[2], ' ='));
                $capabilities[strtoupper($matches[1])] = $value ? explode(' ', $value) : [];
            }
        }

        return $capabilities;
    }

    protected function serverSupportsSmtpUtf8(): bool
    {
        return \array_key_exists('SMTPUTF8', $this->capabilities);
    }

    private function handleAuth(array $modes): void
    {
        if (!$this->username) {
            return;
        }

        $code = null;
        $authNames = [];
        $errors = [];
        $modes = array_map('strtolower', $modes);
        foreach ($this->authenticators as $authenticator) {
            if (!\in_array(strtolower($authenticator->getAuthKeyword()), $modes, true)) {
                continue;
            }

            $code = null;
            $authNames[] = $authenticator->getAuthKeyword();
            try {
                $authenticator->authenticate($this);

                return;
            } catch (UnexpectedResponseException $e) {
                $code = $e->getCode();

                try {
                    $this->executeCommand("RSET\r\n", [250]);
                } catch (TransportExceptionInterface) {
                    // ignore this exception as it probably means that the server error was final
                }

                // keep the error message, but tries the other authenticators
                $errors[$authenticator->getAuthKeyword()] = $e->getMessage();
            }
        }

        if (!$authNames) {
            throw new TransportException(\sprintf('Failed to find an authenticator supported by the SMTP server, which currently supports: "%s".', implode('", "', $modes)), $code ?: 504);
        }

        $message = \sprintf('Failed to authenticate on SMTP server with username "%s" using the following authenticators: "%s".', $this->username, implode('", "', $authNames));
        foreach ($errors as $name => $error) {
            $message .= \sprintf(' Authenticator "%s" returned "%s".', $name, $error);
        }

        throw new TransportException($message, $code ?: 535);
    }
}
