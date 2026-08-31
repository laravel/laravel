<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\ManualUpdater;

use Psy\ConfigPaths;
use Psy\Configuration;
use Psy\Exception\ErrorException;
use Psy\Exception\InvalidManualException;
use Psy\Manual\V2Manual;
use Psy\Manual\V3Manual;
use Psy\VersionUpdater\Downloader;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Manual update command.
 *
 * If a new manual version is available, this command will download and install it.
 */
class ManualUpdate
{
    const SUCCESS = 0;
    const FAILURE = 1;

    /** @var array{checker: Checker, installer: Installer}[] */
    private array $updates;
    private ?Downloader $downloader = null;

    /**
     * @param array{checker: Checker, installer: Installer} ...$updates Update configuration(s)
     */
    public function __construct(array ...$updates)
    {
        $this->updates = $updates;
    }

    /**
     * Create a ManualUpdate instance from Configuration and command-line input.
     *
     * @param Configuration   $config Configuration instance
     * @param InputInterface  $input  Input interface
     * @param OutputInterface $output Output interface
     *
     * @return self
     */
    public static function fromConfig(Configuration $config, InputInterface $input, OutputInterface $output): self
    {
        $lang = $input->getOption('update-manual') ?: null;

        // Clear the manual update cache when explicitly running --update-manual
        $cacheFile = $config->getManualUpdateCheckCacheFile();
        if ($cacheFile && \file_exists($cacheFile)) {
            @\unlink($cacheFile);
        }

        // Get current manual language before potentially deleting files
        $currentLang = null;
        $removedInvalidSqlite = false;
        $manualFile = $config->getManualDbFile();
        if ($manualFile && \file_exists($manualFile)) {
            try {
                $manual = $config->getManual();
                if ($manual) {
                    $currentMeta = $manual->getMeta();
                    $currentLang = $currentMeta['lang'] ?? null;
                }
            } catch (InvalidManualException $e) {
                $removedInvalidSqlite = \substr($e->getManualFile(), -7) === '.sqlite';
                self::handleInvalidManual($e, $input, $output);
            }
        }

        $dataDir = $config->getManualInstallDir();
        if ($dataDir === false) {
            throw new \RuntimeException('Unable to find a writable data directory for manual installation');
        }

        $phpManualPath = $dataDir.'/php_manual.php';
        $sqliteManualPath = $dataDir.'/php_manual.sqlite';

        $formats = self::getFormatsToUpdate(
            $input,
            $output,
            \file_exists($phpManualPath),
            \file_exists($sqliteManualPath),
            $removedInvalidSqlite,
            $sqliteManualPath
        );

        // Build update configurations for selected formats
        $checkerLang = $lang ?: $currentLang ?: 'en';
        $updates = [];

        foreach ($formats as $format) {
            $path = $format === 'php' ? $phpManualPath : $sqliteManualPath;
            $meta = self::getManualMeta($path);

            $updates[] = [
                'checker'   => new GitHubChecker($checkerLang, $format, $meta['version'] ?? null, $meta['lang'] ?? null),
                'installer' => new Installer($dataDir, $format),
            ];
        }

        return new self(...$updates);
    }

    /**
     * Allow the downloader to be injected for testing.
     *
     * @return void
     */
    public function setDownloader(Downloader $downloader)
    {
        $this->downloader = $downloader;
    }

    /**
     * Get the currently set Downloader or create one based on the capabilities of the php environment.
     *
     * @throws ErrorException if a downloader cannot be created for the php environment
     */
    private function getDownloader(): Downloader
    {
        if (!isset($this->downloader)) {
            return Downloader\Factory::getDownloader();
        }

        return $this->downloader;
    }

