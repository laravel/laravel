<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Parser;

use function array_merge;
use function assert;
use function class_exists;
use function count;
use function explode;
use function method_exists;
use function preg_replace;
use function rtrim;
use function sprintf;
use function str_contains;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Metadata\Annotation\Parser\Registry as AnnotationRegistry;
use PHPUnit\Metadata\AnnotationsAreNotSupportedForInternalClassesException;
use PHPUnit\Metadata\InvalidVersionRequirementException;
use PHPUnit\Metadata\Metadata;
use PHPUnit\Metadata\MetadataCollection;
use PHPUnit\Metadata\ReflectionException;
use PHPUnit\Metadata\Version\ComparisonRequirement;
use PHPUnit\Metadata\Version\ConstraintRequirement;
use PHPUnit\Util\InvalidVersionOperatorException;
use PHPUnit\Util\VersionComparisonOperator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class AnnotationParser implements Parser
{
    /**
     * @var array<string, true>
     */
    private static array $deprecationEmittedForClass = [];

    /**
     * @var array<string, true>
     */
    private static array $deprecationEmittedForMethod = [];

    /**
     * @param class-string $className
     *
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     * @throws InvalidVersionOperatorException
     * @throws ReflectionException
     */
    public function forClass(string $className): MetadataCollection
    {
        assert(class_exists($className));

        $result = [];

        foreach (AnnotationRegistry::getInstance()->forClassName($className)->symbolAnnotations() as $annotation => $values) {
            switch ($annotation) {
                case 'backupGlobals':
                    $result[] = Metadata::backupGlobalsOnClass($this->stringToBool($values[0]));

                    break;

                case 'backupStaticAttributes':
                case 'backupStaticProperties':
                    $result[] = Metadata::backupStaticPropertiesOnClass($this->stringToBool($values[0]));

                    break;

                case 'covers':
                    foreach ($values as $value) {
                        $value = $this->cleanUpCoversOrUsesTarget($value);

                        $result[] = Metadata::coversOnClass($value);
                    }

                    break;

                case 'coversDefaultClass':
                    foreach ($values as $value) {
                        $result[] = Metadata::coversDefaultClass($value);
                    }

                    break;

                case 'coversNothing':
                    $result[] = Metadata::coversNothingOnClass();

                    break;

                case 'doesNotPerformAssertions':
                    $result[] = Metadata::doesNotPerformAssertionsOnClass();

                    break;

                case 'group':
                case 'ticket':
                    foreach ($values as $value) {
                        $result[] = Metadata::groupOnClass($value);
                    }

                    break;

                case 'large':
                    $result[] = Metadata::groupOnClass('large');

                    break;

                case 'medium':
                    $result[] = Metadata::groupOnClass('medium');

                    break;

                case 'preserveGlobalState':
                    $result[] = Metadata::preserveGlobalStateOnClass($this->stringToBool($values[0]));

                    break;

                case 'runClassInSeparateProcess':
                    $result[] = Metadata::runClassInSeparateProcess();

                    break;

                case 'runTestsInSeparateProcesses':
                    $result[] = Metadata::runTestsInSeparateProcesses();

                    break;

                case 'small':
                    $result[] = Metadata::groupOnClass('small');

                    break;

                case 'testdox':
                    $result[] = Metadata::testDoxOnClass($values[0]);

                    break;

                case 'uses':
                    foreach ($values as $value) {
                        $value = $this->cleanUpCoversOrUsesTarget($value);

                        $result[] = Metadata::usesOnClass($value);
                    }

                    break;

                case 'usesDefaultClass':
                    foreach ($values as $value) {
                        $result[] = Metadata::usesDefaultClass($value);
                    }

                    break;
            }
        }

        try {
            $result = array_merge(
                $result,
                $this->parseRequirements(
                    AnnotationRegistry::getInstance()->forClassName($className)->requirements(),
                    'class',
                ),
            );
        } catch (InvalidVersionRequirementException $e) {
            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                sprintf(
                    'Class %s is annotated using an invalid version requirement: %s',
                    $className,
                    $e->getMessage(),
                ),
            );
        }

        if (!empty($result) &&
            !isset(self::$deprecationEmittedForClass[$className]) &&
            !str_starts_with($className, 'PHPUnit\TestFixture')) {
            self::$deprecationEmittedForClass[$className] = true;

            EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation(
                sprintf(
                    'Metadata found in doc-comment for class %s. Metadata in doc-comments is deprecated and will no longer be supported in PHPUnit 12. Update your test code to use attributes instead.',
                    $className,
                ),
            );
        }

        return MetadataCollection::fromArray($result);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     * @throws InvalidVersionOperatorException
     * @throws ReflectionException
     */
    public function forMethod(string $className, string $methodName): MetadataCollection
    {
        assert(class_exists($className));
        assert(method_exists($className, $methodName));

        $result = [];

        foreach (AnnotationRegistry::getInstance()->forMethod($className, $methodName)->symbolAnnotations() as $annotation => $values) {
            switch ($annotation) {
                case 'after':
                    $result[] = Metadata::after(0);

                    break;

                case 'afterClass':
                    $result[] = Metadata::afterClass(0);

                    break;

                case 'backupGlobals':
                    $result[] = Metadata::backupGlobalsOnMethod($this->stringToBool($values[0]));

                    break;

                case 'backupStaticAttributes':
                case 'backupStaticProperties':
                    $result[] = Metadata::backupStaticPropertiesOnMethod($this->stringToBool($values[0]));

                    break;

                case 'before':
                    $result[] = Metadata::before(0);

                    break;

                case 'beforeClass':
                    $result[] = Metadata::beforeClass(0);

                    break;

                case 'covers':
                    foreach ($values as $value) {
                        $value = $this->cleanUpCoversOrUsesTarget($value);

                        $result[] = Metadata::coversOnMethod($value);
                    }

                    break;

                case 'coversNothing':
                    $result[] = Metadata::coversNothingOnMethod();

                    break;

                case 'dataProvider':
                    foreach ($values as $value) {
                        $value = rtrim($value, " ()\n\r\t\v\x00");

                        if (str_contains($value, '::')) {
                            $result[] = Metadata::dataProvider(...explode('::', $value));

                            continue;
                        }

                        $result[] = Metadata::dataProvider($className, $value);
                    }

                    break;

                case 'depends':
                    foreach ($values as $value) {
                        $deepClone    = false;
                        $shallowClone = false;

                        if (str_starts_with($value, 'clone ')) {
                            $deepClone = true;
                            $value     = substr($value, strlen('clone '));
                        } elseif (str_starts_with($value, '!clone ')) {
                            $value = substr($value, strlen('!clone '));
                        } elseif (str_starts_with($value, 'shallowClone ')) {
                            $shallowClone = true;
                            $value        = substr($value, strlen('shallowClone '));
                        } elseif (str_starts_with($value, '!shallowClone ')) {
                            $value = substr($value, strlen('!shallowClone '));
                        }

                        if (str_contains($value, '::')) {
                            [$_className, $_methodName] = explode('::', $value);

                            assert($_className !== '');
                            assert($_methodName !== '');

                            if ($_methodName === 'class') {
                                $result[] = Metadata::dependsOnClass($_className, $deepClone, $shallowClone);

                                continue;
                            }

                            $result[] = Metadata::dependsOnMethod($_className, $_methodName, $deepClone, $shallowClone);

                            continue;
                        }

                        $result[] = Metadata::dependsOnMethod($className, $value, $deepClone, $shallowClone);
                    }

                    break;

                case 'doesNotPerformAssertions':
                    $result[] = Metadata::doesNotPerformAssertionsOnMethod();

                    break;

                case 'excludeGlobalVariableFromBackup':
                    foreach ($values as $value) {
                        $result[] = Metadata::excludeGlobalVariableFromBackupOnMethod($value);
                    }

                    break;

                case 'excludeStaticPropertyFromBackup':
                    foreach ($values as $value) {
                        $tmp = explode(' ', $value);

                        if (count($tmp) !== 2) {
                            continue;
                        }

                        $result[] = Metadata::excludeStaticPropertyFromBackupOnMethod(
                            trim($tmp[0]),
                            trim($tmp[1]),
                        );
                    }

                    break;

                case 'group':
                case 'ticket':
                    foreach ($values as $value) {
                        $result[] = Metadata::groupOnMethod($value);
                    }

                    break;

                case 'large':
                    $result[] = Metadata::groupOnMethod('large');

                    break;

                case 'medium':
                    $result[] = Metadata::groupOnMethod('medium');

                    break;

                case 'postCondition':
                    $result[] = Metadata::postCondition(0);

                    break;

                case 'preCondition':
                    $result[] = Metadata::preCondition(0);

                    break;

                case 'preserveGlobalState':
                    $result[] = Metadata::preserveGlobalStateOnMethod($this->stringToBool($values[0]));

                    break;

                case 'runInSeparateProcess':
                    $result[] = Metadata::runInSeparateProcess();

                    break;

                case 'small':
                    $result[] = Metadata::groupOnMethod('small');

                    break;

                case 'test':
                    $result[] = Metadata::test();

                    break;

                case 'testdox':
                    $result[] = Metadata::testDoxOnMethod($values[0]);

                    break;

                case 'uses':
                    foreach ($values as $value) {
                        $value = $this->cleanUpCoversOrUsesTarget($value);

                        $result[] = Metadata::usesOnMethod($value);
                    }

                    break;
            }
        }

        try {
            $result = array_merge(
                $result,
                $this->parseRequirements(
                    AnnotationRegistry::getInstance()->forMethod($className, $methodName)->requirements(),
                    'method',
                ),
            );
        } catch (InvalidVersionRequirementException $e) {
            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                sprintf(
                    'Method %s::%s is annotated using an invalid version requirement: %s',
                    $className,
                    $methodName,
                    $e->getMessage(),
                ),
            );
        }

        if (!empty($result) &&
            !isset(self::$deprecationEmittedForMethod[$className . '::' . $methodName]) &&
            !str_starts_with($className, 'PHPUnit\TestFixture')) {
            self::$deprecationEmittedForMethod[$className . '::' . $methodName] = true;

            EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation(
                sprintf(
                    'Metadata found in doc-comment for method %s::%s(). Metadata in doc-comments is deprecated and will no longer be supported in PHPUnit 12. Update your test code to use attributes instead.',
                    $className,
                    $methodName,
                ),
            );
        }

        return MetadataCollection::fromArray($result);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     * @throws InvalidVersionOperatorException
     * @throws ReflectionException
     */
    public function forClassAndMethod(string $className, string $methodName): MetadataCollection
    {
        return $this->forClass($className)->mergeWith(
            $this->forMethod($className, $methodName),
        );
    }

    private function stringToBool(string $value): bool
    {
        if ($value === 'enabled') {
            return true;
        }

        return false;
    }

    private function cleanUpCoversOrUsesTarget(string $value): string
    {
        $value = preg_replace('/[\s()]+$/', '', $value);

        return explode(' ', $value, 2)[0];
    }

    /**
     * @throws InvalidVersionOperatorException
     *
     * @return list<Metadata>
     *
     * @phpstan-ignore missingType.iterableValue
     */
    private function parseRequirements(array $requirements, string $level): array
    {
        $result = [];

        if (!empty($requirements['PHP'])) {
            $versionRequirement = new ComparisonRequirement(
                $requirements['PHP']['version'],
                new VersionComparisonOperator(empty($requirements['PHP']['operator']) ? '>=' : $requirements['PHP']['operator']),
            );

            if ($level === 'class') {
                $result[] = Metadata::requiresPhpOnClass($versionRequirement);
            } else {
                $result[] = Metadata::requiresPhpOnMethod($versionRequirement);
            }
        } elseif (!empty($requirements['PHP_constraint'])) {
            $versionRequirement = new ConstraintRequirement($requirements['PHP_constraint']['constraint']);

            if ($level === 'class') {
                $result[] = Metadata::requiresPhpOnClass($versionRequirement);
            } else {
                $result[] = Metadata::requiresPhpOnMethod($versionRequirement);
            }
        }

        if (!empty($requirements['extensions'])) {
            foreach ($requirements['extensions'] as $extension) {
                if (isset($requirements['extension_versions'][$extension])) {
                    continue;
                }

                if ($level === 'class') {
                    $result[] = Metadata::requiresPhpExtensionOnClass($extension, null);
                } else {
                    $result[] = Metadata::requiresPhpExtensionOnMethod($extension, null);
                }
            }
        }

        if (!empty($requirements['extension_versions'])) {
            foreach ($requirements['extension_versions'] as $extension => $version) {
                $versionRequirement = new ComparisonRequirement(
                    $version['version'],
                    new VersionComparisonOperator(empty($version['operator']) ? '>=' : $version['operator']),
                );

                if ($level === 'class') {
                    $result[] = Metadata::requiresPhpExtensionOnClass($extension, $versionRequirement);
                } else {
                    $result[] = Metadata::requiresPhpExtensionOnMethod($extension, $versionRequirement);
                }
            }
        }

        if (!empty($requirements['PHPUnit'])) {
            $versionRequirement = new ComparisonRequirement(
                $requirements['PHPUnit']['version'],
                new VersionComparisonOperator(empty($requirements['PHPUnit']['operator']) ? '>=' : $requirements['PHPUnit']['operator']),
            );

            if ($level === 'class') {
                $result[] = Metadata::requiresPhpunitOnClass($versionRequirement);
            } else {
                $result[] = Metadata::requiresPhpunitOnMethod($versionRequirement);
            }
        } elseif (!empty($requirements['PHPUnit_constraint'])) {
            $versionRequirement = new ConstraintRequirement($requirements['PHPUnit_constraint']['constraint']);

            if ($level === 'class') {
                $result[] = Metadata::requiresPhpunitOnClass($versionRequirement);
            } else {
                $result[] = Metadata::requiresPhpunitOnMethod($versionRequirement);
            }
        }

        if (!empty($requirements['OSFAMILY'])) {
            if ($level === 'class') {
                $result[] = Metadata::requiresOperatingSystemFamilyOnClass($requirements['OSFAMILY']);
            } else {
                $result[] = Metadata::requiresOperatingSystemFamilyOnMethod($requirements['OSFAMILY']);
            }
        }

        if (!empty($requirements['OS'])) {
            if ($level === 'class') {
                $result[] = Metadata::requiresOperatingSystemOnClass($requirements['OS']);
            } else {
                $result[] = Metadata::requiresOperatingSystemOnMethod($requirements['OS']);
            }
        }

        if (!empty($requirements['functions'])) {
            foreach ($requirements['functions'] as $function) {
                $pieces = explode('::', $function);

                if (count($pieces) === 2) {
                    if ($level === 'class') {
                        $result[] = Metadata::requiresMethodOnClass($pieces[0], $pieces[1]);
                    } else {
                        $result[] = Metadata::requiresMethodOnMethod($pieces[0], $pieces[1]);
                    }
                } elseif ($level === 'class') {
                    $result[] = Metadata::requiresFunctionOnClass($function);
                } else {
                    $result[] = Metadata::requiresFunctionOnMethod($function);
                }
            }
        }

        if (!empty($requirements['setting'])) {
            foreach ($requirements['setting'] as $setting => $value) {
                if ($level === 'class') {
                    $result[] = Metadata::requiresSettingOnClass($setting, $value);
                } else {
                    $result[] = Metadata::requiresSettingOnMethod($setting, $value);
                }
            }
        }

        return $result;
    }
}
