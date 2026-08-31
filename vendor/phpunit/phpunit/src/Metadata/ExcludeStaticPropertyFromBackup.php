<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ExcludeStaticPropertyFromBackup extends Metadata
{
    /**
     * @var class-string
     */
    private string $className;

    /**
     * @var non-empty-string
     */
    private string $propertyName;

    /**
     * @param 0|1              $level
     * @param class-string     $className
     * @param non-empty-string $propertyName
     */
    protected function __construct(int $level, string $className, string $propertyName)
    {
        parent::__construct($level);

        $this->className    = $className;
        $this->propertyName = $propertyName;
    }

    public function isExcludeStaticPropertyFromBackup(): true
    {
        return true;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * @return non-empty-string
     */
    public function propertyName(): string
    {
        return $this->propertyName;
    }
}
