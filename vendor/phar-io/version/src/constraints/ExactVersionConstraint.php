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

class ExactVersionConstraint extends AbstractVersionConstraint {
    public function complies(Version $version): bool {
        $other = $version->getVersionString();

        if ($version->hasBuildMetaData()) {
            $other .= '+' . $version->getBuildMetaData()->asString();
        }

        return $this->asString() === $other;
    }
}
