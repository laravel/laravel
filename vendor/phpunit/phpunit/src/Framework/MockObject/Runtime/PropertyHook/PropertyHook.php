<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Runtime;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class PropertyHook
{
    /**
     * @var non-empty-string
     */
    private string $propertyName;

    /**
     * @param non-empty-string $propertyName
     */
    public static function get(string $propertyName): PropertyGetHook
    {
        return new PropertyGetHook($propertyName);
    }

    /**
     * @param non-empty-string $propertyName
     */
    public static function set(string $propertyName): PropertySetHook
    {
        return new PropertySetHook($propertyName);
    }

    /**
     * @param non-empty-string $propertyName
     */
    protected function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @return non-empty-string
     */
    public function propertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @return non-empty-string
     *
     * @internal This method is not covered by the backward compatibility promise for PHPUnit
     */
    abstract public function asString(): string;
}
