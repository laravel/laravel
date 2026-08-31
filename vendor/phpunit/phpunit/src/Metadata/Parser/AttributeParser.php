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

use const JSON_THROW_ON_ERROR;
use function assert;
use function class_exists;
use function json_decode;
use function method_exists;
use function sprintf;
use function str_starts_with;
use function strtolower;
use function trim;
use Error;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DependsExternal;
use PHPUnit\Framework\Attributes\DependsExternalUsingDeepClone;
use PHPUnit\Framework\Attributes\DependsExternalUsingShallowClone;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\DependsOnClassUsingDeepClone;
use PHPUnit\Framework\Attributes\DependsOnClassUsingShallowClone;
use PHPUnit\Framework\Attributes\DependsUsingDeepClone;
use PHPUnit\Framework\Attributes\DependsUsingShallowClone;
use PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\ExcludeGlobalVariableFromBackup;
use PHPUnit\Framework\Attributes\ExcludeStaticPropertyFromBackup;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\IgnorePhpunitDeprecations;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\PostCondition;
use PHPUnit\Framework\Attributes\PreCondition;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RequiresFunction;
use PHPUnit\Framework\Attributes\RequiresMethod;
use PHPUnit\Framework\Attributes\RequiresOperatingSystem;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RequiresPhpunit;
use PHPUnit\Framework\Attributes\RequiresPhpunitExtension;
use PHPUnit\Framework\Attributes\RequiresSetting;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\TestWithJson;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesFunction;
use PHPUnit\Framework\Attributes\UsesMethod;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use PHPUnit\Metadata\InvalidAttributeException;
use PHPUnit\Metadata\Metadata;
use PHPUnit\Metadata\MetadataCollection;
use PHPUnit\Metadata\Version\Requirement;
use ReflectionClass;
use ReflectionMethod;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class AttributeParser implements Parser
{
    /**
     * @param class-string $className
     */
    public function forClass(string $className): MetadataCollection
    {
        assert(class_exists($className));

        $reflector = new ReflectionClass($className);
        $result    = [];

        foreach ($reflector->getAttributes() as $attribute) {
            if (!str_starts_with($attribute->getName(), 'PHPUnit\\Framework\\Attributes\\')) {
                continue;
            }

            if (!class_exists($attribute->getName())) {
                continue;
            }

            try {
                $attributeInstance = $attribute->newInstance();
            } catch (Error $e) {
                throw new InvalidAttributeException(
                    $attribute->getName(),
                    'class ' . $className,
                    $reflector->getFileName(),
                    $reflector->getStartLine(),
                    $e->getMessage(),
                );
            }

            switch ($attribute->getName()) {
                case BackupGlobals::class:
                    assert($attributeInstance instanceof BackupGlobals);

                    $result[] = Metadata::backupGlobalsOnClass($attributeInstance->enabled());

                    break;

                case BackupStaticProperties::class:
                    assert($attributeInstance instanceof BackupStaticProperties);

                    $result[] = Metadata::backupStaticPropertiesOnClass($attributeInstance->enabled());

                    break;

                case CoversClass::class:
                    assert($attributeInstance instanceof CoversClass);

                    $result[] = Metadata::coversClass($attributeInstance->className());

                    break;

                case CoversTrait::class:
                    assert($attributeInstance instanceof CoversTrait);

                    $result[] = Metadata::coversTrait($attributeInstance->traitName());

                    break;

                case CoversFunction::class:
                    assert($attributeInstance instanceof CoversFunction);

                    $result[] = Metadata::coversFunction($attributeInstance->functionName());

                    break;

                case CoversMethod::class:
                    assert($attributeInstance instanceof CoversMethod);

                    $result[] = Metadata::coversMethod(
                        $attributeInstance->className(),
                        $attributeInstance->methodName(),
                    );

                    break;

                case CoversNothing::class:
                    $result[] = Metadata::coversNothingOnClass();

                    break;

                case DisableReturnValueGenerationForTestDoubles::class:
                    $result[] = Metadata::disableReturnValueGenerationForTestDoubles();

                    break;

                case DoesNotPerformAssertions::class:
                    $result[] = Metadata::doesNotPerformAssertionsOnClass();

                    break;

                case ExcludeGlobalVariableFromBackup::class:
                    assert($attributeInstance instanceof ExcludeGlobalVariableFromBackup);

                    $result[] = Metadata::excludeGlobalVariableFromBackupOnClass($attributeInstance->globalVariableName());

                    break;

                case ExcludeStaticPropertyFromBackup::class:
                    assert($attributeInstance instanceof ExcludeStaticPropertyFromBackup);

                    $result[] = Metadata::excludeStaticPropertyFromBackupOnClass(
                        $attributeInstance->className(),
                        $attributeInstance->propertyName(),
                    );

                    break;

                case Group::class:
                    assert($attributeInstance instanceof Group);

                    if (!$this->isSizeGroup($attributeInstance->name(), $className)) {
                        $result[] = Metadata::groupOnClass($attributeInstance->name());
                    }

                    break;

                case Large::class:
                    $result[] = Metadata::groupOnClass('large');

                    break;

                case Medium::class:
                    $result[] = Metadata::groupOnClass('medium');

                    break;

                case IgnoreDeprecations::class:
                    assert($attributeInstance instanceof IgnoreDeprecations);

                    $result[] = Metadata::ignoreDeprecationsOnClass();

                    break;

                case IgnorePhpunitDeprecations::class:
                    assert($attributeInstance instanceof IgnorePhpunitDeprecations);

                    $result[] = Metadata::ignorePhpunitDeprecationsOnClass();

                    break;

                case PreserveGlobalState::class:
                    assert($attributeInstance instanceof PreserveGlobalState);

                    $result[] = Metadata::preserveGlobalStateOnClass($attributeInstance->enabled());

                    break;

                case RequiresMethod::class:
                    assert($attributeInstance instanceof RequiresMethod);

                    $result[] = Metadata::requiresMethodOnClass(
                        $attributeInstance->className(),
                        $attributeInstance->methodName(),
                    );

                    break;

                case RequiresFunction::class:
                    assert($attributeInstance instanceof RequiresFunction);

                    $result[] = Metadata::requiresFunctionOnClass($attributeInstance->functionName());

                    break;

                case RequiresOperatingSystem::class:
                    assert($attributeInstance instanceof RequiresOperatingSystem);

                    $result[] = Metadata::requiresOperatingSystemOnClass($attributeInstance->regularExpression());

                    break;

                case RequiresOperatingSystemFamily::class:
                    assert($attributeInstance instanceof RequiresOperatingSystemFamily);

                    $result[] = Metadata::requiresOperatingSystemFamilyOnClass($attributeInstance->operatingSystemFamily());

                    break;

                case RequiresPhp::class:
                    assert($attributeInstance instanceof RequiresPhp);

                    $result[] = Metadata::requiresPhpOnClass(
                        Requirement::from(
                            $attributeInstance->versionRequirement(),
                        ),
                    );

                    break;

                case RequiresPhpExtension::class:
                    assert($attributeInstance instanceof RequiresPhpExtension);

                    $versionConstraint  = null;
                    $versionRequirement = $attributeInstance->versionRequirement();

                    if ($versionRequirement !== null) {
                        $versionConstraint = Requirement::from($versionRequirement);
                    }

                    $result[] = Metadata::requiresPhpExtensionOnClass(
                        $attributeInstance->extension(),
                        $versionConstraint,
                    );

                    break;

                case RequiresPhpunit::class:
                    assert($attributeInstance instanceof RequiresPhpunit);

                    $result[] = Metadata::requiresPhpunitOnClass(
                        Requirement::from(
                            $attributeInstance->versionRequirement(),
                        ),
                    );

                    break;

                case RequiresPhpunitExtension::class:
                    assert($attributeInstance instanceof RequiresPhpunitExtension);

                    $result[] = Metadata::requiresPhpunitExtensionOnClass(
                        $attributeInstance->extensionClass(),
                    );

                    break;

                case RequiresSetting::class:
                    assert($attributeInstance instanceof RequiresSetting);

                    $result[] = Metadata::requiresSettingOnClass(
                        $attributeInstance->setting(),
                        $attributeInstance->value(),
                    );

                    break;

                case RunClassInSeparateProcess::class:
                    $result[] = Metadata::runClassInSeparateProcess();

                    break;

                case RunTestsInSeparateProcesses::class:
                    $result[] = Metadata::runTestsInSeparateProcesses();

                    break;

                case Small::class:
                    $result[] = Metadata::groupOnClass('small');

                    break;

                case TestDox::class:
                    assert($attributeInstance instanceof TestDox);

                    $result[] = Metadata::testDoxOnClass($attributeInstance->text());

                    break;

                case Ticket::class:
                    assert($attributeInstance instanceof Ticket);

                    $result[] = Metadata::groupOnClass($attributeInstance->text());

                    break;

                case UsesClass::class:
                    assert($attributeInstance instanceof UsesClass);

                    $result[] = Metadata::usesClass($attributeInstance->className());

                    break;

                case UsesTrait::class:
                    assert($attributeInstance instanceof UsesTrait);

                    $result[] = Metadata::usesTrait($attributeInstance->traitName());

                    break;

                case UsesFunction::class:
                    assert($attributeInstance instanceof UsesFunction);

                    $result[] = Metadata::usesFunction($attributeInstance->functionName());

                    break;

                case UsesMethod::class:
                    assert($attributeInstance instanceof UsesMethod);

                    $result[] = Metadata::usesMethod(
                        $attributeInstance->className(),
                        $attributeInstance->methodName(),
                    );

                    break;
            }
        }

        return MetadataCollection::fromArray($result);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forMethod(string $className, string $methodName): MetadataCollection
    {
        assert(class_exists($className));
        assert(method_exists($className, $methodName));

        $reflector = new ReflectionMethod($className, $methodName);
        $result    = [];

        foreach ($reflector->getAttributes() as $attribute) {
            if (!str_starts_with($attribute->getName(), 'PHPUnit\\Framework\\Attributes\\')) {
                continue;
            }

            if (!class_exists($attribute->getName())) {
                continue;
            }

            try {
                $attributeInstance = $attribute->newInstance();
            } catch (Error $e) {
                throw new InvalidAttributeException(
                    $attribute->getName(),
                    'method ' . $className . '::' . $methodName . '()',
                    $reflector->getFileName(),
                    $reflector->getStartLine(),
                    $e->getMessage(),
                );
            }

            switch ($attribute->getName()) {
                case After::class:
                    assert($attributeInstance instanceof After);

                    $result[] = Metadata::after($attributeInstance->priority());

                    break;

                case AfterClass::class:
                    assert($attributeInstance instanceof AfterClass);

                    $result[] = Metadata::afterClass($attributeInstance->priority());

                    break;

                case BackupGlobals::class:
                    assert($attributeInstance instanceof BackupGlobals);

                    $result[] = Metadata::backupGlobalsOnMethod($attributeInstance->enabled());

                    break;

                case BackupStaticProperties::class:
                    assert($attributeInstance instanceof BackupStaticProperties);

                    $result[] = Metadata::backupStaticPropertiesOnMethod($attributeInstance->enabled());

                    break;

                case Before::class:
                    assert($attributeInstance instanceof Before);

                    $result[] = Metadata::before($attributeInstance->priority());

                    break;

                case BeforeClass::class:
                    assert($attributeInstance instanceof BeforeClass);

                    $result[] = Metadata::beforeClass($attributeInstance->priority());

                    break;

                case CoversNothing::class:
                    $result[] = Metadata::coversNothingOnMethod();

                    break;

                case DataProvider::class:
                    assert($attributeInstance instanceof DataProvider);

                    $result[] = Metadata::dataProvider($className, $attributeInstance->methodName());

                    break;

                case DataProviderExternal::class:
                    assert($attributeInstance instanceof DataProviderExternal);

                    $result[] = Metadata::dataProvider($attributeInstance->className(), $attributeInstance->methodName());

                    break;

                case Depends::class:
                    assert($attributeInstance instanceof Depends);

                    $result[] = Metadata::dependsOnMethod($className, $attributeInstance->methodName(), false, false);

                    break;

                case DependsUsingDeepClone::class:
                    assert($attributeInstance instanceof DependsUsingDeepClone);

                    $result[] = Metadata::dependsOnMethod($className, $attributeInstance->methodName(), true, false);

                    break;

                case DependsUsingShallowClone::class:
                    assert($attributeInstance instanceof DependsUsingShallowClone);

                    $result[] = Metadata::dependsOnMethod($className, $attributeInstance->methodName(), false, true);

                    break;

                case DependsExternal::class:
                    assert($attributeInstance instanceof DependsExternal);

                    $result[] = Metadata::dependsOnMethod($attributeInstance->className(), $attributeInstance->methodName(), false, false);

                    break;

                case DependsExternalUsingDeepClone::class:
                    assert($attributeInstance instanceof DependsExternalUsingDeepClone);

                    $result[] = Metadata::dependsOnMethod($attributeInstance->className(), $attributeInstance->methodName(), true, false);

                    break;

                case DependsExternalUsingShallowClone::class:
                    assert($attributeInstance instanceof DependsExternalUsingShallowClone);

                    $result[] = Metadata::dependsOnMethod($attributeInstance->className(), $attributeInstance->methodName(), false, true);

                    break;

                case DependsOnClass::class:
                    assert($attributeInstance instanceof DependsOnClass);

                    $result[] = Metadata::dependsOnClass($attributeInstance->className(), false, false);

                    break;

                case DependsOnClassUsingDeepClone::class:
                    assert($attributeInstance instanceof DependsOnClassUsingDeepClone);

                    $result[] = Metadata::dependsOnClass($attributeInstance->className(), true, false);

                    break;

                case DependsOnClassUsingShallowClone::class:
                    assert($attributeInstance instanceof DependsOnClassUsingShallowClone);

                    $result[] = Metadata::dependsOnClass($attributeInstance->className(), false, true);

                    break;

                case DoesNotPerformAssertions::class:
                    assert($attributeInstance instanceof DoesNotPerformAssertions);

                    $result[] = Metadata::doesNotPerformAssertionsOnMethod();

                    break;

                case ExcludeGlobalVariableFromBackup::class:
                    assert($attributeInstance instanceof ExcludeGlobalVariableFromBackup);

                    $result[] = Metadata::excludeGlobalVariableFromBackupOnMethod($attributeInstance->globalVariableName());

                    break;

                case ExcludeStaticPropertyFromBackup::class:
                    assert($attributeInstance instanceof ExcludeStaticPropertyFromBackup);

                    $result[] = Metadata::excludeStaticPropertyFromBackupOnMethod(
                        $attributeInstance->className(),
                        $attributeInstance->propertyName(),
                    );

                    break;

                case Group::class:
                    assert($attributeInstance instanceof Group);

                    if (!$this->isSizeGroup($attributeInstance->name(), $className, $methodName)) {
                        $result[] = Metadata::groupOnMethod($attributeInstance->name());
                    }

                    break;

                case IgnoreDeprecations::class:
                    assert($attributeInstance instanceof IgnoreDeprecations);

                    $result[] = Metadata::ignoreDeprecationsOnMethod();

                    break;

                case IgnorePhpunitDeprecations::class:
                    assert($attributeInstance instanceof IgnorePhpunitDeprecations);

                    $result[] = Metadata::ignorePhpunitDeprecationsOnMethod();

                    break;

                case PostCondition::class:
                    assert($attributeInstance instanceof PostCondition);

                    $result[] = Metadata::postCondition($attributeInstance->priority());

                    break;

                case PreCondition::class:
                    assert($attributeInstance instanceof PreCondition);

                    $result[] = Metadata::preCondition($attributeInstance->priority());

                    break;

                case PreserveGlobalState::class:
                    assert($attributeInstance instanceof PreserveGlobalState);

                    $result[] = Metadata::preserveGlobalStateOnMethod($attributeInstance->enabled());

                    break;

                case RequiresMethod::class:
                    assert($attributeInstance instanceof RequiresMethod);

                    $result[] = Metadata::requiresMethodOnMethod(
                        $attributeInstance->className(),
                        $attributeInstance->methodName(),
                    );

                    break;

                case RequiresFunction::class:
                    assert($attributeInstance instanceof RequiresFunction);

                    $result[] = Metadata::requiresFunctionOnMethod($attributeInstance->functionName());

                    break;

                case RequiresOperatingSystem::class:
                    assert($attributeInstance instanceof RequiresOperatingSystem);

                    $result[] = Metadata::requiresOperatingSystemOnMethod($attributeInstance->regularExpression());

                    break;

                case RequiresOperatingSystemFamily::class:
                    assert($attributeInstance instanceof RequiresOperatingSystemFamily);

                    $result[] = Metadata::requiresOperatingSystemFamilyOnMethod($attributeInstance->operatingSystemFamily());

                    break;

                case RequiresPhp::class:
                    assert($attributeInstance instanceof RequiresPhp);

                    $result[] = Metadata::requiresPhpOnMethod(
                        Requirement::from(
                            $attributeInstance->versionRequirement(),
                        ),
                    );

                    break;

                case RequiresPhpExtension::class:
                    assert($attributeInstance instanceof RequiresPhpExtension);

                    $versionConstraint  = null;
                    $versionRequirement = $attributeInstance->versionRequirement();

                    if ($versionRequirement !== null) {
                        $versionConstraint = Requirement::from($versionRequirement);
                    }

                    $result[] = Metadata::requiresPhpExtensionOnMethod(
                        $attributeInstance->extension(),
                        $versionConstraint,
                    );

                    break;

                case RequiresPhpunit::class:
                    assert($attributeInstance instanceof RequiresPhpunit);

                    $result[] = Metadata::requiresPhpunitOnMethod(
                        Requirement::from(
                            $attributeInstance->versionRequirement(),
                        ),
                    );

                    break;

                case RequiresPhpunitExtension::class:
                    assert($attributeInstance instanceof RequiresPhpunitExtension);

                    $result[] = Metadata::requiresPhpunitExtensionOnMethod(
                        $attributeInstance->extensionClass(),
                    );

                    break;

                case RequiresSetting::class:
                    assert($attributeInstance instanceof RequiresSetting);

                    $result[] = Metadata::requiresSettingOnMethod(
                        $attributeInstance->setting(),
                        $attributeInstance->value(),
                    );

                    break;

                case RunInSeparateProcess::class:
                    $result[] = Metadata::runInSeparateProcess();

                    break;

                case Test::class:
                    $result[] = Metadata::test();

                    break;

                case TestDox::class:
                    assert($attributeInstance instanceof TestDox);

                    $result[] = Metadata::testDoxOnMethod($attributeInstance->text());

                    break;

                case TestWith::class:
                    assert($attributeInstance instanceof TestWith);

                    $result[] = Metadata::testWith($attributeInstance->data(), $attributeInstance->name());

                    break;

                case TestWithJson::class:
                    assert($attributeInstance instanceof TestWithJson);

                    $result[] = Metadata::testWith(
                        json_decode($attributeInstance->json(), true, 512, JSON_THROW_ON_ERROR),
                        $attributeInstance->name(),
                    );

                    break;

                case Ticket::class:
                    assert($attributeInstance instanceof Ticket);

                    $result[] = Metadata::groupOnMethod($attributeInstance->text());

                    break;

                case WithoutErrorHandler::class:
                    assert($attributeInstance instanceof WithoutErrorHandler);

                    $result[] = Metadata::withoutErrorHandler();

                    break;
            }
        }

        return MetadataCollection::fromArray($result);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forClassAndMethod(string $className, string $methodName): MetadataCollection
    {
        return $this->forClass($className)->mergeWith(
            $this->forMethod($className, $methodName),
        );
    }

    /**
     * @param non-empty-string  $groupName
     * @param class-string      $testClassName
     * @param ?non-empty-string $testMethodName
     */
    private function isSizeGroup(string $groupName, string $testClassName, ?string $testMethodName = null): bool
    {
        $_groupName = strtolower(trim($groupName));

        if ($_groupName !== 'small' && $_groupName !== 'medium' && $_groupName !== 'large') {
            return false;
        }

        EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
            sprintf(
                'Group name "%s" is not allowed for %s %s%s%s',
                $_groupName,
                $testMethodName !== null ? 'method' : 'class',
                $testClassName,
                $testMethodName !== null ? '::' : '',
                $testMethodName !== null ? $testMethodName : '',
            ),
        );

        return true;
    }
}
