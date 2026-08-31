<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\TextUI\Configuration\ConstantCollection;
use PHPUnit\TextUI\Configuration\DirectoryCollection;
use PHPUnit\TextUI\Configuration\ExtensionBootstrapCollection;
use PHPUnit\TextUI\Configuration\FileCollection;
use PHPUnit\TextUI\Configuration\FilterDirectoryCollection as CodeCoverageFilterDirectoryCollection;
use PHPUnit\TextUI\Configuration\GroupCollection;
use PHPUnit\TextUI\Configuration\IniSettingCollection;
use PHPUnit\TextUI\Configuration\Php;
use PHPUnit\TextUI\Configuration\Source;
use PHPUnit\TextUI\Configuration\TestSuiteCollection;
use PHPUnit\TextUI\Configuration\VariableCollection;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage;
use PHPUnit\TextUI\XmlConfiguration\Logging\Logging;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class DefaultConfiguration extends Configuration
{
    public static function create(): self
    {
        return new self(
            ExtensionBootstrapCollection::fromArray([]),
            new Source(
                null,
                false,
                CodeCoverageFilterDirectoryCollection::fromArray([]),
                FileCollection::fromArray([]),
                CodeCoverageFilterDirectoryCollection::fromArray([]),
                FileCollection::fromArray([]),
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                [
                    'functions' => [],
                    'methods'   => [],
                ],
                false,
                false,
                false,
                true,
            ),
            new CodeCoverage(
                false,
                true,
                false,
                false,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ),
            new Groups(
                GroupCollection::fromArray([]),
                GroupCollection::fromArray([]),
            ),
            new Logging(
                null,
                null,
                null,
                null,
            ),
            new Php(
                DirectoryCollection::fromArray([]),
                IniSettingCollection::fromArray([]),
                ConstantCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
                VariableCollection::fromArray([]),
            ),
            new PHPUnit(
                null,
                true,
                80,
                \PHPUnit\TextUI\Configuration\Configuration::COLOR_DEFAULT,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                null,
                false,
                false,
                false,
                false,
                true,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                null,
                false,
                false,
                true,
                false,
                false,
                1,
                1,
                10,
                60,
                null,
                TestSuiteSorter::ORDER_DEFAULT,
                true,
                false,
                false,
                false,
                false,
                false,
                false,
                100,
                0,
            ),
            TestSuiteCollection::fromArray([]),
        );
    }

    public function isDefault(): bool
    {
        return true;
    }
}
