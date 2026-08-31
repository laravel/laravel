<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Extension;

use function array_key_exists;
use PHPUnit\Runner\ParameterDoesNotExistException;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ParameterCollection
{
    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * @param array<string, string> $parameters
     */
    public static function fromArray(array $parameters): self
    {
        return new self($parameters);
    }

    /**
     * @param array<string, string> $parameters
     */
    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @throws ParameterDoesNotExistException
     */
    public function get(string $name): string
    {
        if (!$this->has($name)) {
            throw new ParameterDoesNotExistException($name);
        }

        return $this->parameters[$name];
    }
}
