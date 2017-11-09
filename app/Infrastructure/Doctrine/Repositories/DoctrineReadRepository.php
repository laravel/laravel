<?php

namespace App\Infrastructure\Doctrine\Repositories;

use ProjectName\Repositories\ReadRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

abstract class DoctrineReadRepository extends EntityRepository implements ReadRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata($this->getEntity()));
    }

    abstract public function getEntity();

    /**
     * {@inheritdoc}
     */
    public function get(int $id)
    {
        $entity = $this->findOne($id);
        if ($entity) {
            return $entity;
        }

        throw new EntityNotFoundException($this->getEntity());
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(int $id)
    {
        return $this->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->findAll();
    }

    public function refresh($entity)
    {
        $this->_em->refresh($entity);
    }
}
