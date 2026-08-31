<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Handles project trust decisions, persistence, and interactive prompting.
 */
class ProjectTrust
{
    private string $mode = Configuration::PROJECT_TRUST_PROMPT;
    private bool $forceTrust = false;
    private bool $forceUntrust = false;
    private bool $warnedUntrustedAutoload = false;

    /** @var string[] Project roots trusted for this session only (when persistence fails) */
    private array $sessionTrustedRoots = [];

    private ConfigPaths $configPaths;

    public function __construct(ConfigPaths $configPaths)
    {
        $this->configPaths = $configPaths;
    }

    /**
     * Get the current trust mode.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Set the trust mode.
     *
     * Accepts one of: 'prompt', 'always', 'never'.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Set trust mode from environment variable.
     *
     * Accepts: true, 1, false, 0
     *
     * @throws \InvalidArgumentException for invalid values
     */
    public function setModeFromEnv(string $value): void
    {
        switch (\strtolower(\trim($value))) {
            case 'true':
            case '1':
                $this->mode = Configuration::PROJECT_TRUST_ALWAYS;
                break;
            case 'false':
            case '0':
                $this->mode = Configuration::PROJECT_TRUST_NEVER;
                break;
            default:
                throw new \InvalidArgumentException('Invalid PSYSH_TRUST_PROJECT value. Expected: true, 1, false, or 0');
        }
    }

    /**
     * Set force trust for this run.
     */
    public function setForceTrust(bool $force = true): void
    {
        $this->forceTrust = $force;
        if ($force) {
            $this->forceUntrust = false;
        }
    }

    /**
     * Get force trust status.
     */
    public function getForceTrust(): bool
    {
        return $this->forceTrust;
    }

    /**
     * Set force untrust for this run.
     */
    public function setForceUntrust(bool $force = true): void
    {
        $this->forceUntrust = $force;
        if ($force) {
            $this->forceTrust = false;
        }
    }

    /**
     * Get force untrust status.
     */
    public function getForceUntrust(): bool
    {
        return $this->forceUntrust;
    }

    /**
     * Check project trust status.
     *
     * @return bool|null true if trusted, false if untrusted, null if should prompt
     */
    public function getProjectTrustStatus(string $projectRoot): ?bool
    {
        if ($this->forceUntrust || $this->mode === Configuration::PROJECT_TRUST_NEVER) {
            return false;
        }

        if ($this->forceTrust || $this->mode === Configuration::PROJECT_TRUST_ALWAYS || $this->isProjectTrusted($projectRoot)) {
            return true;
        }

        return null;
    }

    /**
     * Check if a project root is trusted (via stored trust or session trust).
     */
    public function isProjectTrusted(string $root): bool
    {
        if ($this->forceUntrust) {
            return false;
        }

        if ($this->forceTrust) {
            return true;
        }

        if ($this->mode === Configuration::PROJECT_TRUST_ALWAYS) {
            return true;
        }

        if ($this->mode === Configuration::PROJECT_TRUST_NEVER) {
            return false;
        }

        $root = $this->normalizeProjectRoot($root);
        $trustedRoots = $this->getTrustedProjectRoots();

        if (\in_array($root, $trustedRoots, true)) {
            return true;
        }

        return \in_array($root, $this->sessionTrustedRoots, true);
    }

    /**
     * Trust a project root, persisting if possible.
     *
     * Falls back to session-only trust with a warning if persistence fails.
     */
    public function trustProjectRoot(string $root, ?OutputInterface $output = null): bool
    {
        $root = $this->normalizeProjectRoot($root);
        $trustedRoots = $this->getTrustedProjectRoots();
        if (!\in_array($root, $trustedRoots, true)) {
            $trustedRoots[] = $root;
        }

        if (!$this->saveTrustedProjectRoots($trustedRoots)) {
            if ($output !== null) {
                $this->warnTrustPersistenceFailed($root, $output);
            }
            $this->sessionTrustedRoots[] = $root;

            return true;
        }

        return true;
    }

