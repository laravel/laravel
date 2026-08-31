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

/**
 * An interval-based manual update checker.
 *
 * Caches update checks and only checks for updates at the configured interval.
 */
class IntervalChecker implements Checker
{
    private Checker $checker;
    private string $cacheFile;
    private string $interval;
    private ?array $cached = null;

    public function __construct(Checker $checker, string $cacheFile, string $interval)
    {
        $this->checker = $checker;
        $this->cacheFile = $cacheFile;
        $this->interval = $interval;
    }

    public function isLatest(): bool
    {
        $this->loadCache();

        // If we have a recent check, use the cached result
        if ($this->isCacheValid()) {
            return $this->cached['is_latest'] ?? true;
        }

        // Otherwise check now and cache the result
        $isLatest = $this->checker->isLatest();
        $this->updateCache($isLatest);

        return $isLatest;
    }

    public function getLatest(): string
    {
        $this->loadCache();

        // If we have a recent check, use the cached version
        if ($this->isCacheValid() && isset($this->cached['latest_version'])) {
            return $this->cached['latest_version'];
        }

        // Otherwise fetch now and cache the result
        $latest = $this->checker->getLatest();
        $this->updateCache(null, $latest);

        return $latest;
    }

    public function getDownloadUrl(): string
    {
        // Always delegate to the underlying checker
        // (URL might change between checks)
        return $this->checker->getDownloadUrl();
    }

    private function loadCache()
    {
        if ($this->cached !== null) {
            return;
        }

        $content = @\file_get_contents($this->cacheFile);
        if ($content) {
            $this->cached = \json_decode($content, true) ?: [];
        } else {
            $this->cached = [];
        }
    }

    private function isCacheValid(): bool
    {
        if (!isset($this->cached['last_check'])) {
            return false;
        }

        try {
            $now = new \DateTime();
            $lastCheck = new \DateTime($this->cached['last_check']);

            return $lastCheck >= $now->sub($this->getDateInterval());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @throws \RuntimeException if interval is not supported
     */
    private function getDateInterval(): \DateInterval
    {
        switch ($this->interval) {
            case Checker::DAILY:
                return new \DateInterval('P1D');
            case Checker::WEEKLY:
                return new \DateInterval('P1W');
            case Checker::MONTHLY:
                return new \DateInterval('P1M');
        }

        throw new \RuntimeException('Invalid interval configured');
    }

    private function updateCache(?bool $isLatest = null, ?string $latestVersion = null)
    {
        $this->loadCache();

        // Update cache data
        $this->cached['last_check'] = \date(\DATE_ATOM);

        if ($isLatest !== null) {
            $this->cached['is_latest'] = $isLatest;
        }

        if ($latestVersion !== null) {
            $this->cached['latest_version'] = $latestVersion;
        }

        // Write to file
        @\file_put_contents($this->cacheFile, \json_encode($this->cached));
    }
}
