<?php

declare(strict_types=1);

namespace Faker\Core;

use Faker\Extension\Helper;
use Faker\Extension\VersionExtension;
use Faker\Provider\DateTime;

final class Version implements VersionExtension
{
    /**
     * @var string[]
     */
    private $semverCommonPreReleaseIdentifiers = ['alpha', 'beta', 'rc'];

    /**
     * Represents v2.0.0 of the semantic versioning: https://semver.org/spec/v2.0.0.html
     */
    public function semver(bool $preRelease = false, bool $build = false): string
    {
        return sprintf(
            '%d.%d.%d%s%s',
            mt_rand(0, 9),
            mt_rand(0, 99),
            mt_rand(0, 99),
            $preRelease && mt_rand(0, 1) ? '-' . $this->semverPreReleaseIdentifier() : '',
            $build && mt_rand(0, 1) ? '+' . $this->semverBuildIdentifier() : ''
        );
    }

    /**
     * Common pre-release identifier
     */
    private function semverPreReleaseIdentifier(): string
    {
        $ident = Helper::randomElement($this->semverCommonPreReleaseIdentifiers);

        if (!mt_rand(0, 1)) {
            return $ident;
        }

        return $ident . '.' . mt_rand(1, 99);
    }

    /**
     * Common random build identifier
     */
    private function semverBuildIdentifier(): string
    {
        if (mt_rand(0, 1)) {
            // short git revision syntax: https://git-scm.com/book/en/v2/Git-Tools-Revision-Selection
            return substr(sha1(Helper::lexify('??????')), 0, 7);
        }

        // date syntax
        return DateTime::date('YmdHis');
    }
}
