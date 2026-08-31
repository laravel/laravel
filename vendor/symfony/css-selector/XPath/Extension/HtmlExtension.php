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
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class HtmlExtension extends AbstractExtension
{
    public function __construct(Translator $translator)
    {
        $translator
            ->getExtension('node')
            ->setFlag(NodeExtension::ELEMENT_NAME_IN_LOWER_CASE, true)
            ->setFlag(NodeExtension::ATTRIBUTE_NAME_IN_LOWER_CASE, true);
    }

    public function getPseudoClassTranslators(): array
    {
        return [
            'checked' => $this->translateChecked(...),
            'link' => $this->translateLink(...),
            'disabled' => $this->translateDisabled(...),
            'enabled' => $this->translateEnabled(...),
            'selected' => $this->translateSelected(...),
            'invalid' => $this->translateInvalid(...),
            'hover' => $this->translateHover(...),
            'visited' => $this->translateVisited(...),
        ];
    }

    public function getFunctionTranslators(): array
    {
        return [
            'lang' => $this->translateLang(...),
        ];
    }

    public function translateChecked(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition(
            '(@checked '
            ."and (name(.) = 'input' or name(.) = 'command')"
            ."and (@type = 'checkbox' or @type = 'radio'))"
        );
    }

    public function translateLink(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition("@href and (name(.) = 'a' or name(.) = 'link' or name(.) = 'area')");
    }

    public function translateDisabled(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition(
            '('
                .'@disabled and'
                .'('
                    ."(name(.) = 'input' and @type != 'hidden')"
                    ." or name(.) = 'button'"
                    ." or name(.) = 'select'"
                    ." or name(.) = 'textarea'"
                    ." or name(.) = 'command'"
                    ." or name(.) = 'fieldset'"
                    ." or name(.) = 'optgroup'"
                    ." or name(.) = 'option'"
                .')'
            .') or ('
                ."(name(.) = 'input' and @type != 'hidden')"
                ." or name(.) = 'button'"
                ." or name(.) = 'select'"
                ." or name(.) = 'textarea'"
            .')'
            .' and ancestor::fieldset[@disabled]'
        );
        // todo: in the second half, add "and is not a descendant of that fieldset element's first legend element child, if any."
    }

    public function translateEnabled(XPathExpr $xpath): XPathExpr
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
                .' and not (@disabled or ancestor::fieldset[@disabled])'
            .') or ('
                ."name(.) = 'option' and not("
                    .'@disabled or ancestor::optgroup[@disabled]'
                .')'
            .')'
        );
    }

    /**
     * @throws ExpressionErrorException
     */
    public function translateLang(XPathExpr $xpath, FunctionNode $function): XPathExpr
    {
        $arguments = $function->getArguments();
        foreach ($arguments as $token) {
            if (!($token->isString() || $token->isIdentifier())) {
                throw new ExpressionErrorException('Expected a single string or identifier for :lang(), got '.implode(', ', $arguments));
            }
        }

        return $xpath->addCondition(\sprintf(
            'ancestor-or-self::*[@lang][1][starts-with(concat('
            ."translate(@%s, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '-')"
            .', %s)]',
            'lang',
            Translator::getXpathLiteral(strtolower($arguments[0]->getValue()).'-')
        ));
    }

    public function translateSelected(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition("(@selected and name(.) = 'option')");
    }

    public function translateInvalid(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('0');
    }

    public function translateHover(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('0');
    }

    public function translateVisited(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('0');
    }

    public function getName(): string
    {
        return 'html';
    }
}
