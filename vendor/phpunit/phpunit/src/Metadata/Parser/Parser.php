<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Parser;

use PHPUnit\Metadata\MetadataCollection;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This interface is not covered by the backward compatibility promise for PHPUnit
 */
interface Parser
{
    /**
     * @param class-string $className
     */
    public function forClass(string $className): MetadataCollection;

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forMethod(string $className, string $methodName): MetadataCollection;

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forClassAndMethod(string $className, string $methodName): MetadataCollection;
}
