<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DataCollector;

use Composer\InstalledVersions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\VarDumper\Caster\ClassStub;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ConfigDataCollector extends DataCollector implements LateDataCollectorInterface
{
    private KernelInterface $kernel;

    /**
     * Sets the Kernel associated with this Request.
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $eom = \DateTimeImmutable::createFromFormat('d/m/Y', '01/'.Kernel::END_OF_MAINTENANCE);
        $eol = \DateTimeImmutable::createFromFormat('d/m/Y', '01/'.Kernel::END_OF_LIFE);

        $xdebugMode = getenv('XDEBUG_MODE') ?: \ini_get('xdebug.mode');

        $this->data = [
            'token' => $response->headers->get('X-Debug-Token'),
            'symfony_version' => class_exists(InstalledVersions::class) ? InstalledVersions::getPrettyVersion('symfony/http-kernel') ?? InstalledVersions::getPrettyVersion('symfony/symfony') : Kernel::MAJOR_VERSION.'.'.Kernel::MINOR_VERSION,
            'symfony_minor_version' => \sprintf('%s.%s', Kernel::MAJOR_VERSION, Kernel::MINOR_VERSION),
            'symfony_lts' => 4 === Kernel::MINOR_VERSION,
            'symfony_state' => $this->determineSymfonyState(),
            'symfony_eom' => $eom->format('F Y'),
            'symfony_eol' => $eol->format('F Y'),
            'env' => isset($this->kernel) ? $this->kernel->getEnvironment() : 'n/a',
            'debug' => isset($this->kernel) ? $this->kernel->isDebug() : 'n/a',
            'php_version' => \PHP_VERSION,
            'php_architecture' => \PHP_INT_SIZE * 8,
            'php_intl_locale' => class_exists(\Locale::class, false) && \Locale::getDefault() ? \Locale::getDefault() : 'n/a',
            'php_timezone' => date_default_timezone_get(),
            'xdebug_enabled' => \extension_loaded('xdebug'),
            'xdebug_status' => \extension_loaded('xdebug') ? ($xdebugMode && 'off' !== $xdebugMode ? 'Enabled ('.$xdebugMode.')' : 'Not enabled') : 'Not installed',
            'apcu_enabled' => \extension_loaded('apcu') && filter_var(\ini_get('apc.enabled'), \FILTER_VALIDATE_BOOL),
            'apcu_status' => \extension_loaded('apcu') ? (filter_var(\ini_get('apc.enabled'), \FILTER_VALIDATE_BOOLEAN) ? 'Enabled' : 'Not enabled') : 'Not installed',
            'zend_opcache_enabled' => \extension_loaded('Zend OPcache') && filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOL),
            'zend_opcache_status' => \extension_loaded('Zend OPcache') ? (filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOLEAN) ? 'Enabled' : 'Not enabled') : 'Not installed',
            'bundles' => [],
            'sapi_name' => \PHP_SAPI,
            'runner_class' => $this->determineRunnerClass(),
        ];

        if (isset($this->kernel)) {
            foreach ($this->kernel->getBundles() as $name => $bundle) {
                $this->data['bundles'][$name] = new ClassStub($bundle::class);
            }
        }

        if (preg_match('~^(\d+(?:\.\d+)*)(.+)?$~', $this->data['php_version'], $matches) && isset($matches[2])) {
            $this->data['php_version'] = $matches[1];
            $this->data['php_version_extra'] = $matches[2];
        }
    }

    public function lateCollect(): void
    {
        $this->data = $this->cloneVar($this->data);
    }

    /**
     * Gets the token.
     */
    public function getToken(): ?string
    {
        return $this->data['token'];
    }

    /**
     * Gets the Symfony version.
     */
    public function getSymfonyVersion(): string
    {
        return $this->data['symfony_version'];
    }

    /**
     * Returns the state of the current Symfony release
     * as one of: unknown, dev, stable, eom, eol.
     */
    public function getSymfonyState(): string
    {
        return $this->data['symfony_state'];
    }

    /**
     * Returns the minor Symfony version used (without patch numbers of extra
     * suffix like "RC", "beta", etc.).
     */
    public function getSymfonyMinorVersion(): string
    {
        return $this->data['symfony_minor_version'];
    }

    public function isSymfonyLts(): bool
    {
        return $this->data['symfony_lts'];
    }

    /**
     * Returns the human readable date when this Symfony version ends its
     * maintenance period.
     */
    public function getSymfonyEom(): string
    {
        return $this->data['symfony_eom'];
    }

    /**
     * Returns the human readable date when this Symfony version reaches its
     * "end of life" and won't receive bugs or security fixes.
     */
    public function getSymfonyEol(): string
    {
        return $this->data['symfony_eol'];
    }

    /**
     * Gets the PHP version.
     */
    public function getPhpVersion(): string
    {
        return $this->data['php_version'];
    }

    /**
     * Gets the PHP version extra part.
     */
    public function getPhpVersionExtra(): ?string
    {
        return $this->data['php_version_extra'] ?? null;
    }

    public function getPhpArchitecture(): int
    {
        return $this->data['php_architecture'];
    }

    public function getPhpIntlLocale(): string
    {
        return $this->data['php_intl_locale'];
    }

    public function getPhpTimezone(): string
    {
        return $this->data['php_timezone'];
    }

    /**
     * Gets the environment.
     */
    public function getEnv(): string
    {
        return $this->data['env'];
    }

    /**
     * Returns true if the debug is enabled.
     *
     * @return bool|string true if debug is enabled, false otherwise or a string if no kernel was set
     */
    public function isDebug(): bool|string
    {
        return $this->data['debug'];
    }

    /**
     * Returns true if the Xdebug is enabled.
     */
    public function hasXdebug(): bool
    {
        return $this->data['xdebug_enabled'];
    }

    public function getXdebugStatus(): string
    {
        return $this->data['xdebug_status'];
    }

    /**
     * Returns true if the function xdebug_info is available.
     */
    public function hasXdebugInfo(): bool
    {
        return \function_exists('xdebug_info');
    }

    /**
     * Returns true if APCu is enabled.
     */
    public function hasApcu(): bool
    {
        return $this->data['apcu_enabled'];
    }

    public function getApcuStatus(): string
    {
        return $this->data['apcu_status'];
    }

    /**
     * Returns true if Zend OPcache is enabled.
     */
    public function hasZendOpcache(): bool
    {
        return $this->data['zend_opcache_enabled'];
    }

    public function getZendOpcacheStatus(): string
    {
        return $this->data['zend_opcache_status'];
    }

    public function getBundles(): array|Data
    {
        return $this->data['bundles'];
    }

    /**
     * Gets the PHP SAPI name.
     */
    public function getSapiName(): string
    {
        return $this->data['sapi_name'];
    }

    public function getRunnerClass(): ?string
    {
        return $this->data['runner_class'];
    }

    public function getName(): string
    {
        return 'config';
    }

    private function determineSymfonyState(): string
    {
        $now = new \DateTimeImmutable();
        $eom = \DateTimeImmutable::createFromFormat('d/m/Y', '01/'.Kernel::END_OF_MAINTENANCE)->modify('last day of this month');
        $eol = \DateTimeImmutable::createFromFormat('d/m/Y', '01/'.Kernel::END_OF_LIFE)->modify('last day of this month');

        if ($now > $eol) {
            $versionState = 'eol';
        } elseif ($now > $eom) {
            $versionState = 'eom';
        } elseif ('' !== Kernel::EXTRA_VERSION) {
            $versionState = 'dev';
        } else {
            $versionState = 'stable';
        }

        return $versionState;
    }

    private function determineRunnerClass(): ?string
    {
        $stack = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        for ($frame = end($stack); $frame; $frame = prev($stack)) {
            if (!$class = $frame['class'] ?? null) {
                continue;
            }
            if (is_a($class, RunnerInterface::class, true)) {
                return $class;
            }
        }

        return null;
    }
}
