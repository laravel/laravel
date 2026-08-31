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

class Installer
{
    private string $dataDir;
    private string $format;

    /**
     * @param string $dataDir Data directory where manual will be installed
     * @param string $format  Format type ('php' or 'sqlite')
     */
    public function __construct(string $dataDir, string $format)
    {
        $this->dataDir = $dataDir;
        $this->format = $format;
    }

    /**
     * Check if the data directory is writable.
     */
    public function isDataDirWritable(): bool
    {
        return \is_dir($this->dataDir) && \is_writable($this->dataDir);
    }

    /**
     * Extract and install the manual from a downloaded tarball.
     *
     * @param string $tarballPath Path to the downloaded .tar.gz file
     *
     * @return bool True on success
     */
    public function install(string $tarballPath): bool
    {
        if (!\file_exists($tarballPath)) {
            return false;
        }

        // Create temp directory for extraction
        $tempDir = \sys_get_temp_dir().'/psysh-manual-'.\uniqid();
        if (!\mkdir($tempDir)) {
            return false;
        }

        try {
            // Extract tarball
            $phar = new \PharData($tarballPath);
            $phar->extractTo($tempDir);

            // Determine the manual filename
            $manualFilename = $this->format === 'php' ? 'php_manual.php' : 'php_manual.sqlite';
            $extractedFile = $tempDir.'/'.$manualFilename;

            if (!\file_exists($extractedFile)) {
                return false;
            }

            // Move to data directory (overwrites existing)
            $success = \rename($extractedFile, $this->getInstallPath());

            return $success;
        } finally {
            // Clean up temp directory
            $this->removeDirectory($tempDir);
        }
    }

    /**
     * Get the path where the manual will be installed.
     */
    public function getInstallPath(): string
    {
        $manualFilename = $this->format === 'php' ? 'php_manual.php' : 'php_manual.sqlite';

        return $this->dataDir.'/'.$manualFilename;
    }

    /**
     * Recursively remove a directory.
     */
    private function removeDirectory(string $dir)
    {
        if (!\is_dir($dir)) {
            return;
        }

        $files = \array_diff(\scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            \is_dir($path) ? $this->removeDirectory($path) : \unlink($path);
        }

        \rmdir($dir);
    }
}
