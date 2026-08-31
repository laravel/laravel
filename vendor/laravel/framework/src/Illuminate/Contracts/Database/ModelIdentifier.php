<?php

namespace Illuminate\Contracts\Database;

use Illuminate\Database\Eloquent\Relations\Relation;

class ModelIdentifier
{
    /**
     * Use the Relation morphMap for a Model's name when serializing.
     */
    protected static bool $useMorphMap = false;

    /**
     * The class name of the model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>|string|null
     */
    public $class;

    /**
     * The unique identifier of the model.
     *
     * This may be either a single ID or an array of IDs.
     *
     * @var mixed
     */
    public $id;

    /**
     * The relationships loaded on the model.
     *
     * @var array
     */
    public $relations;

    /**
     * The connection name of the model.
     *
     * @var string|null
     */
    public $connection;

    /**
     * The class name of the model collection.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Collection>|null
     */
    public $collectionClass;

    /**
     * Create a new model identifier.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|null  $class
     * @param  mixed  $id
     * @param  array  $relations
     * @param  mixed  $connection
     */
    public function __construct($class, $id, array $relations, $connection)
    {
        if ($class !== null && self::$useMorphMap) {
            $class = Relation::getMorphAlias($class);
        }

        $this->class = $class;
        $this->id = $id;
        $this->relations = $relations;
        $this->connection = $connection;
    }

    /**
     * Specify the collection class that should be used when serializing / restoring collections.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Collection>  $collectionClass
     * @return $this
     */
    public function useCollectionClass(?string $collectionClass)
    {
        $this->collectionClass = $collectionClass;

        return $this;
    }

    /**
     * Get the fully-qualified class name of the Model.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>|null
     */
    public function getClass(): ?string
    {
        if (self::$useMorphMap && $this->class !== null) {
            return Relation::getMorphedModel($this->class) ?? $this->class;
        }

        return $this->class;
    }

    /**
     * Indicate whether to use the relational morph-map when serializing Models.
     */
    public static function useMorphMap(bool $useMorphMap = true): void
    {
        static::$useMorphMap = $useMorphMap;
    }
}
