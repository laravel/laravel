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
final readonly class CoversDefaultClass extends Metadata
{
    /**
     * @var class-string
     */
    private string $className;

    /**
     * @param 0|1          $level
     * @param class-string $className
     */
    protected function __construct(int $level, string $className)
    {
        parent::__construct($level);

        $this->className = $className;
    }

    public function isCoversDefaultClass(): true
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
}
