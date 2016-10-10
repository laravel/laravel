<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\NameConverter;

/**
 * CamelCase to Underscore name converter.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CamelCaseToSnakeCaseNameConverter implements NameConverterInterface
{
    /**
     * @var array|null
     */
    private $attributes;

    /**
     * @var bool
     */
    private $lowerCamelCase;

    /**
     * @param null|array $attributes     The list of attributes to rename or null for all attributes.
     * @param bool       $lowerCamelCase Use lowerCamelCase style.
     */
    public function __construct(array $attributes = null, $lowerCamelCase = true)
    {
        $this->attributes = $attributes;
        $this->lowerCamelCase = $lowerCamelCase;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($propertyName)
    {
        if (null === $this->attributes || in_array($propertyName, $this->attributes)) {
            $snakeCasedName = '';

            $len = strlen($propertyName);
            for ($i = 0; $i < $len; ++$i) {
                if (ctype_upper($propertyName[$i])) {
                    $snakeCasedName .= '_'.strtolower($propertyName[$i]);
                } else {
                    $snakeCasedName .= strtolower($propertyName[$i]);
                }
            }

            return $snakeCasedName;
        }

        return $propertyName;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($propertyName)
    {
        $camelCasedName = preg_replace_callback('/(^|_|\.)+(.)/', function ($match) {
            return ('.' === $match[1] ? '_' : '').strtoupper($match[2]);
        }, $propertyName);

        if ($this->lowerCamelCase) {
            $camelCasedName = lcfirst($camelCasedName);
        }

        if (null === $this->attributes || in_array($camelCasedName, $this->attributes)) {
            return $this->lowerCamelCase ? lcfirst($camelCasedName) : $camelCasedName;
        }

        return $propertyName;
    }
}
