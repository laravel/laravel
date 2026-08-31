<?php

namespace DeepCopy;

use ArrayObject;
use DateInterval;
use DatePeriod;
use DateTimeInterface;
use DateTimeZone;
use DeepCopy\Exception\CloneException;
use DeepCopy\Filter\ChainableFilter;
use DeepCopy\Filter\Filter;
use DeepCopy\Matcher\Matcher;
use DeepCopy\Reflection\ReflectionHelper;
use DeepCopy\TypeFilter\Date\DateIntervalFilter;
use DeepCopy\TypeFilter\Date\DatePeriodFilter;
use DeepCopy\TypeFilter\Spl\ArrayObjectFilter;
use DeepCopy\TypeFilter\Spl\SplDoublyLinkedListFilter;
use DeepCopy\TypeFilter\TypeFilter;
use DeepCopy\TypeMatcher\TypeMatcher;
use ReflectionObject;
use ReflectionProperty;
use SplDoublyLinkedList;

/**
 * @final
 */
class DeepCopy
{
    /**
     * @var object[] List of objects copied.
     */
    private $hashMap = [];

    /**
     * Filters to apply.
     *
     * @var array Array of ['filter' => Filter, 'matcher' => Matcher] pairs.
     */
    private $filters = [];

    /**
     * Type Filters to apply.
     *
     * @var array Array of ['filter' => Filter, 'matcher' => Matcher] pairs.
     */
    private $typeFilters = [];

    /**
     * @var bool
     */
    private $skipUncloneable = false;

    /**
     * @var bool
     */
    private $useCloneMethod;

    /**
     * @param bool $useCloneMethod   If set to true, when an object implements the __clone() function, it will be used
     *                               instead of the regular deep cloning.
     */
    public function __construct($useCloneMethod = false)
    {
        $this->useCloneMethod = $useCloneMethod;

        $this->addTypeFilter(new ArrayObjectFilter($this), new TypeMatcher(ArrayObject::class));
        $this->addTypeFilter(new DateIntervalFilter(), new TypeMatcher(DateInterval::class));
        $this->addTypeFilter(new DatePeriodFilter(), new TypeMatcher(DatePeriod::class));
        $this->addTypeFilter(new SplDoublyLinkedListFilter($this), new TypeMatcher(SplDoublyLinkedList::class));
    }

    /**
     * If enabled, will not throw an exception when coming across an uncloneable property.
     *
     * @param $skipUncloneable
     *
     * @return $this
     */
    public function skipUncloneable($skipUncloneable = true)
    {
        $this->skipUncloneable = $skipUncloneable;

        return $this;
    }

    /**
     * Deep copies the given object.
     *
     * @template TObject
     *
     * @param TObject $object
     *
     * @return TObject
     */
    public function copy($object)
    {
        $this->hashMap = [];

        return $this->recursiveCopy($object);
    }

    public function addFilter(Filter $filter, Matcher $matcher)
    {
        $this->filters[] = [
            'matcher' => $matcher,
            'filter'  => $filter,
        ];
    }

    public function prependFilter(Filter $filter, Matcher $matcher)
    {
        array_unshift($this->filters, [
            'matcher' => $matcher,
            'filter'  => $filter,
        ]);
    }

    public function addTypeFilter(TypeFilter $filter, TypeMatcher $matcher)
    {
        $this->typeFilters[] = [
            'matcher' => $matcher,
            'filter'  => $filter,
        ];
    }

    public function prependTypeFilter(TypeFilter $filter, TypeMatcher $matcher)
    {
        array_unshift($this->typeFilters, [
            'matcher' => $matcher,
            'filter'  => $filter,
        ]);
    }

    private function recursiveCopy($var)
    {
        // Matches Type Filter
        if ($filter = $this->getFirstMatchedTypeFilter($this->typeFilters, $var)) {
            return $filter->apply($var);
        }

        // Resource
        if (is_resource($var)) {
            return $var;
        }

        // Array
        if (is_array($var)) {
            return $this->copyArray($var);
        }

        // Scalar
        if (! is_object($var)) {
            return $var;
        }

        // Enum
        if (PHP_VERSION_ID >= 80100 && enum_exists(get_class($var))) {
            return $var;
        }

        // Object
        return $this->copyObject($var);
    }

