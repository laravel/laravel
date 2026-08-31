<?php

namespace League\Flysystem\UrlGeneration;

use InvalidArgumentException;
use League\Flysystem\Config;
use League\Flysystem\PathPrefixer;

use function array_map;
use function count;
use function crc32;

final class ShardedPrefixPublicUrlGenerator implements PublicUrlGenerator
{
    /** @var PathPrefixer[] */
    private array $prefixes;
    private int $count;

    /**
     * @param string[] $prefixes
     */
    public function __construct(array $prefixes)
    {
        $this->count = count($prefixes);

        if ($this->count === 0) {
            throw new InvalidArgumentException('At least one prefix is required.');
        }

        $this->prefixes = array_map(static fn (string $prefix) => new PathPrefixer($prefix, '/'), $prefixes);
    }

    public function publicUrl(string $path, Config $config): string
    {
        $index = abs(crc32($path)) % $this->count;

        return $this->prefixes[$index]->prefixPath($path);
    }
}
