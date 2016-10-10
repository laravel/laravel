<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\XPath;

/**
 * XPath expression translator interface.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class XPathExpr
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $element;

    /**
     * @var string
     */
    private $condition;

    /**
     * @param string  $path
     * @param string  $element
     * @param string  $condition
     * @param bool    $starPrefix
     */
    public function __construct($path = '', $element = '*', $condition = '', $starPrefix = false)
    {
        $this->path = $path;
        $this->element = $element;
        $this->condition = $condition;

        if ($starPrefix) {
            $this->addStarPrefix();
        }
    }

    /**
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param $condition
     *
     * @return XPathExpr
     */
    public function addCondition($condition)
    {
        $this->condition = $this->condition ? sprintf('%s and (%s)', $this->condition, $condition) : $condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return XPathExpr
     */
    public function addNameTest()
    {
        if ('*' !== $this->element) {
            $this->addCondition('name() = '.Translator::getXpathLiteral($this->element));
            $this->element = '*';
        }

        return $this;
    }

    /**
     * @return XPathExpr
     */
    public function addStarPrefix()
    {
        $this->path .= '*/';

        return $this;
    }

    /**
     * Joins another XPathExpr with a combiner.
     *
     * @param string    $combiner
     * @param XPathExpr $expr
     *
     * @return XPathExpr
     */
    public function join($combiner, XPathExpr $expr)
    {
        $path = $this->__toString().$combiner;

        if ('*/' !== $expr->path) {
            $path .= $expr->path;
        }

        $this->path = $path;
        $this->element = $expr->element;
        $this->condition = $expr->condition;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $path = $this->path.$this->element;
        $condition = null === $this->condition || '' === $this->condition ? '' : '['.$this->condition.']';

        return $path.$condition;
    }
}
