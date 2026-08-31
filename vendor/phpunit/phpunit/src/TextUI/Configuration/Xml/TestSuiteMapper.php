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

use const PHP_VERSION;
use function explode;
use function in_array;
use function is_dir;
use function is_file;
use function sprintf;
use function str_contains;
use function version_compare;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\Exception as FrameworkException;
use PHPUnit\Framework\TestSuite as TestSuiteObject;
use PHPUnit\TextUI\Configuration\TestSuiteCollection;
use PHPUnit\TextUI\RuntimeException;
use PHPUnit\TextUI\TestDirectoryNotFoundException;
use PHPUnit\TextUI\TestFileNotFoundException;
use SebastianBergmann\FileIterator\Facade;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestSuiteMapper
{
    /**
     * @param non-empty-string $xmlConfigurationFile,
     *
     * @throws RuntimeException
     * @throws TestDirectoryNotFoundException
     * @throws TestFileNotFoundException
     */
    public function map(string $xmlConfigurationFile, TestSuiteCollection $configuredTestSuites, string $namesOfIncludedTestSuites, string $namesOfExcludedTestSuites): TestSuiteObject
    {
        try {
            $namesOfIncludedTestSuitesAsArray = $namesOfIncludedTestSuites ? explode(',', $namesOfIncludedTestSuites) : [];
            $excludedTestSuitesAsArray        = $namesOfExcludedTestSuites ? explode(',', $namesOfExcludedTestSuites) : [];
            $result                           = TestSuiteObject::empty($xmlConfigurationFile);
            $processed                        = [];

            foreach ($configuredTestSuites as $configuredTestSuite) {
                if (!empty($namesOfIncludedTestSuitesAsArray) && !in_array($configuredTestSuite->name(), $namesOfIncludedTestSuitesAsArray, true)) {
                    continue;
                }

                if (!empty($excludedTestSuitesAsArray) && in_array($configuredTestSuite->name(), $excludedTestSuitesAsArray, true)) {
                    continue;
                }

                $testSuiteName = $configuredTestSuite->name();
                $exclude       = [];

                foreach ($configuredTestSuite->exclude()->asArray() as $file) {
                    $exclude[] = $file->path();
                }

                $testSuite = TestSuiteObject::empty($configuredTestSuite->name());
                $empty     = true;

                foreach ($configuredTestSuite->directories() as $directory) {
                    if (!str_contains($directory->path(), '*') && !is_dir($directory->path())) {
                        throw new TestDirectoryNotFoundException($directory->path());
                    }

                    if (!version_compare(PHP_VERSION, $directory->phpVersion(), $directory->phpVersionOperator()->asString())) {
                        continue;
                    }

                    $files = (new Facade)->getFilesAsArray(
                        $directory->path(),
                        $directory->suffix(),
                        $directory->prefix(),
                        $exclude,
                    );

                    $groups = $directory->groups();

                    foreach ($files as $file) {
                        if (isset($processed[$file])) {
                            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                                sprintf(
                                    'Cannot add file %s to test suite "%s" as it was already added to test suite "%s"',
                                    $file,
                                    $testSuiteName,
                                    $processed[$file],
                                ),
                            );

                            continue;
                        }

                        $processed[$file] = $testSuiteName;
                        $empty            = false;

                        $testSuite->addTestFile($file, $groups);
                    }
                }

                foreach ($configuredTestSuite->files() as $file) {
                    if (!is_file($file->path())) {
                        throw new TestFileNotFoundException($file->path());
                    }

                    if (!version_compare(PHP_VERSION, $file->phpVersion(), $file->phpVersionOperator()->asString())) {
                        continue;
                    }

                    if (isset($processed[$file->path()])) {
                        EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                            sprintf(
                                'Cannot add file %s to test suite "%s" as it was already added to test suite "%s"',
                                $file->path(),
                                $testSuiteName,
                                $processed[$file->path()],
                            ),
                        );

                        continue;
                    }

                    $processed[$file->path()] = $testSuiteName;
                    $empty                    = false;

                    $testSuite->addTestFile($file->path(), $file->groups());
                }

                if (!$empty) {
                    $result->addTest($testSuite);
                }
            }

            return $result;
        } catch (FrameworkException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }
}
