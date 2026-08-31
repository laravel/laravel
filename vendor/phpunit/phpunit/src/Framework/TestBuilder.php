<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function array_merge;
use function assert;
use PHPUnit\Metadata\Api\DataProvider;
use PHPUnit\Metadata\Api\Groups;
use PHPUnit\Metadata\Api\Requirements;
use PHPUnit\Metadata\BackupGlobals;
use PHPUnit\Metadata\BackupStaticProperties;
use PHPUnit\Metadata\ExcludeGlobalVariableFromBackup;
use PHPUnit\Metadata\ExcludeStaticPropertyFromBackup;
use PHPUnit\Metadata\Parser\Registry as MetadataRegistry;
use PHPUnit\Metadata\PreserveGlobalState;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestBuilder
{
    /**
     * @param ReflectionClass<TestCase> $theClass
     * @param non-empty-string          $methodName
     * @param list<non-empty-string>    $groups
     *
     * @throws InvalidDataProviderException
     */
    public function build(ReflectionClass $theClass, string $methodName, array $groups = []): Test
    {
        $className = $theClass->getName();

        $data = null;

        if ($this->requirementsSatisfied($className, $methodName)) {
            $data = (new DataProvider)->providedData($className, $methodName);
        }

        if ($data !== null) {
            return $this->buildDataProviderTestSuite(
                $methodName,
                $className,
                $data,
                $this->shouldTestMethodBeRunInSeparateProcess($className, $methodName),
                $this->shouldGlobalStateBePreserved($className, $methodName),
                $this->shouldAllTestMethodsOfTestClassBeRunInSingleSeparateProcess($className),
                $this->backupSettings($className, $methodName),
                $groups,
            );
        }

        $test = new $className($methodName);

        $this->configureTestCase(
            $test,
            $this->shouldTestMethodBeRunInSeparateProcess($className, $methodName),
            $this->shouldGlobalStateBePreserved($className, $methodName),
            $this->shouldAllTestMethodsOfTestClassBeRunInSingleSeparateProcess($className),
            $this->backupSettings($className, $methodName),
        );

        return $test;
    }

    /**
     * @param non-empty-string                                                                                                                                                  $methodName
     * @param class-string<TestCase>                                                                                                                                            $className
     * @param array<list<mixed>>                                                                                                                                                $data
     * @param array{backupGlobals: ?true, backupGlobalsExcludeList: list<string>, backupStaticProperties: ?true, backupStaticPropertiesExcludeList: array<string,list<string>>} $backupSettings
     * @param list<non-empty-string>                                                                                                                                            $groups
     */
    private function buildDataProviderTestSuite(string $methodName, string $className, array $data, bool $runTestInSeparateProcess, ?bool $preserveGlobalState, bool $runClassInSeparateProcess, array $backupSettings, array $groups): DataProviderTestSuite
    {
        $dataProviderTestSuite = DataProviderTestSuite::empty(
            $className . '::' . $methodName,
        );

        $groups = array_merge(
            $groups,
            (new Groups)->groups($className, $methodName),
        );

        foreach ($data as $_dataName => $_data) {
            $_test = new $className($methodName);

            $_test->setData($_dataName, $_data);

            $this->configureTestCase(
                $_test,
                $runTestInSeparateProcess,
                $preserveGlobalState,
                $runClassInSeparateProcess,
                $backupSettings,
            );

            $dataProviderTestSuite->addTest($_test, $groups);
        }

        return $dataProviderTestSuite;
    }

    /**
     * @param array{backupGlobals: ?true, backupGlobalsExcludeList: list<string>, backupStaticProperties: ?true, backupStaticPropertiesExcludeList: array<string,list<string>>} $backupSettings
     */
    private function configureTestCase(TestCase $test, bool $runTestInSeparateProcess, ?bool $preserveGlobalState, bool $runClassInSeparateProcess, array $backupSettings): void
    {
        if ($runTestInSeparateProcess) {
            $test->setRunTestInSeparateProcess(true);
        }

        if ($runClassInSeparateProcess) {
            $test->setRunClassInSeparateProcess(true);
        }

        if ($preserveGlobalState !== null) {
            $test->setPreserveGlobalState($preserveGlobalState);
        }

        if ($backupSettings['backupGlobals'] !== null) {
            $test->setBackupGlobals($backupSettings['backupGlobals']);
        } else {
            $test->setBackupGlobals(ConfigurationRegistry::get()->backupGlobals());
        }

        $test->setBackupGlobalsExcludeList($backupSettings['backupGlobalsExcludeList']);

        if ($backupSettings['backupStaticProperties'] !== null) {
            $test->setBackupStaticProperties($backupSettings['backupStaticProperties']);
        } else {
            $test->setBackupStaticProperties(ConfigurationRegistry::get()->backupStaticProperties());
        }

        $test->setBackupStaticPropertiesExcludeList($backupSettings['backupStaticPropertiesExcludeList']);
    }

    /**
     * @param class-string<TestCase> $className
     * @param non-empty-string       $methodName
     *
     * @return array{backupGlobals: ?true, backupGlobalsExcludeList: list<string>, backupStaticProperties: ?true, backupStaticPropertiesExcludeList: array<string,list<string>>}
     */
    private function backupSettings(string $className, string $methodName): array
    {
        $metadataForClass          = MetadataRegistry::parser()->forClass($className);
        $metadataForMethod         = MetadataRegistry::parser()->forMethod($className, $methodName);
        $metadataForClassAndMethod = MetadataRegistry::parser()->forClassAndMethod($className, $methodName);

        $backupGlobals            = null;
        $backupGlobalsExcludeList = [];

        if ($metadataForMethod->isBackupGlobals()->isNotEmpty()) {
            $metadata = $metadataForMethod->isBackupGlobals()->asArray()[0];

            assert($metadata instanceof BackupGlobals);

            if ($metadata->enabled()) {
                $backupGlobals = true;
            }
        } elseif ($metadataForClass->isBackupGlobals()->isNotEmpty()) {
            $metadata = $metadataForClass->isBackupGlobals()->asArray()[0];

            assert($metadata instanceof BackupGlobals);

            if ($metadata->enabled()) {
                $backupGlobals = true;
            }
        }

        foreach ($metadataForClassAndMethod->isExcludeGlobalVariableFromBackup() as $metadata) {
            assert($metadata instanceof ExcludeGlobalVariableFromBackup);

            $backupGlobalsExcludeList[] = $metadata->globalVariableName();
        }

        $backupStaticProperties            = null;
        $backupStaticPropertiesExcludeList = [];

        if ($metadataForMethod->isBackupStaticProperties()->isNotEmpty()) {
            $metadata = $metadataForMethod->isBackupStaticProperties()->asArray()[0];

            assert($metadata instanceof BackupStaticProperties);

            if ($metadata->enabled()) {
                $backupStaticProperties = true;
            }
        } elseif ($metadataForClass->isBackupStaticProperties()->isNotEmpty()) {
            $metadata = $metadataForClass->isBackupStaticProperties()->asArray()[0];

            assert($metadata instanceof BackupStaticProperties);

            if ($metadata->enabled()) {
                $backupStaticProperties = true;
            }
        }

        foreach ($metadataForClassAndMethod->isExcludeStaticPropertyFromBackup() as $metadata) {
            assert($metadata instanceof ExcludeStaticPropertyFromBackup);

            if (!isset($backupStaticPropertiesExcludeList[$metadata->className()])) {
                $backupStaticPropertiesExcludeList[$metadata->className()] = [];
            }

            $backupStaticPropertiesExcludeList[$metadata->className()][] = $metadata->propertyName();
        }

        return [
            'backupGlobals'                     => $backupGlobals,
            'backupGlobalsExcludeList'          => $backupGlobalsExcludeList,
            'backupStaticProperties'            => $backupStaticProperties,
            'backupStaticPropertiesExcludeList' => $backupStaticPropertiesExcludeList,
        ];
    }

    /**
     * @param class-string<TestCase> $className
     * @param non-empty-string       $methodName
     */
    private function shouldGlobalStateBePreserved(string $className, string $methodName): ?bool
    {
        $metadataForMethod = MetadataRegistry::parser()->forMethod($className, $methodName);

        if ($metadataForMethod->isPreserveGlobalState()->isNotEmpty()) {
            $metadata = $metadataForMethod->isPreserveGlobalState()->asArray()[0];

            assert($metadata instanceof PreserveGlobalState);

            return $metadata->enabled();
        }

        $metadataForClass = MetadataRegistry::parser()->forClass($className);

        if ($metadataForClass->isPreserveGlobalState()->isNotEmpty()) {
            $metadata = $metadataForClass->isPreserveGlobalState()->asArray()[0];

            assert($metadata instanceof PreserveGlobalState);

            return $metadata->enabled();
        }

        return null;
    }

    /**
     * @param class-string<TestCase> $className
     * @param non-empty-string       $methodName
     */
    private function shouldTestMethodBeRunInSeparateProcess(string $className, string $methodName): bool
    {
        if (MetadataRegistry::parser()->forClass($className)->isRunTestsInSeparateProcesses()->isNotEmpty()) {
            return true;
        }

        if (MetadataRegistry::parser()->forMethod($className, $methodName)->isRunInSeparateProcess()->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * @param class-string<TestCase> $className
     */
    private function shouldAllTestMethodsOfTestClassBeRunInSingleSeparateProcess(string $className): bool
    {
        return MetadataRegistry::parser()->forClass($className)->isRunClassInSeparateProcess()->isNotEmpty();
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    private function requirementsSatisfied(string $className, string $methodName): bool
    {
        return (new Requirements)->requirementsNotSatisfiedFor($className, $methodName) === [];
    }
}
