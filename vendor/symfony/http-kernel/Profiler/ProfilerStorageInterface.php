<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

/**
 * ProfilerStorageInterface.
 *
 * This interface exists for historical reasons. The only supported
 * implementation is FileProfilerStorage.
 *
 * As the profiler must only be used on non-production servers, the file storage
 * is more than enough and no other implementations will ever be supported.
 *
 * @internal
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ProfilerStorageInterface
{
    /**
     * Finds profiler tokens for the given criteria.
     *
     * @param int|null      $limit      The maximum number of tokens to return
     * @param int|null      $start      The start date to search from
     * @param int|null      $end        The end date to search to
     * @param string|null   $statusCode The response status code
     * @param \Closure|null $filter     A filter to apply on the list of tokens
     */
    public function find(?string $ip, ?string $url, ?int $limit, ?string $method, ?int $start = null, ?int $end = null, ?string $statusCode = null, ?\Closure $filter = null): array;

    /**
     * Reads data associated with the given token.
     *
     * The method returns false if the token does not exist in the storage.
     */
    public function read(string $token): ?Profile;

    /**
     * Saves a Profile.
     */
    public function write(Profile $profile): bool;

    /**
     * Purges all data from the database.
     */
    public function purge(): void;
}
