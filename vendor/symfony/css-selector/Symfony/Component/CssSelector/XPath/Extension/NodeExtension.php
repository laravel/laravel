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

use Symfony\Component\CssSelector\Node;
use Symfony\Component\CssSelector\XPath\Translator;
use Symfony\Component\CssSelector\XPath\XPathExpr;

/**
 * XPath expression translator node extension.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class NodeExtension extends AbstractExtension
{
    const ELEMENT_NAME_IN_LOWER_CASE    = 1;
    const ATTRIBUTE_NAME_IN_LOWER_CASE  = 2;
    const ATTRIBUTE_VALUE_IN_LOWER_CASE = 4;

    /**
     * @var int
     */
    private $flags;

    /**
     * Constructor.
     *
     * @param int $flags
     */
    public function __construct($flags = 0)
    {
        $this->flags = $flags;
    }

    /**
     * @param int     $flag
     * @param bool    $on
     *
     * @return NodeExtension
     */
    public function setFlag($flag, $on)
    {
        if ($on && !$this->hasFlag($flag)) {
            $this->flags += $flag;
        }

        if (!$on && $this->hasFlag($flag)) {
            $this->flags -= $flag;
        }

        return $this;
    }

    /**
     * @param int $flag
     *
     * @return bool
     */
    public function hasFlag($flag)
    {
        return $this->flags & $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTranslators()
    {
        return array(
            'Selector'         => array($this, 'translateSelector'),
            'CombinedSelector' => array($this, 'translateCombinedSelector'),
            'Negation'         => array($this, 'translateNegation'),
            'Function'         => array($this, 'translateFunction'),
            'Pseudo'           => array($this, 'translatePseudo'),
            'Attribute'        => array($this, 'translateAttribute'),
            'Class'            => array($this, 'translateClass'),
            'Hash'             => array($this, 'translateHash'),
            'Element'          => array($this, 'translateElement'),
        );
    }

    /**
     * @param Node\SelectorNode $node
     * @param Translator        $translator
     *
     * @return XPathExpr
     */
    public function translateSelector(Node\SelectorNode $node, Translator $translator)
    {
        return $translator->nodeToXPath($node->getTree());
    }

    /**
     * @param Node\CombinedSelectorNode $node
     * @param Translator                $translator
     *
     * @return XPathExpr
     */
    public function translateCombinedSelector(Node\CombinedSelectorNode $node, Translator $translator)
    {
        return $translator->addCombination($node->getCombinator(), $node->getSelector(), $node->getSubSelector());
    }

    /**
     * @param Node\NegationNode $node
     * @param Translator        $translator
     *
     * @return XPathExpr
     */
    public function translateNegation(Node\NegationNode $node, Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        $subXpath = $translator->nodeToXPath($node->getSubSelector());
        $subXpath->addNameTest();

        if ($subXpath->getCondition()) {
            return $xpath->addCondition(sprintf('not(%s)', $subXpath->getCondition()));
        }

        return $xpath->addCondition('0');
    }

    /**
     * @param Node\FunctionNode $node
     * @param Translator        $translator
     *
     * @return XPathExpr
     */
    public function translateFunction(Node\FunctionNode $node, Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());

        return $translator->addFunction($xpath, $node);
    }

    /**
     * @param Node\PseudoNode $node
     * @param Translator      $translator
     *
     * @return XPathExpr
     */
    public function translatePseudo(Node\PseudoNode $node, Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());

        return $translator->addPseudoClass($xpath, $node->getIdentifier());
    }

    /**
     * @param Node\AttributeNode $node
     * @param Translator         $translator
     *
     * @return XPathExpr
     */
    public function translateAttribute(Node\AttributeNode $node, Translator $translator)
    {
        $name = $node->getAttribute();
        $safe = $this->isSafeName($name);

        if ($this->hasFlag(self::ATTRIBUTE_NAME_IN_LOWER_CASE)) {
            $name = strtolower($name);
        }

        if ($node->getNamespace()) {
            $name = sprintf('%s:%s', $node->getNamespace(), $name);
            $safe = $safe && $this->isSafeName($node->getNamespace());
        }

        $attribute = $safe ? '@'.$name : sprintf('attribute::*[name() = %s]', Translator::getXpathLiteral($name));
        $value = $node->getValue();
        $xpath = $translator->nodeToXPath($node->getSelector());

        if ($this->hasFlag(self::ATTRIBUTE_VALUE_IN_LOWER_CASE)) {
            $value = strtolower($value);
        }

        return $translator->addAttributeMatching($xpath, $node->getOperator(), $attribute, $value);
    }

    /**
     * @param Node\ClassNode $node
     * @param Translator     $translator
     *
     * @return XPathExpr
     */
    public function translateClass(Node\ClassNode $node, Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());

        return $translator->addAttributeMatching($xpath, '~=', '@class', $node->getName());
    }

    /**
     * @param Node\HashNode $node
     * @param Translator    $translator
     *
     * @return XPathExpr
     */
    public function translateHash(Node\HashNode $node, Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());

        return $translator->addAttributeMatching($xpath, '=', '@id', $node->getId());
    }

    /**
     * @param Node\ElementNode $node
     *
     * @return XPathExpr
     */
    public function translateElement(Node\ElementNode $node)
    {
        $element = $node->getElement();

        if ($this->hasFlag(self::ELEMENT_NAME_IN_LOWER_CASE)) {
            $element = strtolower($element);
        }

        if ($element) {
            $safe = $this->isSafeName($element);
        } else {
            $element = '*';
            $safe = true;
        }

        if ($node->getNamespace()) {
            $element = sprintf('%s:%s', $node->getNamespace(), $element);
            $safe = $safe && $this->isSafeName($node->getNamespace());
        }

        $xpath = new XPathExpr('', $element);

        if (!$safe) {
            $xpath->addNameTest();
        }

        return $xpath;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'node';
    }

    /**
     * Tests if given name is safe.
     *
     * @param string $name
     *
     * @return bool
     */
    private function isSafeName($name)
    {
        return 0 < preg_match('~^[a-zA-Z_][a-zA-Z0-9_.-]*$~', $name);
    }
}
