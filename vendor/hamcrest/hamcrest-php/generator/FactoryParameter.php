<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

class FactoryParameter
{
    /**
     * @var FactoryMethod
     */
    private $method;

    /**
     * @var ReflectionParameter
     */
    private $reflector;

    public function __construct(FactoryMethod $method, ReflectionParameter $reflector)
    {
        $this->method = $method;
        $this->reflector = $reflector;
    }

    /**
     * Compute the declaration code.
     *
     * @return string
     */
    public function getDeclaration()
    {
        $code = $this->getTypeCode() . $this->getInvocation();

        if ($this->reflector->isOptional()) {
            $default = $this->reflector->getDefaultValue();
            if (is_null($default)) {
                $default = 'null';
            } elseif (is_bool($default)) {
                $default = $default ? 'true' : 'false';
            } elseif (is_string($default)) {
                $default = "'" . $default . "'";
            } elseif (is_numeric($default)) {
                $default = strval($default);
            } elseif (is_array($default)) {
                $default = 'array()';
            } else {
                echo 'Warning: unknown default type for ' . $this->getMethod()->getFullName() . "\n";
                var_dump($default);
                $default = 'null';
            }
            $code .= ' = ' . $default;
        }
        return $code;
    }

    /**
     * Compute the type code for the parameter.
     *
     * @return string
     */
    private function getTypeCode()
    {
        // Handle PHP 5 separately
        if (PHP_VERSION_ID < 70000) {
            if ($this->reflector->isArray()) {
                return 'array';
            }

            $class = $this->reflector->getClass();

            return $class ? sprintf('\\%s ', $class->getName()) : '';
        }

        if (!$this->reflector->hasType()) {
            return '';
        }

        $type = $this->reflector->getType();
        $name = self::getQualifiedName($type);

        // PHP 7.1+ supports nullable types via a leading question mark
        return (PHP_VERSION_ID >= 70100 && $type->allowsNull()) ? sprintf('?%s ', $name) : sprintf('%s ', $name);
    }

    /**
     * Compute qualified name for the given type.
     *
     * This function knows how to prefix class names with a leading slash and
     * also how to handle PHP 8's union types.
     *
     * @param ReflectionType $type
     *
     * @return string
     */
    private static function getQualifiedName(ReflectionType $type)
    {
        // PHP 8 union types can be recursively processed
        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(function (ReflectionType $type) {
                // The "self::" call within a Closure is fine here because this
                // code will only ever be executed on PHP 7.0+
                return self::getQualifiedName($type);
            }, $type->getTypes()));
        }

        // PHP 7.0 doesn't have named types, but 7.1+ does
        $name = $type instanceof ReflectionNamedType ? $type->getName() : (string) $type;

        return $type->isBuiltin() ? $name : sprintf('\\%s', $name);
    }

    /**
     * Compute the invocation code.
     *
     * @return string
     */
    public function getInvocation()
    {
        return sprintf('$%s', $this->reflector->getName());
    }

    /**
     * Compute the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
