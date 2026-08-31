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

interface Checker
{
    const ALWAYS = 'always';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const NEVER = 'never';

    /**
     * Check if the local manual is the latest version.
     */
    public function isLatest(): bool;

    /**
     * Get the latest available version for the configured language/format.
     */
    public function getLatest(): string;

    /**
     * Get the download URL for the latest manual.
     */
    public function getDownloadUrl(): string;
}
