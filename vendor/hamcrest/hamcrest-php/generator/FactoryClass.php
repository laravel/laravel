<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

class FactoryClass
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var ReflectionClass
     */
    private $reflector;

    /**
     * @var array
     */
    private $methods;

    public function __construct($file, ReflectionClass $class)
    {
        $this->file = $file;
        $this->reflector = $class;
        $this->extractFactoryMethods();
    }

    public function extractFactoryMethods()
    {
        $this->methods = array();
        foreach ($this->getPublicStaticMethods() as $method) {
            if ($method->isFactory()) {
                $this->methods[] = $method;
            }
        }
    }

    public function getPublicStaticMethods()
    {
        $methods = array();
        foreach ($this->reflector->getMethods(ReflectionMethod::IS_STATIC) as $method) {
            if ($method->isPublic() && $method->getDeclaringClass() == $this->reflector) {
                $methods[] = new FactoryMethod($this, $method);
            }
        }
        return $methods;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getName()
    {
        return $this->reflector->name;
    }

    public function isFactory()
    {
        return !empty($this->methods);
    }

    public function getMethods()
    {
        return $this->methods;
    }
}
