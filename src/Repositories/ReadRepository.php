<?php

namespace ProjectName\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityNotFoundException;

interface ReadRepository extends ObjectRepository
{
    /**
     * @throws EntityNotFoundException
     *
     * @return object
     */
    public function get(int $id);

    /**
     * @throws EntityNotFoundException
     *
     * @return object|null
     */
    public function findOne(int $id);

    /**
     * @return array
     */
    public function all();

    /**
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    public function findOneBy(array $criteria);

    /**
     * @param mixed $entity
     */
    public function refresh($entity);
}
