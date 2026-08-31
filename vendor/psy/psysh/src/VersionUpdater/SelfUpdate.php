<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\VersionUpdater;

use Psy\Exception\ErrorException;
use Psy\Shell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Self update command.
 *
 * If a new version is available, this command will download it and replace the currently installed version
 */
class SelfUpdate
{
    const URL_PREFIX = 'https://github.com/bobthecow/psysh/releases/download';
    const SUCCESS = 0;
    const FAILURE = 1;

    private Checker $checker;
    private Installer $installer;
    private ?Downloader $downloader = null;

    public function __construct(Checker $checker, Installer $installer)
    {
        $this->checker = $checker;
        $this->installer = $installer;
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
     * Build the download URL for the latest release.
     *
     * The file name used in the URL will include the flavour postfix extracted from the current version
     * if it's present
     */
    private function getAssetUrl(string $latestVersion): string
    {
        $versionPostfix = '';
        if (\strpos(Shell::VERSION, '+')) {
            $versionPostfix = '-'.\substr(Shell::VERSION, \strpos(Shell::VERSION, '+') + 1);
        }
        $downloadFilename = \sprintf('psysh-%s%s.tar.gz', $latestVersion, $versionPostfix);

        // check if latest release data contains an asset matching the filename?

        return \sprintf('%s/%s/%s', self::URL_PREFIX, $latestVersion, $downloadFilename);
    }

    /**
     * Execute the self-update process.
     *
     * @throws ErrorException if the current version is not restored when installation fails
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $currentVersion = Shell::VERSION;

        // already have the latest version?
        if ($this->checker->isLatest()) {
            // current version is latest version...
            $output->writeln('<info>Current version is up-to-date.</info>');

            return self::SUCCESS;
        }

        // can overwrite current version?
        if (!$this->installer->isInstallLocationWritable()) {
            $output->writeln('<error>Installed version is not writable.</error>');

            return self::FAILURE;
        }
        // can download to, and create a backup in the temp directory?
        if (!$this->installer->isTempDirectoryWritable()) {
            $output->writeln('<error>Temporary directory is not writable.</error>');

            return self::FAILURE;
        }

        $latestVersion = $this->checker->getLatest();
        $downloadUrl = $this->getAssetUrl($latestVersion);

        $output->write("Downloading PsySH $latestVersion ...");

        try {
            $downloader = $this->getDownloader();
            $downloader->setTempDir($this->installer->getTempDirectory());
            $downloaded = $downloader->download($downloadUrl);
        } catch (ErrorException $e) {
            $output->write(' <error>Failed.</error>');
            $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));

            return self::FAILURE;
        }

        if (!$downloaded) {
            $output->writeln('<error>Download failed.</error>');
            $downloader->cleanup();

            return self::FAILURE;
        } else {
            $output->write(' <info>OK</info>'.\PHP_EOL);
        }

        $downloadedFile = $downloader->getFilename();

        if (!$this->installer->isValidSource($downloadedFile)) {
            $downloader->cleanup();
            $output->writeln('<error>Downloaded file is not a valid archive.</error>');

            return self::FAILURE;
        }

        // create backup as bin.old-version in the temporary directory
        $backupCreated = $this->installer->createBackup($currentVersion);
        if (!$backupCreated) {
            $downloader->cleanup();
            $output->writeln('<error>Failed to create a backup of the current version.</error>');

            return self::FAILURE;
        } elseif ($input->getOption('verbose')) {
            $backupFilename = $this->installer->getBackupFilename($currentVersion);
            $output->writeln('Created backup of current version: '.$backupFilename);
        }

        if (!$this->installer->install($downloadedFile)) {
            $this->installer->restoreFromBackup($currentVersion);
            $downloader->cleanup();
            $output->writeln("<error>Failed to install new PsySH version $latestVersion.</error>");

            return self::FAILURE;
        }

        // Remove the downloaded archive file from the temporary directory
        $downloader->cleanup();

        $output->writeln("Updated PsySH from $currentVersion to <info>$latestVersion</info>");

        return self::SUCCESS;
    }
}
