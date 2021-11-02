<?php

namespace ProjectName\Repositories;

interface PersistRepository
{
    public function save(object $entity): void;

    public function remove(object $entity): void;

    public function persist(object $entity): void;

    /**
     * If $evictCache is true, will only evict the cache of $entity.
     * If $entity is null, then the cache will not be evicted.
     */
    public function flush(?object $entity = null, bool $evictCache = false): void;

    public function clear(): void;

    /**
     * @throws \Throwable
     *
     * @return bool|mixed
     */
    public function transactional(callable $function);

    public function lockPesimistic(object $entity): void;
}
