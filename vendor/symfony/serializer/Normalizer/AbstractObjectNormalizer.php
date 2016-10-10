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

use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Base class for a normalizer dealing with objects.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
abstract class AbstractObjectNormalizer extends AbstractNormalizer
{
    const ENABLE_MAX_DEPTH = 'enable_max_depth';
    const DEPTH_KEY_PATTERN = 'depth_%s::%s';

    private $propertyTypeExtractor;
    private $attributesCache = array();

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        parent::__construct($classMetadataFactory, $nameConverter);

        $this->propertyTypeExtractor = $propertyTypeExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && !$data instanceof \Traversable;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CircularReferenceException
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($context);
        }

        if ($this->isCircularReference($object, $context)) {
            return $this->handleCircularReference($object);
        }

        $data = array();
        $stack = array();
        $attributes = $this->getAttributes($object, $format, $context);
        $class = get_class($object);

        foreach ($attributes as $attribute) {
            if ($this->isMaxDepthReached($class, $attribute, $context)) {
                continue;
            }

            $attributeValue = $this->getAttributeValue($object, $attribute, $format, $context);

            if (isset($this->callbacks[$attribute])) {
                $attributeValue = call_user_func($this->callbacks[$attribute], $attributeValue);
            }

            if (null !== $attributeValue && !is_scalar($attributeValue)) {
                $stack[$attribute] = $attributeValue;
            }

            $data = $this->updateData($data, $attribute, $attributeValue);
        }

        foreach ($stack as $attribute => $attributeValue) {
            if (!$this->serializer instanceof NormalizerInterface) {
                throw new LogicException(sprintf('Cannot normalize attribute "%s" because the injected serializer is not a normalizer', $attribute));
            }

            $data = $this->updateData($data, $attribute, $this->serializer->normalize($attributeValue, $format, $context));
        }

        return $data;
    }

    /**
     * Gets and caches attributes for the given object, format and context.
     *
     * @param object      $object
     * @param string|null $format
     * @param array       $context
     *
     * @return string[]
     */
    protected function getAttributes($object, $format = null, array $context)
    {
        $class = get_class($object);
        $key = $class.'-'.$context['cache_key'];

        if (isset($this->attributesCache[$key])) {
            return $this->attributesCache[$key];
        }

        $allowedAttributes = $this->getAllowedAttributes($object, $context, true);

        if (false !== $allowedAttributes) {
            if ($context['cache_key']) {
                $this->attributesCache[$key] = $allowedAttributes;
            }

            return $allowedAttributes;
        }

        if (isset($this->attributesCache[$class])) {
            return $this->attributesCache[$class];
        }

        return $this->attributesCache[$class] = $this->extractAttributes($object, $format, $context);
    }

    /**
     * Extracts attributes to normalize from the class of the given object, format and context.
     *
     * @param object      $object
     * @param string|null $format
     * @param array       $context
     *
     * @return string[]
     */
    abstract protected function extractAttributes($object, $format = null, array $context = array());

    /**
     * Gets the attribute value.
     *
     * @param object      $object
     * @param string      $attribute
     * @param string|null $format
     * @param array       $context
     *
     * @return mixed
     */
    abstract protected function getAttributeValue($object, $attribute, $format = null, array $context = array());

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($context);
        }
        $allowedAttributes = $this->getAllowedAttributes($class, $context, true);
        $normalizedData = $this->prepareForDenormalization($data);

        $reflectionClass = new \ReflectionClass($class);
        $object = $this->instantiateObject($normalizedData, $class, $context, $reflectionClass, $allowedAttributes);

        foreach ($normalizedData as $attribute => $value) {
            if ($this->nameConverter) {
                $attribute = $this->nameConverter->denormalize($attribute);
            }

            if (($allowedAttributes !== false && !in_array($attribute, $allowedAttributes)) || !$this->isAllowedAttribute($class, $attribute, $format, $context)) {
                continue;
            }

            $value = $this->validateAndDenormalize($class, $attribute, $value, $format, $context);
            try {
                $this->setAttributeValue($object, $attribute, $value, $format, $context);
            } catch (InvalidArgumentException $e) {
                throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $object;
    }

    /**
     * Sets attribute value.
     *
     * @param object      $object
     * @param string      $attribute
     * @param mixed       $value
     * @param string|null $format
     * @param array       $context
     */
    abstract protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = array());

    /**
     * Should this attribute be normalized?
     *
     * @param mixed  $object
     * @param string $attributeName
     * @param array  $context
     *
     * @return bool
     */
    protected function isAttributeToNormalize($object, $attributeName, &$context)
    {
        return !in_array($attributeName, $this->ignoredAttributes) && !$this->isMaxDepthReached(get_class($object), $attributeName, $context);
    }

    /**
     * Validates the submitted data and denormalizes it.
     *
     * @param string      $currentClass
     * @param string      $attribute
     * @param mixed       $data
     * @param string|null $format
     * @param array       $context
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     * @throws LogicException
     */
    private function validateAndDenormalize($currentClass, $attribute, $data, $format, array $context)
    {
        if (null === $this->propertyTypeExtractor || null === $types = $this->propertyTypeExtractor->getTypes($currentClass, $attribute)){
            return $data;
        }

        $expectedTypes = array();
        foreach ($types as $type) {
            if (null === $data && $type->isNullable()) {
                return;
            }

            $builtinType = $type->getBuiltinType();
            $class = $type->getClassName();
            $expectedTypes[Type::BUILTIN_TYPE_OBJECT === $builtinType && $class ? $class : $builtinType] = true;

            if (Type::BUILTIN_TYPE_OBJECT === $builtinType) {
                if (!$this->serializer instanceof DenormalizerInterface) {
                    throw new LogicException(sprintf('Cannot denormalize attribute "%s" for class "%s" because injected serializer is not a denormalizer', $attribute, $class));
                }

                if ($this->serializer->supportsDenormalization($data, $class, $format)) {
                    return $this->serializer->denormalize($data, $class, $format, $context);
                }
            }

            if (call_user_func('is_'.$builtinType, $data)) {
                return $data;
            }
        }

        throw new UnexpectedValueException(sprintf('The type of the "%s" attribute for class "%s" must be one of "%s" ("%s" given).', $attribute, $currentClass, implode('", "', array_keys($expectedTypes)), gettype($data)));
    }

    /**
     * Sets an attribute and apply the name converter if necessary.
     *
     * @param array  $data
     * @param string $attribute
     * @param mixed  $attributeValue
     *
     * @return array
     */
    private function updateData(array $data, $attribute, $attributeValue)
    {
        if ($this->nameConverter) {
            $attribute = $this->nameConverter->normalize($attribute);
        }

        $data[$attribute] = $attributeValue;

        return $data;
    }

    /**
     * Is the max depth reached for the given attribute?
     *
     * @param string $class
     * @param string $attribute
     * @param array  $context
     *
     * @return bool
     */
    private function isMaxDepthReached($class, $attribute, array &$context)
    {
        if (!$this->classMetadataFactory || !isset($context[static::ENABLE_MAX_DEPTH])) {
            return false;
        }

        $classMetadata = $this->classMetadataFactory->getMetadataFor($class);
        $attributesMetadata = $classMetadata->getAttributesMetadata();

        if (!isset($attributesMetadata[$attribute])) {
            return false;
        }

        $maxDepth = $attributesMetadata[$attribute]->getMaxDepth();
        if (null === $maxDepth) {
            return false;
        }

        $key = sprintf(static::DEPTH_KEY_PATTERN, $class, $attribute);
        $keyExist = isset($context[$key]);

        if ($keyExist && $context[$key] === $maxDepth) {
            return true;
        }

        if ($keyExist) {
            ++$context[$key];
        } else {
            $context[$key] = 1;
        }

        return false;
    }

    /**
     * Gets the cache key to use.
     *
     * @param array $context
     *
     * @return bool|string
     */
    private function getCacheKey(array $context)
    {
        try {
            return md5(serialize($context));
        } catch (\Exception $exception) {
            // The context cannot be serialized, skip the cache
            return false;
        }
    }
}
