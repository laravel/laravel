<?php

namespace App\Infrastructure\Doctrine\Repositories;

use Doctrine\ORM\EntityManager;
use ProjectName\Repositories\PersistRepository;

class DoctrinePersistRepository implements PersistRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save($entity)
    {
        $this->persist($entity);
        $this->flush($entity);
    }

    public function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->flush($entity);
    }

    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }

    public function flush($entity = null)
    {
        $this->entityManager->flush($entity);
    }

    public function clear($entity = null)
    {
        $this->entityManager->clear($entity);
    }

    public function transactional(callable $function)
    {
        return $this->entityManager->transactional($function);
    }
}