    /**
     * Update the manual installation.
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->updates as $update) {
            if (!$update['installer']->isDataDirWritable()) {
                $output->writeln('<error>Data directory is not writable.</error>');

                return self::FAILURE;
            }
        }

        $downloader = $this->getDownloader();
        $downloader->setTempDir(\sys_get_temp_dir());
        $installed = [];

        // Download and install each format
        foreach ($this->updates as $update) {
            $checker = $update['checker'];
            $installer = $update['installer'];

            if ($checker->isLatest()) {
                continue;
            }

            $latestVersion = $checker->getLatest();
            $downloadUrl = $checker->getDownloadUrl();

            $output->write("Downloading manual v{$latestVersion}...");

            try {
                $downloaded = $downloader->download($downloadUrl);
            } catch (ErrorException $e) {
                $output->write(' <error>Failed.</error>');
                $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));
                $downloader->cleanup();

                return self::FAILURE;
            }

            if (!$downloaded) {
                $output->writeln(' <error>Download failed.</error>');
                $downloader->cleanup();

                return self::FAILURE;
            }

            $output->write(' <info>OK</info>'.\PHP_EOL);

            $downloadedFile = $downloader->getFilename();

            if (!$installer->install($downloadedFile)) {
                $downloader->cleanup();
                $output->writeln('<error>Failed to install manual.</error>');

                return self::FAILURE;
            }

            $installed[] = [$installer->getInstallPath(), $latestVersion];

            $downloader->cleanup();
        }

        if (empty($installed)) {
            $output->writeln('<info>Manual is up-to-date.</info>');
        } else {
            foreach ($installed as [$installPath, $version]) {
                $prettyPath = ConfigPaths::prettyPath($installPath);
                $output->writeln("Installed manual v{$version} to <info>{$prettyPath}</info>");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Handle an invalid manual file by prompting the user to remove it.
     *
     * @param InvalidManualException $e      The exception containing invalid manual details
     * @param InputInterface         $input  Input interface
     * @param OutputInterface        $output Output interface
     *
     * @throws \RuntimeException if user declines to remove the file or removal fails
     */
    private static function handleInvalidManual(InvalidManualException $e, InputInterface $input, OutputInterface $output): void
    {
        $prettyPath = ConfigPaths::prettyPath($e->getManualFile());
        $output->writeln(\sprintf('<error>Invalid manual file detected:</error> <info>%s</info>', $prettyPath));
        $output->writeln('');

        $helper = new QuestionHelper();
        $question = new ConfirmationQuestion('Remove this file and continue? [Y/n] ', true);

        if (!$helper->ask($input, $output, $question)) {
            throw new \RuntimeException('Manual update cancelled.');
        }

        if (!\unlink($e->getManualFile())) {
            throw new \RuntimeException(\sprintf('Failed to remove file: %s', $prettyPath));
        }

        $output->writeln('<info>Invalid manual file removed.</info>');
        $output->writeln('');
    }

    /**
     * Prompt user to download PHP format manual when they have/had legacy SQLite.
     *
     * @param InputInterface  $input      Input interface
     * @param OutputInterface $output     Output interface
     * @param string          $manualFile Path to current/former SQLite manual file
     * @param bool            $wasRemoved Whether the file was already removed
     *
     * @return bool True if user wants to download PHP format
     */
    private static function promptMigrateToV3(InputInterface $input, OutputInterface $output, string $manualFile, bool $wasRemoved): bool
    {
        $prettyPath = ConfigPaths::prettyPath($manualFile);
        $verb = $wasRemoved ? 'had' : 'have';
        $output->writeln(\sprintf('You %s a legacy SQLite manual: <info>%s</info>', $verb, $prettyPath));
        $output->writeln('');

        $helper = new QuestionHelper();
        $question = new ConfirmationQuestion('Download the current manual format? [Y/n] ', true);

        return $helper->ask($input, $output, $question);
    }

    /**
     * Determine which manual formats should be updated.
     *
     * @param InputInterface  $input                Input interface
     * @param OutputInterface $output               Output interface
     * @param bool            $hasPhpManual         Whether PHP manual exists
     * @param bool            $hasSqliteManual      Whether SQLite manual exists
     * @param bool            $removedInvalidSqlite Whether we just removed an invalid SQLite manual
     * @param string          $sqliteManualPath     Path to SQLite manual file
     *
     * @return string[] Array of format names to update ('php', 'sqlite')
     */
    private static function getFormatsToUpdate(
        InputInterface $input,
        OutputInterface $output,
        bool $hasPhpManual,
        bool $hasSqliteManual,
        bool $removedInvalidSqlite,
        string $sqliteManualPath
    ): array {
        // Only SQLite exists (or just removed invalid SQLite): offer to add PHP format
        if (!$hasPhpManual && ($hasSqliteManual || $removedInvalidSqlite)) {
            if (self::promptMigrateToV3($input, $output, $sqliteManualPath, $removedInvalidSqlite)) {
                return ['php', 'sqlite'];
            }

            return ['sqlite'];
        }

        // PHP exists, or neither exist: default to PHP, and include SQLite if it exists
        $formats = ['php'];
        if ($hasSqliteManual) {
            $formats[] = 'sqlite';
        }

        return $formats;
    }

    /**
     * Get manual metadata from a file.
     *
     * @param string $path Path to manual file
     *
     * @return array|null Metadata array with 'version' and 'lang' keys, or null if unavailable
     */
    private static function getManualMeta(string $path): ?array
    {
        if (!\file_exists($path)) {
            return null;
        }

        try {
            if (\substr($path, -4) === '.php') {
                $manual = new V3Manual($path);

                return $manual->getMeta();
            }

            if (\substr($path, -7) === '.sqlite') {
                $pdo = new \PDO('sqlite:'.$path);
                $manual = new V2Manual($pdo);

                return $manual->getMeta();
            }
        } catch (\Exception $e) {
            // Ignore errors reading manual metadata
        }

        return null;
    }
}
