<?php

namespace DeepCopy\Reflection;

use DeepCopy\Exception\PropertyException;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;

class ReflectionHelper
{
    /**
     * Retrieves all properties (including private ones), from object and all its ancestors.
     *
     * Standard \ReflectionClass->getProperties() does not return private properties from ancestor classes.
     *
     * @author muratyaman@gmail.com
     * @see http://php.net/manual/en/reflectionclass.getproperties.php
     *
     * @param ReflectionClass $ref
     *
     * @return ReflectionProperty[]
     */
    public static function getProperties(ReflectionClass $ref)
    {
        $props = $ref->getProperties();
        $propsArr = array();

        foreach ($props as $prop) {
            $propertyName = $prop->getName();
            $propsArr[$propertyName] = $prop;
        }

        if ($parentClass = $ref->getParentClass()) {
            $parentPropsArr = self::getProperties($parentClass);
            foreach ($propsArr as $key => $property) {
                $parentPropsArr[$key] = $property;
            }

            return $parentPropsArr;
        }

        return $propsArr;
    }

    /**
     * Retrieves property by name from object and all its ancestors.
     *
     * @param object|string $object
     * @param string $name
     *
     * @throws PropertyException
     * @throws ReflectionException
     *
     * @return ReflectionProperty
     */
    public static function getProperty($object, $name)
    {
        $reflection = is_object($object) ? new ReflectionObject($object) : new ReflectionClass($object);

        if ($reflection->hasProperty($name)) {
            return $reflection->getProperty($name);
        }

        if ($parentClass = $reflection->getParentClass()) {
            return self::getProperty($parentClass->getName(), $name);
        }

        throw new PropertyException(
            sprintf(
                'The class "%s" doesn\'t have a property with the given name: "%s".',
                is_object($object) ? get_class($object) : $object,
                $name
            )
        );
    }
}
