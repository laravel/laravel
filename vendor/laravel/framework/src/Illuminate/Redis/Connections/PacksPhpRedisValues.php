<?php

namespace Illuminate\Redis\Connections;

use Redis;
use RuntimeException;
use UnexpectedValueException;

trait PacksPhpRedisValues
{
    /**
     * Indicates if Redis supports packing.
     *
     * @var bool|null
     */
    protected $supportsPacking;

    /**
     * Indicates if Redis supports LZF compression.
     *
     * @var bool|null
     */
    protected $supportsLzf;

    /**
     * Indicates if Redis supports Zstd compression.
     *
     * @var bool|null
     */
    protected $supportsZstd;

    /**
     * Prepares the given values to be used with the `eval` command, including serialization and compression.
     *
     * @param  array<int|string,string>  $values
     * @return array<int|string,string>
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */
    public function pack(array $values): array
    {
        if (empty($values)) {
            return $values;
        }

        if ($this->supportsPacking()) {
            return array_map($this->client->_pack(...), $values);
        }

        if ($this->compressed()) {
            if ($this->supportsLzf() && $this->lzfCompressed()) {
                if (! function_exists('lzf_compress')) {
                    throw new RuntimeException("'lzf' extension required to call 'lzf_compress'.");
                }

                $processor = function ($value) {
                    return \lzf_compress($this->client->_serialize($value));
                };
            } elseif ($this->supportsZstd() && $this->zstdCompressed()) {
                if (! function_exists('zstd_compress')) {
                    throw new RuntimeException("'zstd' extension required to call 'zstd_compress'.");
                }

                $compressionLevel = $this->client->getOption(Redis::OPT_COMPRESSION_LEVEL);

                $processor = function ($value) use ($compressionLevel) {
                    return \zstd_compress(
                        $this->client->_serialize($value),
                        $compressionLevel === 0 ? Redis::COMPRESSION_ZSTD_DEFAULT : $compressionLevel
                    );
                };
            } else {
                throw new UnexpectedValueException(sprintf(
                    'Unsupported phpredis compression in use [%d].',
                    $this->client->getOption(Redis::OPT_COMPRESSION)
                ));
            }
        } else {
            $processor = function ($value) {
                return $this->client->_serialize($value);
            };
        }

        return array_map($processor, $values);
    }

    /**
     * Execute the given callback without serialization or compression when applicable.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public function withoutSerializationOrCompression(callable $callback)
    {
        $client = $this->client;

        $oldSerializer = null;

        if ($this->serialized()) {
            $oldSerializer = $client->getOption(Redis::OPT_SERIALIZER);
            $client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
        }

        $oldCompressor = null;

        if ($this->compressed()) {
            $oldCompressor = $client->getOption(Redis::OPT_COMPRESSION);
            $client->setOption(Redis::OPT_COMPRESSION, Redis::COMPRESSION_NONE);
        }

        try {
            return $callback();
        } finally {
            if ($oldSerializer !== null) {
                $client->setOption(Redis::OPT_SERIALIZER, $oldSerializer);
            }

            if ($oldCompressor !== null) {
                $client->setOption(Redis::OPT_COMPRESSION, $oldCompressor);
            }
        }
    }

    /**
     * Determine if serialization is enabled.
     *
     * @return bool
     */
    public function serialized(): bool
    {
        return defined('Redis::OPT_SERIALIZER') &&
               $this->client->getOption(Redis::OPT_SERIALIZER) !== Redis::SERIALIZER_NONE;
    }

    /**
     * Determine if compression is enabled.
     *
     * @return bool
     */
    public function compressed(): bool
    {
        return defined('Redis::OPT_COMPRESSION') &&
               $this->client->getOption(Redis::OPT_COMPRESSION) !== Redis::COMPRESSION_NONE;
    }

    /**
     * Determine if LZF compression is enabled.
     *
     * @return bool
     */
    public function lzfCompressed(): bool
    {
        return defined('Redis::COMPRESSION_LZF') &&
               $this->client->getOption(Redis::OPT_COMPRESSION) === Redis::COMPRESSION_LZF;
    }

    /**
     * Determine if ZSTD compression is enabled.
     *
     * @return bool
     */
    public function zstdCompressed(): bool
    {
        return defined('Redis::COMPRESSION_ZSTD') &&
               $this->client->getOption(Redis::OPT_COMPRESSION) === Redis::COMPRESSION_ZSTD;
    }

    /**
     * Determine if LZ4 compression is enabled.
     *
     * @return bool
     */
    public function lz4Compressed(): bool
    {
        return defined('Redis::COMPRESSION_LZ4') &&
               $this->client->getOption(Redis::OPT_COMPRESSION) === Redis::COMPRESSION_LZ4;
    }

    /**
     * Determine if the current PhpRedis extension version supports packing.
     *
     * @return bool
     */
    protected function supportsPacking(): bool
    {
        if ($this->supportsPacking === null) {
            $this->supportsPacking = $this->phpRedisVersionAtLeast('5.3.5');
        }

        return $this->supportsPacking;
    }

    /**
     * Determine if the current PhpRedis extension version supports LZF compression.
     *
     * @return bool
     */
    protected function supportsLzf(): bool
    {
        if ($this->supportsLzf === null) {
            $this->supportsLzf = $this->phpRedisVersionAtLeast('4.3.0');
        }

        return $this->supportsLzf;
    }

    /**
     * Determine if the current PhpRedis extension version supports Zstd compression.
     *
     * @return bool
     */
    protected function supportsZstd(): bool
    {
        if ($this->supportsZstd === null) {
            $this->supportsZstd = $this->phpRedisVersionAtLeast('5.1.0');
        }

        return $this->supportsZstd;
    }

    /**
     * Determine if the PhpRedis extension version is at least the given version.
     *
     * @param  string  $version
     * @return bool
     */
    protected function phpRedisVersionAtLeast(string $version): bool
    {
        $phpredisVersion = phpversion('redis');

        return $phpredisVersion !== false && version_compare($phpredisVersion, $version, '>=');
    }
}
