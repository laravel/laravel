<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

use PHPUnit\TextUI\CliArguments\Builder as CliConfigurationBuilder;
use PHPUnit\TextUI\CliArguments\Exception as CliConfigurationException;
use PHPUnit\TextUI\CliArguments\XmlConfigurationFileFinder;
use PHPUnit\TextUI\XmlConfiguration\DefaultConfiguration;
use PHPUnit\TextUI\XmlConfiguration\Exception as XmlConfigurationException;
use PHPUnit\TextUI\XmlConfiguration\Loader;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @codeCoverageIgnore
 */
final readonly class Builder
{
    /**
     * @param list<string> $argv
     *
     * @throws ConfigurationCannotBeBuiltException
     */
    public function build(array $argv): Configuration
    {
        try {
            $cliConfiguration  = (new CliConfigurationBuilder)->fromParameters($argv);
            $configurationFile = (new XmlConfigurationFileFinder)->find($cliConfiguration);
            $xmlConfiguration  = DefaultConfiguration::create();

            if ($configurationFile !== false) {
                $xmlConfiguration = (new Loader)->load($configurationFile);
            }

            return Registry::init(
                $cliConfiguration,
                $xmlConfiguration,
            );
        } catch (CliConfigurationException|XmlConfigurationException $e) {
            throw new ConfigurationCannotBeBuiltException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }
}
