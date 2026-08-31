<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Runtime;

use PHPUnit\Runner\Version;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class PHPUnit
{
    private string $versionId;
    private string $releaseSeries;

    public function __construct()
    {
        $this->versionId     = Version::id();
        $this->releaseSeries = Version::series();
    }

    public function versionId(): string
    {
        return $this->versionId;
    }

    public function releaseSeries(): string
    {
        return $this->releaseSeries;
    }
}
