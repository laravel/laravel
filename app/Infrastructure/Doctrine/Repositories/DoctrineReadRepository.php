<?php

namespace App\Infrastructure\Doctrine\Repositories;

use Digbang\Utils\Doctrine\QueryBuilderDecorator;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use ProjectName\Repositories\ReadRepository;

abstract class DoctrineReadRepository extends EntityRepository implements ReadRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata($this->getEntity()));
    }

    /**
     * @param string $alias
     * @param null $indexBy
     *
     * @return QueryBuilderDecorator
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        return (new QueryBuilderDecorator(
                $this->_em->createQueryBuilder()
            ))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);
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

    public function findManyById(array $ids): array
    {
        /** @var ClassMetadata $meta */
        $meta = $this->_em->getClassMetadata(get_class($this->_entityName));
        $identifier = $meta->getSingleIdentifierFieldName();

        return $this->_em->getRepository($this->_entityName)->findBy([
            $identifier => $ids
        ]);
    }

    public function refresh($entity)
    {
        $this->_em->refresh($entity);
    }
}
