<?php

namespace App\Infrastructure\Doctrine\Validation;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Validation\PresenceVerifierInterface;

class DoctrineInsensitivePresenceVerifier implements PresenceVerifierInterface
{
    protected ManagerRegistry $registry;

    /**
     * The database connection to use.
     */
    protected ?string $connection = null;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param string $collection
     * @param string $column
     * @param string $value
     * @param int    $excludeId
     * @param string $idColumn
     *
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $builder = $this->select($collection);
        $builder->where($this->getWhereClause($collection, $column));

        if (! is_null($excludeId) && $excludeId !== 'NULL') {
            $idColumn = $idColumn ?: 'id';
            $builder->andWhere("e.{$idColumn} <> :" . $this->prepareParam($idColumn));
        }

        $this->queryExtraConditions($extra, $builder);

        $query = $builder->getQuery();
        $query->setParameter($this->prepareParam($column), $value);

        if (! is_null($excludeId) && $excludeId !== 'NULL') {
            $query->setParameter($this->prepareParam($idColumn), $excludeId);
        }

        return $query->getSingleScalarResult();
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param string $collection
     * @param string $column
     *
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $builder = $this->select($collection);
        $builder->where($builder->expr()->in("e.{$column}", $values));

        $this->queryExtraConditions($extra, $builder);

        return $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * Set the connection to be used.
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    protected function select(string $collection): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager($collection);
        $builder = $em->createQueryBuilder();

        $builder->select('count(e)')->from($collection, 'e');

        return $builder;
    }

    protected function queryExtraConditions(array $extra, QueryBuilder $builder): void
    {
        foreach ($extra as $key => $extraValue) {
            $builder->andWhere("e.{$key} = :" . $this->prepareParam($key));
            $builder->setParameter($this->prepareParam($key), $extraValue);
        }
    }

    protected function getEntityManager(string $entity): ?ObjectManager
    {
        if (! is_null($this->connection)) {
            /** @var ObjectManager|null $manager */
            $manager = $this->registry->getManager($this->connection);

            return $manager;
        }

        /** @var ObjectManager|null $manager */
        $manager = $this->registry->getManagerForClass($entity);

        return $manager;
    }

    protected function prepareParam(string $column): string
    {
        return str_replace('.', '', $column);
    }

    private function getWhereClause(string $collection, string $column): string
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->getEntityManager($collection)->getClassMetadata($collection);
        $field = $metadata->fieldMappings[$column];

        switch ($field['type']) {
            case Types::STRING:
            case Types::TEXT:
                return "LOWER(e.{$column}) = LOWER(:{$this->prepareParam($column)})";
            default:
                return "e.{$column} = :{$this->prepareParam($column)}";
        }
    }
}
