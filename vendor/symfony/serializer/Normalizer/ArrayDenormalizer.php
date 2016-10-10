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

use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Denormalizes arrays of objects.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 */
class ArrayDenormalizer implements DenormalizerInterface, SerializerAwareInterface
{
    /**
     * @var SerializerInterface|DenormalizerInterface
     */
    private $serializer;

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if ($this->serializer === null) {
            throw new BadMethodCallException('Please set a serializer before calling denormalize()!');
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('Data expected to be an array, '.gettype($data).' given.');
        }
        if (substr($class, -2) !== '[]') {
            throw new InvalidArgumentException('Unsupported class: '.$class);
        }

        $serializer = $this->serializer;
        $class = substr($class, 0, -2);

        return array_map(
            function ($data) use ($serializer, $class, $format, $context) {
                return $serializer->denormalize($data, $class, $format, $context);
            },
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return substr($type, -2) === '[]'
            && $this->serializer->supportsDenormalization($data, substr($type, 0, -2), $format);
    }

    /**
     * {@inheritdoc}
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        if (!$serializer instanceof DenormalizerInterface) {
            throw new InvalidArgumentException('Expected a serializer that also implements DenormalizerInterface.');
        }

        $this->serializer = $serializer;
    }
}