    /**
     * Display a trust persistence failure warning.
     */
    public function warnTrustPersistenceFailed(string $root, OutputInterface $output): void
    {
        if ($output instanceof ConsoleOutput) {
            $output = $output->getErrorOutput();
        }

        $prettyDir = ConfigPaths::prettyPath($root);
        $output->writeln(
            "<comment>Warning: Unable to save trust settings. Trusting {$prettyDir} for this session only.</comment>"
        );
    }

    /**
     * Display a warning about untrusted autoload warming.
     */
    public function warnUntrustedAutoloadWarming(string $projectRoot, OutputInterface $output): void
    {
        if ($this->warnedUntrustedAutoload) {
            return;
        }

        $this->warnedUntrustedAutoload = true;
        if ($output instanceof ConsoleOutput) {
            $output = $output->getErrorOutput();
        }

        $prettyDir = ConfigPaths::prettyPath($projectRoot);
        $output->writeln(
            "<comment>Skipping project autoload (vendor/autoload.php) in untrusted project {$prettyDir}. Use --trust-project to allow.</comment>"
        );
    }

    /**
     * Display the interactive trust prompt and trust the project on approval.
     *
     * @param string[] $features Features that need trust
     *
     * @return bool true if user approved
     */
    public function promptForTrust(InputInterface $input, OutputInterface $output, string $root, array $features): bool
    {
        $helper = new QuestionHelper();
        $prettyDir = ConfigPaths::prettyPath($root);

        $output->writeln('');
        $output->writeln("<comment>Unrecognized project {$prettyDir}</comment>");
        $output->writeln('');
        $output->writeln('Untrusted projects run in Restricted Mode to protect your system.');
        $output->writeln('');
        $output->writeln('Trusting this project would enable:');
        foreach ($features as $feature) {
            $output->writeln("  - {$feature}");
        }
        $output->writeln('');

        $question = new ConfirmationQuestion('Trust and continue? (y/N) ', false);
        if ($helper->ask($input, $output, $question)) {
            $this->trustProjectRoot($root, $output);

            return true;
        }

        return false;
    }

    /**
     * Normalize a project root path.
     */
    public function normalizeProjectRoot(string $root): string
    {
        $realRoot = \realpath($root);
        if ($realRoot !== false) {
            $root = $realRoot;
        }

        return \str_replace('\\', '/', $root);
    }

    /**
     * Get the project root for trust decisions (walks up to find composer.json).
     */
    public function getProjectRoot(): ?string
    {
        $root = $this->configPaths->projectRoot();
        if ($root === null) {
            return null;
        }

        return $this->normalizeProjectRoot($root);
    }

    /**
     * Get the local config root (cwd only, no ancestor walking).
     */
    public function getLocalConfigRoot(): ?string
    {
        $root = $this->configPaths->localConfigRoot();
        if ($root === null) {
            return null;
        }

        return $this->normalizeProjectRoot($root);
    }

    /**
     * Check if a project has Composer autoload files.
     *
     * Walks up the directory tree from the project root looking for
     * vendor/autoload.php or vendor/composer/autoload_psr4.php.
     */
    public function hasComposerAutoloadFiles(string $projectRoot): bool
    {
        $dir = $projectRoot;
        $parent = \dirname($dir);

        while ($dir !== $parent) {
            if (@\is_file($dir.'/vendor/autoload.php') || @\is_file($dir.'/vendor/composer/autoload_psr4.php')) {
                return true;
            }

            $dir = $parent;
            $parent = \dirname($dir);
        }

        return false;
    }

