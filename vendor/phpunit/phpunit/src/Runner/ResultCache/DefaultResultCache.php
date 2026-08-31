<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\ResultCache;

use const DIRECTORY_SEPARATOR;
use const LOCK_EX;
use function array_keys;
use function assert;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function is_dir;
use function is_file;
use function json_decode;
use function json_encode;
use PHPUnit\Framework\TestStatus\TestStatus;
use PHPUnit\Runner\DirectoryDoesNotExistException;
use PHPUnit\Runner\Exception;
use PHPUnit\Util\Filesystem;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DefaultResultCache implements ResultCache
{
    /**
     * @var int
     */
    private const VERSION = 2;

    /**
     * @var string
     */
    private const DEFAULT_RESULT_CACHE_FILENAME = '.phpunit.result.cache';
    private readonly string $cacheFilename;

    /**
     * @var array<string, TestStatus>
     */
    private array $defects = [];

    /**
     * @var array<string, float>
     */
    private array $times = [];

    public function __construct(?string $filepath = null)
    {
        if ($filepath !== null && is_dir($filepath)) {
            $filepath .= DIRECTORY_SEPARATOR . self::DEFAULT_RESULT_CACHE_FILENAME;
        }

        $this->cacheFilename = $filepath ?? $_ENV['PHPUNIT_RESULT_CACHE'] ?? self::DEFAULT_RESULT_CACHE_FILENAME;
    }

    public function setStatus(ResultCacheId $id, TestStatus $status): void
    {
        if ($status->isSuccess()) {
            return;
        }

        $this->defects[$id->asString()] = $status;
    }

    public function status(ResultCacheId $id): TestStatus
    {
        return $this->defects[$id->asString()] ?? TestStatus::unknown();
    }

    public function setTime(ResultCacheId $id, float $time): void
    {
        $this->times[$id->asString()] = $time;
    }

    public function time(ResultCacheId $id): float
    {
        return $this->times[$id->asString()] ?? 0.0;
    }

    public function mergeWith(self $other): void
    {
        foreach ($other->defects as $id => $defect) {
            $this->defects[$id] = $defect;
        }

        foreach ($other->times as $id => $time) {
            $this->times[$id] = $time;
        }
    }

    public function load(): void
    {
        if (!is_file($this->cacheFilename)) {
            return;
        }

        $contents = file_get_contents($this->cacheFilename);

        if ($contents === false) {
            return;
        }

        $data = json_decode(
            $contents,
            true,
        );

        if ($data === null) {
            return;
        }

        if (!isset($data['version'])) {
            return;
        }

        if ($data['version'] !== self::VERSION) {
            return;
        }

        assert(isset($data['defects']) && is_array($data['defects']));
        assert(isset($data['times']) && is_array($data['times']));

        foreach (array_keys($data['defects']) as $test) {
            $data['defects'][$test] = TestStatus::from($data['defects'][$test]);
        }

        $this->defects = $data['defects'];
        $this->times   = $data['times'];
    }

    /**
     * @throws Exception
     */
    public function persist(): void
    {
        if (!Filesystem::createDirectory(dirname($this->cacheFilename))) {
            throw new DirectoryDoesNotExistException(dirname($this->cacheFilename));
        }

        $data = [
            'version' => self::VERSION,
            'defects' => [],
            'times'   => $this->times,
        ];

        foreach ($this->defects as $test => $status) {
            $data['defects'][$test] = $status->asInt();
        }

        file_put_contents(
            $this->cacheFilename,
            json_encode($data),
            LOCK_EX,
        );
    }
}
