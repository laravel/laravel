<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Api;

use const PHP_OS;
use const PHP_OS_FAMILY;
use const PHP_VERSION;
use function addcslashes;
use function array_column;
use function assert;
use function extension_loaded;
use function function_exists;
use function in_array;
use function ini_get;
use function method_exists;
use function phpversion;
use function preg_match;
use function sprintf;
use PHPUnit\Metadata\Parser\Registry;
use PHPUnit\Metadata\RequiresFunction;
use PHPUnit\Metadata\RequiresMethod;
use PHPUnit\Metadata\RequiresOperatingSystem;
use PHPUnit\Metadata\RequiresOperatingSystemFamily;
use PHPUnit\Metadata\RequiresPhp;
use PHPUnit\Metadata\RequiresPhpExtension;
use PHPUnit\Metadata\RequiresPhpunit;
use PHPUnit\Metadata\RequiresPhpunitExtension;
use PHPUnit\Metadata\RequiresSetting;
use PHPUnit\Runner\Version;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Requirements
{
    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @return list<string>
     */
    public function requirementsNotSatisfiedFor(string $className, string $methodName): array
    {
        $notSatisfied = [];

        foreach (Registry::parser()->forClassAndMethod($className, $methodName) as $metadata) {
            if ($metadata->isRequiresPhp()) {
                assert($metadata instanceof RequiresPhp);

                if (!$metadata->versionRequirement()->isSatisfiedBy(PHP_VERSION)) {
                    $notSatisfied[] = sprintf(
                        'PHP %s is required.',
                        $metadata->versionRequirement()->asString(),
                    );
                }
            }

            if ($metadata->isRequiresPhpExtension()) {
                assert($metadata instanceof RequiresPhpExtension);

                if (!extension_loaded($metadata->extension()) ||
                    ($metadata->hasVersionRequirement() &&
                        !$metadata->versionRequirement()->isSatisfiedBy(phpversion($metadata->extension())))) {
                    $notSatisfied[] = sprintf(
                        'PHP extension %s%s is required.',
                        $metadata->extension(),
                        $metadata->hasVersionRequirement() ? (' ' . $metadata->versionRequirement()->asString()) : '',
                    );
                }
            }

            if ($metadata->isRequiresPhpunit()) {
                assert($metadata instanceof RequiresPhpunit);

                if (!$metadata->versionRequirement()->isSatisfiedBy(Version::id())) {
                    $notSatisfied[] = sprintf(
                        'PHPUnit %s is required.',
                        $metadata->versionRequirement()->asString(),
                    );
                }
            }

            if ($metadata->isRequiresPhpunitExtension()) {
                assert($metadata instanceof RequiresPhpunitExtension);

                $configuration = ConfigurationRegistry::get();

                $extensionBootstrappers = array_column($configuration->extensionBootstrappers(), 'className');

                if ($configuration->noExtensions() || !in_array($metadata->extensionClass(), $extensionBootstrappers, true)) {
                    $notSatisfied[] = sprintf(
                        'PHPUnit extension "%s" is required.',
                        $metadata->extensionClass(),
                    );
                }
            }

            if ($metadata->isRequiresOperatingSystemFamily()) {
                assert($metadata instanceof RequiresOperatingSystemFamily);

                if ($metadata->operatingSystemFamily() !== PHP_OS_FAMILY) {
                    $notSatisfied[] = sprintf(
                        'Operating system %s is required.',
                        $metadata->operatingSystemFamily(),
                    );
                }
            }

            if ($metadata->isRequiresOperatingSystem()) {
                assert($metadata instanceof RequiresOperatingSystem);

                $pattern = sprintf(
                    '/%s/i',
                    addcslashes($metadata->operatingSystem(), '/'),
                );

                if (!preg_match($pattern, PHP_OS)) {
                    $notSatisfied[] = sprintf(
                        'Operating system %s is required.',
                        $metadata->operatingSystem(),
                    );
                }
            }

            if ($metadata->isRequiresFunction()) {
                assert($metadata instanceof RequiresFunction);

                if (!function_exists($metadata->functionName())) {
                    $notSatisfied[] = sprintf(
                        'Function %s() is required.',
                        $metadata->functionName(),
                    );
                }
            }

            if ($metadata->isRequiresMethod()) {
                assert($metadata instanceof RequiresMethod);

                if (!method_exists($metadata->className(), $metadata->methodName())) {
                    $notSatisfied[] = sprintf(
                        'Method %s::%s() is required.',
                        $metadata->className(),
                        $metadata->methodName(),
                    );
                }
            }

            if ($metadata->isRequiresSetting()) {
                assert($metadata instanceof RequiresSetting);

                if (ini_get($metadata->setting()) !== $metadata->value()) {
                    $notSatisfied[] = sprintf(
                        'Setting "%s" is required to be "%s".',
                        $metadata->setting(),
                        $metadata->value(),
                    );
                }
            }
        }

        return $notSatisfied;
    }

    public function requiresXdebug(string $className, string $methodName): bool
    {
        foreach (Registry::parser()->forClassAndMethod($className, $methodName) as $metadata) {
            if ($metadata->isRequiresPhpExtension()) {
                if ($metadata->extension() === 'xdebug') {
                    return true;
                }
            }
        }

        return false;
    }
}
