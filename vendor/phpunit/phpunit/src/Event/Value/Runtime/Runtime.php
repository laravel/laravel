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

use function sprintf;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Runtime
{
    private OperatingSystem $operatingSystem;
    private PHP $php;
    private PHPUnit $phpunit;

    public function __construct()
    {
        $this->operatingSystem = new OperatingSystem;
        $this->php             = new PHP;
        $this->phpunit         = new PHPUnit;
    }

    public function asString(): string
    {
        $php = $this->php();

        return sprintf(
            'PHPUnit %s using PHP %s (%s) on %s',
            $this->phpunit()->versionId(),
            $php->version(),
            $php->sapi(),
            $this->operatingSystem()->operatingSystem(),
        );
    }

    public function operatingSystem(): OperatingSystem
    {
        return $this->operatingSystem;
    }

    public function php(): PHP
    {
        return $this->php;
    }

    public function phpunit(): PHPUnit
    {
        return $this->phpunit;
    }
}
