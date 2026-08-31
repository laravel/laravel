<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration\Logging;

use PHPUnit\TextUI\XmlConfiguration\Exception;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Html as TestDoxHtml;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Text as TestDoxText;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class Logging
{
    private ?Junit $junit;
    private ?TeamCity $teamCity;
    private ?TestDoxHtml $testDoxHtml;
    private ?TestDoxText $testDoxText;

    public function __construct(?Junit $junit, ?TeamCity $teamCity, ?TestDoxHtml $testDoxHtml, ?TestDoxText $testDoxText)
    {
        $this->junit       = $junit;
        $this->teamCity    = $teamCity;
        $this->testDoxHtml = $testDoxHtml;
        $this->testDoxText = $testDoxText;
    }

    public function hasJunit(): bool
    {
        return $this->junit !== null;
    }

    /**
     * @throws Exception
     */
    public function junit(): Junit
    {
        if ($this->junit === null) {
            throw new Exception('Logger "JUnit XML" is not configured');
        }

        return $this->junit;
    }

    public function hasTeamCity(): bool
    {
        return $this->teamCity !== null;
    }

    /**
     * @throws Exception
     */
    public function teamCity(): TeamCity
    {
        if ($this->teamCity === null) {
            throw new Exception('Logger "Team City" is not configured');
        }

        return $this->teamCity;
    }

    public function hasTestDoxHtml(): bool
    {
        return $this->testDoxHtml !== null;
    }

    /**
     * @throws Exception
     */
    public function testDoxHtml(): TestDoxHtml
    {
        if ($this->testDoxHtml === null) {
            throw new Exception('Logger "TestDox HTML" is not configured');
        }

        return $this->testDoxHtml;
    }

    public function hasTestDoxText(): bool
    {
        return $this->testDoxText !== null;
    }

    /**
     * @throws Exception
     */
    public function testDoxText(): TestDoxText
    {
        if ($this->testDoxText === null) {
            throw new Exception('Logger "TestDox Text" is not configured');
        }

        return $this->testDoxText;
    }
}
