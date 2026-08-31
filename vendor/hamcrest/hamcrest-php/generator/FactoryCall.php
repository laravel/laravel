<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

class FactoryCall
{
    /**
     * Hamcrest standard is two spaces for each level of indentation.
     *
     * @var string
     */
    const INDENT = '    ';

    /**
     * @var FactoryMethod
     */
    private $method;

    /**
     * @var string
     */
    private $name;

    public function __construct(FactoryMethod $method, $name)
    {
        $this->method = $method;
        $this->name = $name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getName()
    {
        return $this->name;
    }
}
