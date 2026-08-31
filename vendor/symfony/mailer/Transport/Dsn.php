<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport;

use Symfony\Component\Mailer\Exception\InvalidArgumentException;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class Dsn
{
    public function __construct(
        private string $scheme,
        private string $host,
        private ?string $user = null,
        #[\SensitiveParameter] private ?string $password = null,
        private ?int $port = null,
        private array $options = [],
    ) {
    }

    public static function fromString(#[\SensitiveParameter] string $dsn): self
    {
        if (false === $params = parse_url($dsn)) {
            throw new InvalidArgumentException('The mailer DSN is invalid.');
        }

        if (!isset($params['scheme'])) {
            throw new InvalidArgumentException('The mailer DSN must contain a scheme.');
        }

        if (!isset($params['host'])) {
            throw new InvalidArgumentException('The mailer DSN must contain a host (use "default" by default).');
        }

        $user = '' !== ($params['user'] ?? '') ? rawurldecode($params['user']) : null;
        $password = '' !== ($params['pass'] ?? '') ? rawurldecode($params['pass']) : null;
        $port = $params['port'] ?? null;
        parse_str($params['query'] ?? '', $query);

        return new self($params['scheme'], $params['host'], $user, $password, $port, $query);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPort(?int $default = null): ?int
    {
        return $this->port ?? $default;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function getBooleanOption(string $key, bool $default = false): bool
    {
        return filter_var($this->getOption($key, $default), \FILTER_VALIDATE_BOOLEAN);
    }
}
