<?php
namespace ProjectName\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityNotFoundException;

interface ReadRepository extends ObjectRepository
{
    /**
     * @param int $id
     * @return object
     * @throws EntityNotFoundException
     */
    public function get(int $id);

    /**
     * @param int $id
     * @return object|null
     * @throws EntityNotFoundException
     */
    public function findOne(int $id);

    /**
     * @return array
     */
    public function all();

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    public function findOneBy(array $criteria);

    /**
     * @param mixed $entity
     */
    public function refresh($entity);
}
