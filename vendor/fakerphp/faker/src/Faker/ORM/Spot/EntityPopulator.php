<?php

namespace Faker\ORM\Spot;

use Faker\Generator;
use Faker\Guesser\Name;
use Spot\Locator;
use Spot\Mapper;
use Spot\Relation\BelongsTo;

/**
 * Service class for populating a table through a Spot Entity class.
 */
class EntityPopulator
{
    /**
     * When fetching existing data - fetch only few first rows.
     */
    public const RELATED_FETCH_COUNT = 10;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var Locator
     */
    protected $locator;

    /**
     * @var array
     */
    protected $columnFormatters = [];
    /**
     * @var array
     */
    protected $modifiers = [];

    /**
     * @var bool
     */
    protected $useExistingData = false;

    /**
     * @param bool $useExistingData
     */
    public function __construct(Mapper $mapper, Locator $locator, $useExistingData = false)
    {
        $this->mapper = $mapper;
        $this->locator = $locator;
        $this->useExistingData = $useExistingData;
    }

    /**
     * @return string
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    public function setColumnFormatters($columnFormatters)
    {
        $this->columnFormatters = $columnFormatters;
    }

    /**
     * @return array
     */
    public function getColumnFormatters()
    {
        return $this->columnFormatters;
    }

    public function mergeColumnFormattersWith($columnFormatters)
    {
        $this->columnFormatters = array_merge($this->columnFormatters, $columnFormatters);
    }

    public function setModifiers(array $modifiers)
    {
        $this->modifiers = $modifiers;
    }

    /**
     * @return array
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    public function mergeModifiersWith(array $modifiers)
    {
        $this->modifiers = array_merge($this->modifiers, $modifiers);
    }

    /**
     * @return array
     */
    public function guessColumnFormatters(Generator $generator)
    {
        $formatters = [];
        $nameGuesser = new Name($generator);
        $columnTypeGuesser = new ColumnTypeGuesser($generator);
        $fields = $this->mapper->fields();

        foreach ($fields as $fieldName => $field) {
            if ($field['primary'] === true) {
                continue;
            }

            if ($formatter = $nameGuesser->guessFormat($fieldName)) {
                $formatters[$fieldName] = $formatter;

                continue;
            }

            if ($formatter = $columnTypeGuesser->guessFormat($field)) {
                $formatters[$fieldName] = $formatter;

                continue;
            }
        }
        $entityName = $this->mapper->entity();
        $entity = $this->mapper->build([]);
        $relations = $entityName::relations($this->mapper, $entity);

        foreach ($relations as $relation) {
            // We don't need any other relation here.
            if ($relation instanceof BelongsTo) {
                $fieldName = $relation->localKey();
                $entityName = $relation->entityName();
                $field = $fields[$fieldName];
                $required = $field['required'];

                $locator = $this->locator;

                $formatters[$fieldName] = function ($inserted) use ($required, $entityName, $locator, $generator) {
                    if (!empty($inserted[$entityName])) {
                        return $generator->randomElement($inserted[$entityName])->get('id');
                    }

                    if ($required && $this->useExistingData) {
                        // We did not add anything like this, but it's required,
                        // So let's find something existing in DB.
                        $mapper = $locator->mapper($entityName);
                        $records = $mapper->all()->limit(self::RELATED_FETCH_COUNT)->toArray();

                        if (empty($records)) {
                            return null;
                        }

                        return $generator->randomElement($records)['id'];
                    }

                    return null;
                };
            }
        }

        return $formatters;
    }

    /**
     * Insert one new record using the Entity class.
     *
     * @return string
     */
    public function execute($insertedEntities)
    {
        $obj = $this->mapper->build([]);

        $this->fillColumns($obj, $insertedEntities);
        $this->callMethods($obj, $insertedEntities);

        $this->mapper->insert($obj);

        return $obj;
    }

    private function fillColumns($obj, $insertedEntities)
    {
        foreach ($this->columnFormatters as $field => $format) {
            if (null !== $format) {
                $value = is_callable($format) ? $format($insertedEntities, $obj) : $format;
                $obj->set($field, $value);
            }
        }
    }

    private function callMethods($obj, $insertedEntities)
    {
        foreach ($this->getModifiers() as $modifier) {
            $modifier($obj, $insertedEntities);
        }
    }
}
