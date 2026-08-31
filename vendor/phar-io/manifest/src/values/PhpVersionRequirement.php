<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use PharIo\Version\VersionConstraint;

class PhpVersionRequirement implements Requirement {
    /** @var VersionConstraint */
    private $versionConstraint;

    public function __construct(VersionConstraint $versionConstraint) {
        $this->versionConstraint = $versionConstraint;
    }

    public function getVersionConstraint(): VersionConstraint {
        return $this->versionConstraint;
    }
}
