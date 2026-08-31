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

use PHPUnit\Metadata\Version\Requirement;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class RequiresPhpExtension extends Metadata
{
    /**
     * @var non-empty-string
     */
    private string $extension;
    private ?Requirement $versionRequirement;

    /**
     * @param 0|1              $level
     * @param non-empty-string $extension
     */
    protected function __construct(int $level, string $extension, ?Requirement $versionRequirement)
    {
        parent::__construct($level);

        $this->extension          = $extension;
        $this->versionRequirement = $versionRequirement;
    }

    public function isRequiresPhpExtension(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function extension(): string
    {
        return $this->extension;
    }

    /**
     * @phpstan-assert-if-true !null $this->versionRequirement
     */
    public function hasVersionRequirement(): bool
    {
        return $this->versionRequirement !== null;
    }

    /**
     * @throws NoVersionRequirementException
     */
    public function versionRequirement(): Requirement
    {
        if ($this->versionRequirement === null) {
            throw new NoVersionRequirementException;
        }

        return $this->versionRequirement;
    }
}
