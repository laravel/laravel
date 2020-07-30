<?php

namespace App\Infrastructure\Doctrine\Repositories;

use Digbang\Utils\Doctrine\QueryBuilderDecorator;
use Digbang\Utils\Pagination\Paginator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use ProjectName\Repositories\ReadRepository;

abstract class DoctrineReadRepository extends EntityRepository implements ReadRepository
{
    use Paginator;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata($this->getEntity()));
    }

    /**
     * @param string $alias
     * @param null $indexBy
     */
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilderDecorator
    {
        return (new QueryBuilderDecorator(
                $this->_em->createQueryBuilder()
            ))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);
    }

    abstract public function getEntity(): string;

    public function get(int $id): object
    {
        $entity = $this->findOne($id);
        if ($entity) {
            return $entity;
        }

        throw new EntityNotFoundException($this->getEntity());
    }

    public function findOne(int $id): ?object
    {
        return $this->find($id);
    }

    public function all(): array
    {
        return $this->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?object
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    public function findByIds(array $ids): array
    {
        $meta = $this->_em->getClassMetadata($this->_entityName);
        $identifier = $meta->getSingleIdentifierFieldName();

        return $this->_em->getRepository($this->_entityName)->findBy([
            $identifier => $ids,
        ]);
    }

    public function refresh(object $entity): void
    {
        $this->_em->refresh($entity);
    }
}
