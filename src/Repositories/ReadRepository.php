<?php

namespace ProjectName\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityNotFoundException;

interface ReadRepository extends ObjectRepository
{
    /**
     * @throws EntityNotFoundException
     */
    public function get(int $id): object;

    public function findOne(int $id): ?object;

    public function all(): array;

    /**
     * @param null $limit
     * @param null $offset
     *
     * @return object[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array;

    public function findOneBy(array $criteria): ?object;

    public function refresh(object $entity): void;

    public function findByIds(array $id): array;

    /**
     * @return void
     */
    public function clear();
}
