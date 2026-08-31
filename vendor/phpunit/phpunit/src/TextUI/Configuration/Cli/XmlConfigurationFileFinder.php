<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\CliArguments;

use function getcwd;
use function is_dir;
use function is_file;
use function realpath;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class XmlConfigurationFileFinder
{
    public function find(Configuration $configuration): false|string
    {
        $useDefaultConfiguration = $configuration->useDefaultConfiguration();

        if ($configuration->hasConfigurationFile()) {
            if (is_dir($configuration->configurationFile())) {
                $candidate = $this->configurationFileInDirectory($configuration->configurationFile());

                if ($candidate !== false) {
                    return $candidate;
                }

                return false;
            }

            return $configuration->configurationFile();
        }

        if ($useDefaultConfiguration) {
            $directory = getcwd();

            if ($directory !== false) {
                $candidate = $this->configurationFileInDirectory($directory);

                if ($candidate !== false) {
                    return $candidate;
                }
            }
        }

        return false;
    }

    private function configurationFileInDirectory(string $directory): false|string
    {
        $candidates = [
            $directory . '/phpunit.xml',
            $directory . '/phpunit.dist.xml',
            $directory . '/phpunit.xml.dist',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return realpath($candidate);
            }
        }

        return false;
    }
}
