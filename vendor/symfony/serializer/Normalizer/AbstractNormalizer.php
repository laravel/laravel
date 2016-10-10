<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

/**
 * Normalizer implementation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
abstract class AbstractNormalizer extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    const CIRCULAR_REFERENCE_LIMIT = 'circular_reference_limit';
    const OBJECT_TO_POPULATE = 'object_to_populate';
    const GROUPS = 'groups';

    /**
     * @var int
     */
    protected $circularReferenceLimit = 1;

    /**
     * @var callable
     */
    protected $circularReferenceHandler;

    /**
     * @var ClassMetadataFactoryInterface|null
     */
    protected $classMetadataFactory;

    /**
     * @var NameConverterInterface|null
     */
    protected $nameConverter;

    /**
     * @var array
     */
    protected $callbacks = array();

    /**
     * @var array
     */
    protected $ignoredAttributes = array();

    /**
     * @var array
     */
    protected $camelizedAttributes = array();

    /**
     * Sets the {@link ClassMetadataFactoryInterface} to use.
     *
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null        $nameConverter
     */
    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null)
    {
        $this->classMetadataFactory = $classMetadataFactory;
        $this->nameConverter = $nameConverter;
    }

    /**
     * Set circular reference limit.
     *
     * @param int $circularReferenceLimit limit of iterations for the same object
     *
     * @return self
     */
    public function setCircularReferenceLimit($circularReferenceLimit)
    {
        $this->circularReferenceLimit = $circularReferenceLimit;

        return $this;
    }

    /**
     * Set circular reference handler.
     *
     * @param callable $circularReferenceHandler
     *
     * @return self
     */
    public function setCircularReferenceHandler(callable $circularReferenceHandler)
    {
        $this->circularReferenceHandler = $circularReferenceHandler;

        return $this;
    }

    /**
     * Set normalization callbacks.
     *
     * @param callable[] $callbacks help normalize the result
     *
     * @return self
     *
     * @throws InvalidArgumentException if a non-callable callback is set
     */
    public function setCallbacks(array $callbacks)
    {
        foreach ($callbacks as $attribute => $callback) {
            if (!is_callable($callback)) {
                throw new InvalidArgumentException(sprintf(
                    'The given callback for attribute "%s" is not callable.',
                    $attribute
                ));
            }
        }
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * Set ignored attributes for normalization and denormalization.
     *
     * @param array $ignoredAttributes
     *
     * @return self
     */
    public function setIgnoredAttributes(array $ignoredAttributes)
    {
        $this->ignoredAttributes = $ignoredAttributes;

        return $this;
    }

    /**
     * Detects if the configured circular reference limit is reached.
     *
     * @param object $object
     * @param array  $context
     *
     * @return bool
     *
     * @throws CircularReferenceException
     */
    protected function isCircularReference($object, &$context)
    {
        $objectHash = spl_object_hash($object);

        if (isset($context[static::CIRCULAR_REFERENCE_LIMIT][$objectHash])) {
            if ($context[static::CIRCULAR_REFERENCE_LIMIT][$objectHash] >= $this->circularReferenceLimit) {
                unset($context[static::CIRCULAR_REFERENCE_LIMIT][$objectHash]);

                return true;
            }

            ++$context[static::CIRCULAR_REFERENCE_LIMIT][$objectHash];
        } else {
            $context[static::CIRCULAR_REFERENCE_LIMIT][$objectHash] = 1;
        }

        return false;
    }

    /**
     * Handles a circular reference.
     *
     * If a circular reference handler is set, it will be called. Otherwise, a
     * {@class CircularReferenceException} will be thrown.
     *
     * @param object $object
     *
     * @return mixed
     *
     * @throws CircularReferenceException
     */
    protected function handleCircularReference($object)
    {
        if ($this->circularReferenceHandler) {
            return call_user_func($this->circularReferenceHandler, $object);
        }

        throw new CircularReferenceException(sprintf('A circular reference has been detected (configured limit: %d).', $this->circularReferenceLimit));
    }

    /**
     * Gets attributes to normalize using groups.
     *
     * @param string|object $classOrObject
     * @param array         $context
     * @param bool          $attributesAsString If false, return an array of {@link AttributeMetadataInterface}
     *
     * @return string[]|AttributeMetadataInterface[]|bool
     */
    protected function getAllowedAttributes($classOrObject, array $context, $attributesAsString = false)
    {
        if (!$this->classMetadataFactory || !isset($context[static::GROUPS]) || !is_array($context[static::GROUPS])) {
            return false;
        }

        $allowedAttributes = array();
        foreach ($this->classMetadataFactory->getMetadataFor($classOrObject)->getAttributesMetadata() as $attributeMetadata) {
            $name = $attributeMetadata->getName();

            if (
                count(array_intersect($attributeMetadata->getGroups(), $context[static::GROUPS])) &&
                $this->isAllowedAttribute($classOrObject, $name, null, $context)
            ) {
                $allowedAttributes[] = $attributesAsString ? $name : $attributeMetadata;
            }
        }

        return $allowedAttributes;
    }

    /**
     * Is this attribute allowed?
     *
     * @param object|string $classOrObject
     * @param string        $attribute
     * @param string|null   $format
     * @param array         $context
     *
     * @return bool
     */
    protected function isAllowedAttribute($classOrObject, $attribute, $format = null, array $context = array())
    {
        return !in_array($attribute, $this->ignoredAttributes);
    }

    /**
     * Normalizes the given data to an array. It's particularly useful during
     * the denormalization process.
     *
     * @param object|array $data
     *
     * @return array
     */
    protected function prepareForDenormalization($data)
    {
        return (array) $data;
    }

    /**
     * Instantiates an object using constructor parameters when needed.
     *
     * This method also allows to denormalize data into an existing object if
     * it is present in the context with the object_to_populate. This object
     * is removed from the context before being returned to avoid side effects
     * when recursively normalizing an object graph.
     *
     * @param array            $data
     * @param string           $class
     * @param array            $context
     * @param \ReflectionClass $reflectionClass
     * @param array|bool       $allowedAttributes
     *
     * @return object
     *
     * @throws RuntimeException
     */
    protected function instantiateObject(array &$data, $class, array &$context, \ReflectionClass $reflectionClass, $allowedAttributes)
    {
        if (
            isset($context[static::OBJECT_TO_POPULATE]) &&
            is_object($context[static::OBJECT_TO_POPULATE]) &&
            $context[static::OBJECT_TO_POPULATE] instanceof $class
        ) {
            $object = $context[static::OBJECT_TO_POPULATE];
            unset($context[static::OBJECT_TO_POPULATE]);

            return $object;
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor) {
            $constructorParameters = $constructor->getParameters();

            $params = array();
            foreach ($constructorParameters as $constructorParameter) {
                $paramName = $constructorParameter->name;
                $key = $this->nameConverter ? $this->nameConverter->normalize($paramName) : $paramName;

                $allowed = $allowedAttributes === false || in_array($paramName, $allowedAttributes);
                $ignored = in_array($paramName, $this->ignoredAttributes);
                if (method_exists($constructorParameter, 'isVariadic') && $constructorParameter->isVariadic()) {
                    if ($allowed && !$ignored && (isset($data[$key]) || array_key_exists($key, $data))) {
                        if (!is_array($data[$paramName])) {
                            throw new RuntimeException(sprintf('Cannot create an instance of %s from serialized data because the variadic parameter %s can only accept an array.', $class, $constructorParameter->name));
                        }

                        $params = array_merge($params, $data[$paramName]);
                    }
                } elseif ($allowed && !$ignored && (isset($data[$key]) || array_key_exists($key, $data))) {
                    $params[] = $data[$key];
                    // don't run set for a parameter passed to the constructor
                    unset($data[$key]);
                } elseif ($constructorParameter->isDefaultValueAvailable()) {
                    $params[] = $constructorParameter->getDefaultValue();
                } else {
                    throw new RuntimeException(
                        sprintf(
                            'Cannot create an instance of %s from serialized data because its constructor requires parameter "%s" to be present.',
                            $class,
                            $constructorParameter->name
                        )
                    );
                }
            }

            return $reflectionClass->newInstanceArgs($params);
        }

        return new $class();
    }
}
