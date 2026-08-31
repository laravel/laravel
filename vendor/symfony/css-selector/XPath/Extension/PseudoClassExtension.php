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
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class PseudoClassExtension extends AbstractExtension
{
    public function getPseudoClassTranslators(): array
    {
        return [
            'root' => $this->translateRoot(...),
            'scope' => $this->translateScopePseudo(...),
            'first-child' => $this->translateFirstChild(...),
            'last-child' => $this->translateLastChild(...),
            'first-of-type' => $this->translateFirstOfType(...),
            'last-of-type' => $this->translateLastOfType(...),
            'only-child' => $this->translateOnlyChild(...),
            'only-of-type' => $this->translateOnlyOfType(...),
            'empty' => $this->translateEmpty(...),
        ];
    }

    public function translateRoot(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('not(parent::*)');
    }

    public function translateScopePseudo(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('1');
    }

    public function translateFirstChild(XPathExpr $xpath): XPathExpr
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() = 1');
    }

    public function translateLastChild(XPathExpr $xpath): XPathExpr
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() = last()');
    }

    /**
     * @throws ExpressionErrorException
     */
    public function translateFirstOfType(XPathExpr $xpath): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:first-of-type" is not implemented.');
        }

        return $xpath
            ->addStarPrefix()
            ->addCondition('position() = 1');
    }

    /**
     * @throws ExpressionErrorException
     */
    public function translateLastOfType(XPathExpr $xpath): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:last-of-type" is not implemented.');
        }

        return $xpath
            ->addStarPrefix()
            ->addCondition('position() = last()');
    }

    public function translateOnlyChild(XPathExpr $xpath): XPathExpr
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('last() = 1');
    }

    public function translateOnlyOfType(XPathExpr $xpath): XPathExpr
    {
        $element = $xpath->getElement();

        return $xpath->addCondition(\sprintf('count(preceding-sibling::%s)=0 and count(following-sibling::%s)=0', $element, $element));
    }

    public function translateEmpty(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('not(*) and not(string-length())');
    }

    public function getName(): string
    {
        return 'pseudo-class';
    }
}
