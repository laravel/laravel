<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Version;

use function preg_match;
use PharIo\Version\UnsupportedVersionConstraintException;
use PharIo\Version\VersionConstraintParser;
use PHPUnit\Metadata\InvalidVersionRequirementException;
use PHPUnit\Util\InvalidVersionOperatorException;
use PHPUnit\Util\VersionComparisonOperator;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class Requirement
{
    private const VERSION_COMPARISON = '/(?P<operator>[<>=!]{0,2})\s*(?P<version>[\d\.-]+(dev|(RC|alpha|beta)[\d\.])?)[ \t]*\r?$/m';

    /**
     * @throws InvalidVersionOperatorException
     * @throws InvalidVersionRequirementException
     */
    public static function from(string $versionRequirement): self
    {
        try {
            return new ConstraintRequirement(
                (new VersionConstraintParser)->parse(
                    $versionRequirement,
                ),
            );
        } catch (UnsupportedVersionConstraintException) {
            if (preg_match(self::VERSION_COMPARISON, $versionRequirement, $matches)) {
                return new ComparisonRequirement(
                    $matches['version'],
                    new VersionComparisonOperator(
                        !empty($matches['operator']) ? $matches['operator'] : '>=',
                    ),
                );
            }
        }

        throw new InvalidVersionRequirementException;
    }

    abstract public function isSatisfiedBy(string $version): bool;

    abstract public function asString(): string;
}