    /**
     * Copy an array
     * @param array $array
     * @return array
     */
    private function copyArray(array $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->recursiveCopy($value);
        }

        return $array;
    }

    /**
     * Copies an object.
     *
     * @param object $object
     *
     * @throws CloneException
     *
     * @return object
     */
    private function copyObject($object)
    {
        $objectHash = spl_object_hash($object);

        if (isset($this->hashMap[$objectHash])) {
            return $this->hashMap[$objectHash];
        }

        $reflectedObject = new ReflectionObject($object);
        $isCloneable = $reflectedObject->isCloneable();

        if (false === $isCloneable) {
            if ($this->skipUncloneable) {
                $this->hashMap[$objectHash] = $object;

                return $object;
            }

            throw new CloneException(
                sprintf(
                    'The class "%s" is not cloneable.',
                    $reflectedObject->getName()
                )
            );
        }

        $newObject = clone $object;
        $this->hashMap[$objectHash] = $newObject;

        if ($this->useCloneMethod && $reflectedObject->hasMethod('__clone')) {
            return $newObject;
        }

        if ($newObject instanceof DateTimeInterface || $newObject instanceof DateTimeZone) {
            return $newObject;
        }

        foreach (ReflectionHelper::getProperties($reflectedObject) as $property) {
            $this->copyObjectProperty($newObject, $property);
        }

        return $newObject;
    }

    private function copyObjectProperty($object, ReflectionProperty $property)
    {
        // Ignore static properties
        if ($property->isStatic()) {
            return;
        }

        // Ignore readonly properties
        if (method_exists($property, 'isReadOnly') && $property->isReadOnly()) {
            return;
        }

        // Apply the filters
        foreach ($this->filters as $item) {
            /** @var Matcher $matcher */
            $matcher = $item['matcher'];
            /** @var Filter $filter */
            $filter = $item['filter'];

            if ($matcher->matches($object, $property->getName())) {
                $filter->apply(
                    $object,
                    $property->getName(),
                    function ($object) {
                        return $this->recursiveCopy($object);
                    }
                );

                if ($filter instanceof ChainableFilter) {
                    continue;
                }

                // If a filter matches, we stop processing this property
                return;
            }
        }

        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }

        // Ignore uninitialized properties (for PHP >7.4)
        if (method_exists($property, 'isInitialized') && !$property->isInitialized($object)) {
            return;
        }

        $propertyValue = $property->getValue($object);

        // Copy the property
        $property->setValue($object, $this->recursiveCopy($propertyValue));
    }

    /**
     * Returns first filter that matches variable, `null` if no such filter found.
     *
     * @param array $filterRecords Associative array with 2 members: 'filter' with value of type {@see TypeFilter} and
     *                             'matcher' with value of type {@see TypeMatcher}
     * @param mixed $var
     *
     * @return TypeFilter|null
     */
    private function getFirstMatchedTypeFilter(array $filterRecords, $var)
    {
        $matched = $this->first(
            $filterRecords,
            function (array $record) use ($var) {
                /* @var TypeMatcher $matcher */
                $matcher = $record['matcher'];

                return $matcher->matches($var);
            }
        );

        return isset($matched) ? $matched['filter'] : null;
    }

    /**
     * Returns first element that matches predicate, `null` if no such element found.
     *
     * @param array    $elements Array of ['filter' => Filter, 'matcher' => Matcher] pairs.
     * @param callable $predicate Predicate arguments are: element.
     *
     * @return array|null Associative array with 2 members: 'filter' with value of type {@see TypeFilter} and 'matcher'
     *                    with value of type {@see TypeMatcher} or `null`.
     */
    private function first(array $elements, callable $predicate)
    {
        foreach ($elements as $element) {
            if (call_user_func($predicate, $element)) {
                return $element;
            }
        }

        return null;
    }
}
