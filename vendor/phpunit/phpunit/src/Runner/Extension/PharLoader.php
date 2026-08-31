<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Extension;

use function count;
use function explode;
use function extension_loaded;
use function implode;
use function is_file;
use function sprintf;
use function str_contains;
use PharIo\Manifest\ApplicationName;
use PharIo\Manifest\Exception as ManifestException;
use PharIo\Manifest\ManifestLoader;
use PharIo\Version\Version as PharIoVersion;
use PHPUnit\Event;
use PHPUnit\Runner\Version;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class PharLoader
{
    /**
     * @param non-empty-string $directory
     *
     * @return list<string>
     */
    public function loadPharExtensionsInDirectory(string $directory): array
    {
        $pharExtensionLoaded = extension_loaded('phar');
        $loadedExtensions    = [];

        foreach ((new FileIteratorFacade)->getFilesAsArray($directory, '.phar') as $file) {
            if (!$pharExtensionLoaded) {
                Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                    sprintf(
                        'Cannot load extension from %s because the PHAR extension is not available',
                        $file,
                    ),
                );

                continue;
            }

            if (!is_file('phar://' . $file . '/manifest.xml')) {
                Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                    sprintf(
                        '%s is not an extension for PHPUnit',
                        $file,
                    ),
                );

                continue;
            }

            try {
                $applicationName = new ApplicationName('phpunit/phpunit');
                $version         = new PharIoVersion($this->phpunitVersion());
                $manifest        = ManifestLoader::fromFile('phar://' . $file . '/manifest.xml');

                if (!$manifest->isExtensionFor($applicationName)) {
                    Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                        sprintf(
                            '%s is not an extension for PHPUnit',
                            $file,
                        ),
                    );

                    continue;
                }

                if (!$manifest->isExtensionFor($applicationName, $version)) {
                    Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                        sprintf(
                            '%s is not compatible with PHPUnit %s',
                            $file,
                            Version::series(),
                        ),
                    );

                    continue;
                }
            } catch (ManifestException $e) {
                Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                    sprintf(
                        'Cannot load extension from %s: %s',
                        $file,
                        $e->getMessage(),
                    ),
                );

                continue;
            }

            try {
                @require $file;
            } catch (Throwable $t) {
                Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                    sprintf(
                        'Cannot load extension from %s: %s',
                        $file,
                        $t->getMessage(),
                    ),
                );

                continue;
            }

            $loadedExtensions[] = $manifest->getName()->asString() . ' ' . $manifest->getVersion()->getVersionString();

            Event\Facade::emitter()->testRunnerLoadedExtensionFromPhar(
                $file,
                $manifest->getName()->asString(),
                $manifest->getVersion()->getVersionString(),
            );
        }

        return $loadedExtensions;
    }

    private function phpunitVersion(): string
    {
        $version = Version::id();

        if (!str_contains($version, '-')) {
            return $version;
        }

        $parts = explode('.', explode('-', $version)[0]);

        if (count($parts) === 2) {
            $parts[] = 0;
        }

        return implode('.', $parts);
    }
}
