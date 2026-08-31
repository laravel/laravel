<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PharIo\Version;

class SpecificMajorVersionConstraint extends AbstractVersionConstraint {
    /** @var int */
    private $major;

    public function __construct(string $originalValue, int $major) {
        parent::__construct($originalValue);

        $this->major = $major;
    }

    public function complies(Version $version): bool {
        return $version->getMajor()->getValue() === $this->major;
    }
}
