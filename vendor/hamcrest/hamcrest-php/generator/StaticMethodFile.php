<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

class StaticMethodFile extends FactoryFile
{
    /**
     * @var string containing method definitions
     */
    private $methods;

    public function __construct($file)
    {
        parent::__construct($file, '    ');
        $this->methods = '';
    }

    public function addCall(FactoryCall $call)
    {
        $this->methods .= PHP_EOL . $this->generateFactoryCall($call);
    }

    public function getDeclarationModifiers()
    {
        return 'public static ';
    }

    public function build()
    {
        $this->addFileHeader();
        $this->addPart('matchers_imports');
        $this->addPart('matchers_header');
        $this->addCode($this->methods);
        $this->addPart('matchers_footer');
    }
}
