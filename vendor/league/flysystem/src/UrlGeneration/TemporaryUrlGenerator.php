<?php
declare(strict_types=1);

namespace League\Flysystem\UrlGeneration;

use DateTimeInterface;
use League\Flysystem\Config;
use League\Flysystem\UnableToGenerateTemporaryUrl;

interface TemporaryUrlGenerator
{
    /**
     * @throws UnableToGenerateTemporaryUrl
     */
    public function temporaryUrl(string $path, DateTimeInterface $expiresAt, Config $config): string;
}
