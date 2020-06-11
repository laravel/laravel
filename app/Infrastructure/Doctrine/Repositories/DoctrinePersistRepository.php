<?php

namespace App\Infrastructure\Doctrine\Repositories;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use ProjectName\Repositories\PersistRepository;

class DoctrinePersistRepository implements PersistRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(object $entity): void
    {
        $this->persist($entity);
        $this->flush($entity, true);
    }

    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->flush($entity, true);
    }

    public function persist(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function flush(?object $entity = null, bool $evictCache = false): void
    {
        $this->entityManager->flush($entity);

        if ($entity && $evictCache) {
            $this->evictCache($entity);
        }
    }

    public function clear(?object $entity = null): void
    {
        $this->entityManager->clear($entity);
    }

    /**
     * @throws \Throwable
     *
     * @return bool|mixed
     */
    public function transactional(callable $function)
    {
        return $this->entityManager->transactional($function);
    }

    public function lockPesimistic(object $entity): void
    {
        $this->entityManager->lock($entity, LockMode::PESSIMISTIC_WRITE);
    }

    private function evictCache(object $entity): void
    {
        $cache = $this->entityManager->getCache();

        if ($cache) {
            $cache->evictEntityRegion(get_class($entity));
        }
    }
}
