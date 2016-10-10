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
use Symfony\Component\CssSelector\Node\FunctionNode;
use Symfony\Component\CssSelector\XPath\Translator;
use Symfony\Component\CssSelector\XPath\XPathExpr;

/**
 * XPath expression translator HTML extension.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class HtmlExtension extends AbstractExtension
{
    /**
     * Constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $translator
            ->getExtension('node')
            ->setFlag(NodeExtension::ELEMENT_NAME_IN_LOWER_CASE, true)
            ->setFlag(NodeExtension::ATTRIBUTE_NAME_IN_LOWER_CASE, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getPseudoClassTranslators()
    {
        return array(
            'checked'  => array($this, 'translateChecked'),
            'link'     => array($this, 'translateLink'),
            'disabled' => array($this, 'translateDisabled'),
            'enabled'  => array($this, 'translateEnabled'),
            'selected' => array($this, 'translateSelected'),
            'invalid'  => array($this, 'translateInvalid'),
            'hover'    => array($this, 'translateHover'),
            'visited'  => array($this, 'translateVisited'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctionTranslators()
    {
        return array(
            'lang' => array($this, 'translateLang'),
        );
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateChecked(XPathExpr $xpath)
    {
        return $xpath->addCondition(
            '(@checked '
            ."and (name(.) = 'input' or name(.) = 'command')"
            ."and (@type = 'checkbox' or @type = 'radio'))"
        );
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateLink(XPathExpr $xpath)
    {
        return $xpath->addCondition("@href and (name(.) = 'a' or name(.) = 'link' or name(.) = 'area')");
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateDisabled(XPathExpr $xpath)
    {
        return $xpath->addCondition(
            "("
                ."@disabled and"
                ."("
                    ."(name(.) = 'input' and @type != 'hidden')"
                    ." or name(.) = 'button'"
                    ." or name(.) = 'select'"
                    ." or name(.) = 'textarea'"
                    ." or name(.) = 'command'"
                    ." or name(.) = 'fieldset'"
                    ." or name(.) = 'optgroup'"
                    ." or name(.) = 'option'"
                .")"
            .") or ("
                ."(name(.) = 'input' and @type != 'hidden')"
                ." or name(.) = 'button'"
                ." or name(.) = 'select'"
                ." or name(.) = 'textarea'"
            .")"
            ." and ancestor::fieldset[@disabled]"
        );
        // todo: in the second half, add "and is not a descendant of that fieldset element's first legend element child, if any."
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateEnabled(XPathExpr $xpath)
    {
        return $xpath->addCondition(
            '('
                .'@href and ('
                    ."name(.) = 'a'"
                    ." or name(.) = 'link'"
                    ." or name(.) = 'area'"
                .')'
            .') or ('
                .'('
                    ."name(.) = 'command'"
                    ." or name(.) = 'fieldset'"
                    ." or name(.) = 'optgroup'"
                .')'
                .' and not(@disabled)'
            .') or ('
                .'('
                    ."(name(.) = 'input' and @type != 'hidden')"
                    ." or name(.) = 'button'"
                    ." or name(.) = 'select'"
                    ." or name(.) = 'textarea'"
                    ." or name(.) = 'keygen'"
                .')'
                ." and not (@disabled or ancestor::fieldset[@disabled])"
            .') or ('
                ."name(.) = 'option' and not("
                    ."@disabled or ancestor::optgroup[@disabled]"
                .')'
            .')'
        );
    }

    /**
     * @param XPathExpr    $xpath
     * @param FunctionNode $function
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function translateLang(XPathExpr $xpath, FunctionNode $function)
    {
        $arguments = $function->getArguments();
        foreach ($arguments as $token) {
            if (!($token->isString() || $token->isIdentifier())) {
                throw new ExpressionErrorException(
                    'Expected a single string or identifier for :lang(), got '
                    .implode(', ', $arguments)
                );
            }
        }

        return $xpath->addCondition(sprintf(
            'ancestor-or-self::*[@lang][1][starts-with(concat('
            ."translate(@%s, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '-')"
            .', %s)]',
            'lang',
            Translator::getXpathLiteral(strtolower($arguments[0]->getValue()).'-')
        ));
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateSelected(XPathExpr $xpath)
    {
        return $xpath->addCondition("(@selected and name(.) = 'option')");
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateInvalid(XPathExpr $xpath)
    {
        return $xpath->addCondition('0');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateHover(XPathExpr $xpath)
    {
        return $xpath->addCondition('0');
    }

    /**
     * @param XPathExpr $xpath
     *
     * @return XPathExpr
     */
    public function translateVisited(XPathExpr $xpath)
    {
        return $xpath->addCondition('0');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'html';
    }
}
