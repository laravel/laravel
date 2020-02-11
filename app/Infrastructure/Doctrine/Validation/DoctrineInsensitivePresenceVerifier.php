<?php

namespace App\Infrastructure\Doctrine\Validation;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Validation\PresenceVerifierInterface;

class DoctrineInsensitivePresenceVerifier implements PresenceVerifierInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * The database connection to use.
     *
     * @var string
     */
    protected $connection = null;

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

        if (! is_null($excludeId) && $excludeId != 'NULL') {
            $idColumn = $idColumn ?: 'id';
            $builder->andWhere("e.{$idColumn} <> :" . $this->prepareParam($idColumn));
        }

        $this->queryExtraConditions($extra, $builder);

        $query = $builder->getQuery();
        $query->setParameter($this->prepareParam($column), $value);

        if (! is_null($excludeId) && $excludeId != 'NULL') {
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
     *
     * @param string $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $collection
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function select($collection)
    {
        $em = $this->getEntityManager($collection);
        $builder = $em->createQueryBuilder();

        $builder->select('count(e)')->from($collection, 'e');

        return $builder;
    }

    protected function queryExtraConditions(array $extra, QueryBuilder $builder)
    {
        foreach ($extra as $key => $extraValue) {
            $builder->andWhere("e.{$key} = :" . $this->prepareParam($key));
            $builder->setParameter($this->prepareParam($key), $extraValue);
        }
    }

    /**
     * @param string $entity
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    protected function getEntityManager($entity)
    {
        if (! is_null($this->connection)) {
            return $this->registry->getManager($this->connection);
        }

        return $this->registry->getManagerForClass($entity);
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function prepareParam($column)
    {
        return str_replace('.', '', $column);
    }

    private function getWhereClause($collection, $column)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->getEntityManager($collection)->getClassMetadata($collection);
        $field = $metadata->fieldMappings[$column];

        switch ($field['type']) {
            case Type::STRING:
            case Type::TEXT:
                return "LOWER(e.{$column}) = LOWER(:{$this->prepareParam($column)})";
            default:
                return "e.{$column} = :{$this->prepareParam($column)}";
        }
    }
}
