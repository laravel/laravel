<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class ExtensionBootstrap
{
    /**
     * @var non-empty-string
     */
    private string $className;

    /**
     * @var array<string,string>
     */
    private array $parameters;

    /**
     * @param non-empty-string     $className
     * @param array<string,string> $parameters
     */
    public function __construct(string $className, array $parameters)
    {
        $this->className  = $className;
        $this->parameters = $parameters;
    }

    /**
     * @return non-empty-string
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * @return array<string,string>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }
}
