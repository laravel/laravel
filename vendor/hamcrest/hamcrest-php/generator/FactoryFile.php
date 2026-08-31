<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

abstract class FactoryFile
{
    /**
     * Hamcrest standard is two spaces for each level of indentation.
     *
     * @var string
     */
    const INDENT = '    ';

    private $indent;

    private $file;

    private $code;

    public function __construct($file, $indent)
    {
        $this->file = $file;
        $this->indent = $indent;
    }

    abstract public function addCall(FactoryCall $call);

    abstract public function build();

    public function addFileHeader()
    {
        $this->code = '';
        $this->addPart('file_header');
    }

    public function addPart($name)
    {
        $this->addCode($this->readPart($name));
    }

    public function addCode($code)
    {
        $this->code .= $code;
    }

    public function readPart($name)
    {
        return file_get_contents(__DIR__ . "/parts/$name.txt");
    }

    public function generateFactoryCall(FactoryCall $call)
    {
        $method = $call->getMethod();
        $code = $method->getComment($this->indent) . "\n";
        $code .= $this->generateDeclaration($call->getName(), $method);
        $code .= $this->generateCall($method);
        $code .= $this->generateClosing();
        return $code;
    }

    public function generateDeclaration($name, FactoryMethod $method)
    {
        $code = $this->indent . $this->getDeclarationModifiers()
            . 'function ' . $name . '('
            . $this->generateDeclarationArguments($method)
            . ')' . "\n" . $this->indent . '{' . "\n";
        return $code;
    }

    public function getDeclarationModifiers()
    {
        return '';
    }

    public function generateDeclarationArguments(FactoryMethod $method)
    {
        if ($method->acceptsVariableArguments()) {
            return '/* args... */';
        } else {
            return $method->getParameterDeclarations();
        }
    }

    public function generateImport(FactoryMethod $method)
    {
        return $this->indent . self::INDENT . "require_once '" . $method->getClass()->getFile() . "';" . "\n";
    }

    public function generateCall(FactoryMethod $method)
    {
        $code = '';
        if ($method->acceptsVariableArguments()) {
            $code .= $this->indent . self::INDENT . '$args = func_get_args();' . "\n";
        }

        $code .= $this->indent . self::INDENT . 'return ';
        if ($method->acceptsVariableArguments()) {
            $code .= 'call_user_func_array(array(\''
                . '\\' . $method->getClassName() . '\', \''
                . $method->getName() . '\'), $args);' . "\n";
        } else {
            $code .= '\\' . $method->getClassName() . '::'
                . $method->getName() . '('
                . $method->getParameterInvocations() . ');' . "\n";
        }

        return $code;
    }

    public function generateClosing()
    {
        return $this->indent . '}' . "\n";
    }

    public function write()
    {
        file_put_contents($this->file, $this->code);
    }
}
