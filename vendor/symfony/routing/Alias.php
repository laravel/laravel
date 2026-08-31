<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

use Symfony\Component\Routing\Exception\InvalidArgumentException;

class Alias
{
    private array $deprecation = [];

    public function __construct(
        private string $id,
    ) {
    }

    public function withId(string $id): static
    {
        $new = clone $this;

        $new->id = $id;

        return $new;
    }

    /**
     * Returns the target name of this alias.
     *
     * @return string The target name
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Whether this alias is deprecated, that means it should not be referenced anymore.
     *
     * @param string $package The name of the composer package that is triggering the deprecation
     * @param string $version The version of the package that introduced the deprecation
     * @param string $message The deprecation message to use
     *
     * @return $this
     *
     * @throws InvalidArgumentException when the message template is invalid
     */
    public function setDeprecated(string $package, string $version, string $message): static
    {
        if ('' !== $message) {
            if (preg_match('#[\r\n]|\*/#', $message)) {
                throw new InvalidArgumentException('Invalid characters found in deprecation template.');
            }

            if (!str_contains($message, '%alias_id%')) {
                throw new InvalidArgumentException('The deprecation template must contain the "%alias_id%" placeholder.');
            }
        }

        $this->deprecation = [
            'package' => $package,
            'version' => $version,
            'message' => $message ?: 'The "%alias_id%" route alias is deprecated. You should stop using it, as it will be removed in the future.',
        ];

        return $this;
    }

    public function isDeprecated(): bool
    {
        return (bool) $this->deprecation;
    }

    /**
     * @param string $name Route name relying on this alias
     */
    public function getDeprecation(string $name): array
    {
        return [
            'package' => $this->deprecation['package'],
            'version' => $this->deprecation['version'],
            'message' => str_replace('%alias_id%', $name, $this->deprecation['message']),
        ];
    }
}