    /**
     * Check if the project has a local PsySH installation.
     *
     * Looks for psy/psysh in composer.json name or composer.lock packages,
     * and falls back to the PSYSH_UNTRUSTED_PROJECT env var.
     */
    public function getLocalPsyshProjectRoot(string $projectRoot): ?string
    {
        $composerJson = $projectRoot.'/composer.json';
        if (@\is_file($composerJson)) {
            $cfg = \json_decode(@\file_get_contents($composerJson), true);
            if (\is_array($cfg) && isset($cfg['name']) && $cfg['name'] === 'psy/psysh') {
                if (@\is_file($projectRoot.'/vendor/autoload.php')) {
                    return $this->normalizeProjectRoot($projectRoot);
                }
            }
        }

        $composerLock = $projectRoot.'/composer.lock';
        if (@\is_file($composerLock)) {
            $cfg = \json_decode(@\file_get_contents($composerLock), true);
            if (\is_array($cfg)) {
                $packages = \array_merge($cfg['packages'] ?? [], $cfg['packages-dev'] ?? []);
                foreach ($packages as $pkg) {
                    if (isset($pkg['name']) && $pkg['name'] === 'psy/psysh') {
                        if (@\is_file($projectRoot.'/vendor/autoload.php')) {
                            return $this->normalizeProjectRoot($projectRoot);
                        }

                        return null;
                    }
                }
            }
        }

        if (($untrustedProjectRoot = $this->getUntrustedProjectRootHint()) !== null) {
            return $this->normalizeProjectRoot($untrustedProjectRoot);
        }

        return null;
    }

    /**
     * Only prompt about local PsySH binaries when a launcher detected one.
     */
    public function shouldPromptForLocalPsyshBinary(): bool
    {
        return $this->getUntrustedProjectRootHint() !== null;
    }

    /**
     * Get untrusted project root hint from environment, if present.
     */
    private function getUntrustedProjectRootHint(): ?string
    {
        if (isset($_SERVER['PSYSH_UNTRUSTED_PROJECT'])
            && \is_string($_SERVER['PSYSH_UNTRUSTED_PROJECT'])
            && $_SERVER['PSYSH_UNTRUSTED_PROJECT'] !== ''
        ) {
            return $_SERVER['PSYSH_UNTRUSTED_PROJECT'];
        }

        $env = \getenv('PSYSH_UNTRUSTED_PROJECT');

        if (\is_string($env) && $env !== '') {
            return $env;
        }

        return null;
    }

    /**
     * Get trusted project roots from the trust file.
     *
     * @return string[]
     */
    public function getTrustedProjectRoots(): array
    {
        $trustFile = $this->getProjectTrustFilePath();
        if ($trustFile === null || !@\is_file($trustFile)) {
            return [];
        }

        $contents = @\file_get_contents($trustFile);
        if ($contents === false || $contents === '') {
            return [];
        }

        $data = \json_decode($contents, true);
        if (!\is_array($data)) {
            return [];
        }

        $roots = [];
        foreach ($data as $dir) {
            if (\is_string($dir) && $dir !== '') {
                $roots[] = $dir;
            }
        }

        return \array_values(\array_unique($roots));
    }

    /**
     * Save trusted project roots to the trust file.
     *
     * @param string[] $roots
     */
    public function saveTrustedProjectRoots(array $roots): bool
    {
        $trustFile = $this->getProjectTrustFileForWrite();
        if ($trustFile === false) {
            return false;
        }

        $roots = \array_values(\array_unique(\array_filter($roots, 'is_string')));
        $json = \json_encode($roots, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return false;
        }

        return \file_put_contents($trustFile, $json.\PHP_EOL) !== false;
    }

    /**
     * Get the path to the trusted projects file.
     */
    public function getProjectTrustFilePath(): ?string
    {
        $configDir = $this->configPaths->currentConfigDir();
        if ($configDir === null) {
            return null;
        }

        return $configDir.'/trusted_projects.json';
    }

    /**
     * Get a writable path to the trusted projects file, creating dirs if needed.
     *
     * @return string|false
     */
    private function getProjectTrustFileForWrite()
    {
        $trustFile = $this->getProjectTrustFilePath();
        if ($trustFile === null) {
            return false;
        }

        return ConfigPaths::touchFileWithMkdir($trustFile);
    }
}
