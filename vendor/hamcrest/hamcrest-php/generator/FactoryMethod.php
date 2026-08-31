<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

/**
 * Represents a single static factory method from a {@link Matcher} class.
 *
 * @todo Search method in file contents for func_get_args() to replace factoryVarArgs.
 */
class FactoryMethod
{
    /**
     * @var FactoryClass
     */
    private $class;

    /**
     * @var ReflectionMethod
     */
    private $reflector;

    /**
     * @var array of string
     */
    private $comment;

    /**
     * @var bool
     */
    private $isVarArgs;

    /**
     * @var array of FactoryCall
     */
    private $calls;

    /**
     * @var array FactoryParameter
     */
    private $parameters;

    public function __construct(FactoryClass $class, ReflectionMethod $reflector)
    {
        $this->class = $class;
        $this->reflector = $reflector;
        $this->extractCommentWithoutLeadingShashesAndStars();
        $this->extractFactoryNamesFromComment();
        $this->extractParameters();
    }

    public function extractCommentWithoutLeadingShashesAndStars()
    {
        $this->comment = explode("\n", $this->reflector->getDocComment());
        foreach ($this->comment as &$line) {
            $line = preg_replace('#^\s*(/\\*+|\\*+/|\\*)\s?#', '', $line);
        }
        $this->trimLeadingBlankLinesFromComment();
        $this->trimTrailingBlankLinesFromComment();
    }

    public function trimLeadingBlankLinesFromComment()
    {
        while (count($this->comment) > 0) {
            $line = array_shift($this->comment);
            if (trim($line) != '') {
                array_unshift($this->comment, $line);
                break;
            }
        }
    }

    public function trimTrailingBlankLinesFromComment()
    {
        while (count($this->comment) > 0) {
            $line = array_pop($this->comment);
            if (trim($line) != '') {
                array_push($this->comment, $line);
                break;
            }
        }
    }

    public function extractFactoryNamesFromComment()
    {
        $this->calls = array();
        for ($i = 0; $i < count($this->comment); $i++) {
            if ($this->extractFactoryNamesFromLine($this->comment[$i])) {
                unset($this->comment[$i]);
            }
        }
        $this->trimTrailingBlankLinesFromComment();
    }

    public function extractFactoryNamesFromLine($line)
    {
        if (preg_match('/^\s*@factory(\s+(.+))?$/', $line, $match)) {
            $this->createCalls(
                $this->extractFactoryNamesFromAnnotation(
                    isset($match[2]) ? trim($match[2]) : null
                )
            );
            return true;
        }
        return false;
    }

    public function extractFactoryNamesFromAnnotation($value)
    {
        $primaryName = $this->reflector->getName();
        if (empty($value)) {
            return array($primaryName);
        }
        preg_match_all('/(\.{3}|-|[a-zA-Z_][a-zA-Z_0-9]*)/', $value, $match);
        $names = $match[0];
        if (in_array('...', $names)) {
            $this->isVarArgs = true;
        }
        if (!in_array('-', $names) && !in_array($primaryName, $names)) {
            array_unshift($names, $primaryName);
        }
        return $names;
    }

    public function createCalls(array $names)
    {
        $names = array_unique($names);
        foreach ($names as $name) {
            if ($name != '-' && $name != '...') {
                $this->calls[] = new FactoryCall($this, $name);
            }
        }
    }

    public function extractParameters()
    {
        $this->parameters = array();
        if (!$this->isVarArgs) {
            foreach ($this->reflector->getParameters() as $parameter) {
                $this->parameters[] = new FactoryParameter($this, $parameter);
            }
        }
    }

    public function getParameterDeclarations()
    {
        if ($this->isVarArgs || !$this->hasParameters()) {
            return '';
        }
        $params = array();
        foreach ($this->parameters as /** @var $parameter FactoryParameter */
                 $parameter) {
            $params[] = $parameter->getDeclaration();
        }
        return implode(', ', $params);
    }

    public function getParameterInvocations()
    {
        if ($this->isVarArgs) {
            return '';
        }
        $params = array();
        foreach ($this->parameters as $parameter) {
            $params[] = $parameter->getInvocation();
        }
        return implode(', ', $params);
    }


    public function getClass()
    {
        return $this->class;
    }

    public function getClassName()
    {
        return $this->class->getName();
    }

    public function getName()
    {
        return $this->reflector->name;
    }

    public function isFactory()
    {
        return count($this->calls) > 0;
    }

    public function getCalls()
    {
        return $this->calls;
    }

    public function acceptsVariableArguments()
    {
        return $this->isVarArgs;
    }

    public function hasParameters()
    {
        return !empty($this->parameters);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getFullName()
    {
        return $this->getClassName() . '::' . $this->getName();
    }

    public function getCommentText()
    {
        return implode("\n", $this->comment);
    }

    public function getComment($indent = '')
    {
        $comment = $indent . '/**';
        foreach ($this->comment as $line) {
            $comment .= "\n" . rtrim($indent . ' * ' . $line);
        }
        $comment .= "\n" . $indent . ' */';
        return $comment;
    }
}
