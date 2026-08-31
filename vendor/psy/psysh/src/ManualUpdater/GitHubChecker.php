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

use Psy\Shell;

class GitHubChecker implements Checker
{
    const RELEASES_URL = 'https://api.github.com/repos/bobthecow/psysh-manual/releases';

    private string $lang;
    private string $format;
    private ?string $currentVersion;
    private ?string $currentLang;
    private ?string $latestVersion = null;
    private ?string $downloadUrl = null;

    /**
     * @param string      $lang           Language code (e.g., 'en')
     * @param string      $format         Format type ('php' or 'sqlite')
     * @param string|null $currentVersion Current manual version, or null if not installed
     * @param string|null $currentLang    Current manual language, or null if not installed
     */
    public function __construct(string $lang, string $format, ?string $currentVersion = null, ?string $currentLang = null)
    {
        $this->lang = $lang;
        $this->format = $format;
        $this->currentVersion = $currentVersion;
        $this->currentLang = $currentLang;
    }

    public function isLatest(): bool
    {
        if ($this->currentVersion === null) {
            return false;
        }

        // If language has changed, need to update regardless of version
        if ($this->currentLang !== null && $this->currentLang !== $this->lang) {
            return false;
        }

        return \version_compare($this->currentVersion, $this->getLatest(), '>=');
    }

    public function getLatest(): string
    {
        if (!isset($this->latestVersion)) {
            $this->fetchLatestRelease();
        }

        return $this->latestVersion;
    }

    public function getDownloadUrl(): string
    {
        if (!isset($this->downloadUrl)) {
            $this->fetchLatestRelease();
        }

        return $this->downloadUrl;
    }

    private function fetchLatestRelease()
    {
        $context = \stream_context_create([
            'http' => [
                'user_agent' => 'PsySH/'.Shell::VERSION,
                'timeout'    => 3.0,
            ],
        ]);

        \set_error_handler(function () {
            // Ignore errors - we'll handle failures below
        });

        $result = @\file_get_contents(self::RELEASES_URL, false, $context);

        \restore_error_handler();

        if (!$result) {
            throw new \RuntimeException('Unable to fetch manual releases from GitHub');
        }

        $releases = \json_decode($result, true);
        if (!$releases || !\is_array($releases)) {
            throw new \RuntimeException('Invalid response from GitHub releases API');
        }

        // Find the first release with a manifest
        foreach ($releases as $release) {
            $manifest = $this->fetchManifest($release);
            if ($manifest === null) {
                continue;
            }

            // Find our language/format in the manifest
            foreach ($manifest['manuals'] as $manual) {
                if ($manual['lang'] === $this->lang && $manual['format'] === $this->format) {
                    $this->latestVersion = $manual['version'];

                    // Build download URL
                    $filename = \sprintf('psysh-manual-v%s-%s.tar.gz', $manual['version'], $this->lang);
                    $this->downloadUrl = $release['assets_url'] ?? null;

                    // Find the actual asset URL
                    foreach ($release['assets'] as $asset) {
                        if ($asset['name'] === $filename) {
                            $this->downloadUrl = $asset['browser_download_url'];
                            break;
                        }
                    }

                    return;
                }
            }
        }

        throw new \RuntimeException(\sprintf('No manual found for language "%s" in format "%s"', $this->lang, $this->format));
    }

    /**
     * Fetch and parse manifest.json from a release.
     *
     * @return array|null
     */
    private function fetchManifest(array $release): ?array
    {
        // Find manifest.json in assets
        foreach ($release['assets'] as $asset) {
            if ($asset['name'] === 'manifest.json') {
                $context = \stream_context_create([
                    'http' => [
                        'user_agent' => 'PsySH/'.Shell::VERSION,
                        'timeout'    => 3.0,
                    ],
                ]);

                \set_error_handler(function () {
                    // Ignore errors
                });

                $manifestContent = @\file_get_contents($asset['browser_download_url'], false, $context);

                \restore_error_handler();

                if ($manifestContent) {
                    return \json_decode($manifestContent, true);
                }
            }
        }

        return null;
    }
}
