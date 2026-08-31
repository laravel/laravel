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
final readonly class RequiresPhpunit extends Metadata
{
    private Requirement $versionRequirement;

    /**
     * @param 0|1 $level
     */
    protected function __construct(int $level, Requirement $versionRequirement)
    {
        parent::__construct($level);

        $this->versionRequirement = $versionRequirement;
    }

    public function isRequiresPhpunit(): true
    {
        return true;
    }

    public function versionRequirement(): Requirement
    {
        return $this->versionRequirement;
    }
}
