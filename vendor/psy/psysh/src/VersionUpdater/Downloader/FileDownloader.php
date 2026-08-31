<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\VersionUpdater\Downloader;

use Psy\Exception\RuntimeException;
use Psy\VersionUpdater\Downloader;

class FileDownloader implements Downloader
{
    private ?string $tempDir = null;
    private ?string $outputFile = null;

    /** {@inheritDoc} */
    public function setTempDir(string $tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /** {@inheritDoc} */
    public function download(string $url): bool
    {
        $tempDir = $this->tempDir ?: \sys_get_temp_dir();
        $this->outputFile = \tempnam($tempDir, 'psysh-archive-');
        $targetName = $this->outputFile.'.tar.gz';

        if (!\rename($this->outputFile, $targetName)) {
            return false;
        }

        $this->outputFile = $targetName;

        return (bool) \file_put_contents($this->outputFile, \file_get_contents($url));
    }

    /** {@inheritDoc} */
    public function getFilename(): string
    {
        if ($this->outputFile === null) {
            throw new RuntimeException('Call download() first');
        }

        return $this->outputFile;
    }

    /** {@inheritDoc} */
    public function cleanup()
    {
        if ($this->outputFile !== null && \file_exists($this->outputFile)) {
            \unlink($this->outputFile);
        }
    }
}
