<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\XPath\Extension;

use Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use Symfony\Component\CssSelector\XPath\XPathExpr;

/**
 * XPath expression translator pseudo-class extension.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class PseudoClassExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getPseudoClassTranslators()
    {
        return array(
            'root'          => array($this, 'translateRoot'),
            'first-child'   => array($this, 'translateFirstChild'),
            'last-child'    => array($this, 'translateLastChild'),
            'first-of-type' => array($this, 'translateFirstOfType'),
            'last-of-type'  => array($this, 'translateLastOfType'),
            'only-child'    => array($this, 'translateOnlyChild'),
            'only-of-type'  => array($this, 'translateOnlyOfType'),
            'empty'         => array($this, 'translateEmpty'),
        );
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateRoot(XPathExpr $xpath)
    {
        return $xpath->addCondition('not(parent::*)');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateFirstChild(XPathExpr $xpath)
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() = 1');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateLastChild(XPathExpr $xpath)
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() = last()');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function translateFirstOfType(XPathExpr $xpath)
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:first-of-type" is not implemented.');
        }

        return $xpath
            ->addStarPrefix()
            ->addCondition('position() = 1');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function translateLastOfType(XPathExpr $xpath)
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:last-of-type" is not implemented.');
        }

        return $xpath
            ->addStarPrefix()
            ->addCondition('position() = last()');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateOnlyChild(XPathExpr $xpath)
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('last() = 1');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function translateOnlyOfType(XPathExpr $xpath)
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:only-of-type" is not implemented.');
        }

        return $xpath->addCondition('last() = 1');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateEmpty(XPathExpr $xpath)
    {
        return $xpath->addCondition('not(*) and not(string-length())');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pseudo-class';
    }
}
