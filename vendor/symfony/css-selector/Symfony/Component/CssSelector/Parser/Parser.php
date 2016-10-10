<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Parser;

use Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Symfony\Component\CssSelector\Node;
use Symfony\Component\CssSelector\Parser\Tokenizer\Tokenizer;

/**
 * CSS selector parser.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class Parser implements ParserInterface
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * Constructor.
     *
     * @param null|Tokenizer $tokenizer
     */
    public function __construct(Tokenizer $tokenizer = null)
    {
        $this->tokenizer = $tokenizer ?: new Tokenizer();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        $reader = new Reader($source);
        $stream = $this->tokenizer->tokenize($reader);

        return $this->parseSelectorList($stream);
    }

    /**
     * Parses the arguments for ":nth-child()" and friends.
     *
     * @param Token[] $tokens
     *
     * @throws SyntaxErrorException
     *
     * @return array
     */
    public static function parseSeries(array $tokens)
    {
        foreach ($tokens as $token) {
            if ($token->isString()) {
                throw SyntaxErrorException::stringAsFunctionArgument();
            }
        }

        $joined = trim(implode('', array_map(function (Token $token) {
            return $token->getValue();
        }, $tokens)));

        $int = function ($string) {
            if (!is_numeric($string)) {
                throw SyntaxErrorException::stringAsFunctionArgument();
            }

            return (int) $string;
        };

        switch (true) {
            case 'odd' === $joined:
                return array(2, 1);
            case 'even' === $joined:
                return array(2, 0);
            case 'n' === $joined:
                return array(1, 0);
            case false === strpos($joined, 'n'):
                return array(0, $int($joined));
        }

        $split = explode('n', $joined);
        $first = isset($split[0]) ? $split[0] : null;

        return array(
            $first ? ('-' === $first || '+' === $first ? $int($first.'1') : $int($first)) : 1,
            isset($split[1]) && $split[1] ? $int($split[1]) : 0,
        );
    }

    /**
     * Parses selector nodes.
     *
     * @param TokenStream $stream
     *
     * @return array
     */
    private function parseSelectorList(TokenStream $stream)
    {
        $stream->skipWhitespace();
        $selectors = array();

        while (true) {
            $selectors[] = $this->parserSelectorNode($stream);

            if ($stream->getPeek()->isDelimiter(array(','))) {
                $stream->getNext();
                $stream->skipWhitespace();
            } else {
                break;
            }
        }

        return $selectors;
    }

    /**
     * Parses next selector or combined node.
     *
     * @param TokenStream $stream
     *
     * @throws SyntaxErrorException
     *
     * @return Node\SelectorNode
     */
    private function parserSelectorNode(TokenStream $stream)
    {
        list($result, $pseudoElement) = $this->parseSimpleSelector($stream);

        while (true) {
            $stream->skipWhitespace();
            $peek = $stream->getPeek();

            if ($peek->isFileEnd() || $peek->isDelimiter(array(','))) {
                break;
            }

            if (null !== $pseudoElement) {
                throw SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }

            if ($peek->isDelimiter(array('+', '>', '~'))) {
                $combinator = $stream->getNext()->getValue();
                $stream->skipWhitespace();
            } else {
                $combinator = ' ';
            }

            list($nextSelector, $pseudoElement) = $this->parseSimpleSelector($stream);
            $result = new Node\CombinedSelectorNode($result, $combinator, $nextSelector);
        }

        return new Node\SelectorNode($result, $pseudoElement);
    }

    /**
     * Parses next simple node (hash, class, pseudo, negation).
     *
     * @param TokenStream $stream
     * @param bool        $insideNegation
     *
     * @throws SyntaxErrorException
     *
     * @return array
     */
    private function parseSimpleSelector(TokenStream $stream, $insideNegation = false)
    {
        $stream->skipWhitespace();

        $selectorStart = count($stream->getUsed());
        $result = $this->parseElementNode($stream);
        $pseudoElement = null;

        while (true) {
            $peek = $stream->getPeek();
            if ($peek->isWhitespace()
                || $peek->isFileEnd()
                || $peek->isDelimiter(array(',', '+', '>', '~'))
                || ($insideNegation && $peek->isDelimiter(array(')')))
            ) {
                break;
            }

            if (null !== $pseudoElement) {
                throw SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }

            if ($peek->isHash()) {
                $result = new Node\HashNode($result, $stream->getNext()->getValue());
            } elseif ($peek->isDelimiter(array('.'))) {
                $stream->getNext();
                $result = new Node\ClassNode($result, $stream->getNextIdentifier());
            } elseif ($peek->isDelimiter(array('['))) {
                $stream->getNext();
                $result = $this->parseAttributeNode($result, $stream);
            } elseif ($peek->isDelimiter(array(':'))) {
                $stream->getNext();

                if ($stream->getPeek()->isDelimiter(array(':'))) {
                    $stream->getNext();
                    $pseudoElement = $stream->getNextIdentifier();

                    continue;
                }

                $identifier = $stream->getNextIdentifier();
                if (in_array(strtolower($identifier), array('first-line', 'first-letter', 'before', 'after'))) {
                    // Special case: CSS 2.1 pseudo-elements can have a single ':'.
                    // Any new pseudo-element must have two.
                    $pseudoElement = $identifier;

                    continue;
                }

                if (!$stream->getPeek()->isDelimiter(array('('))) {
                    $result = new Node\PseudoNode($result, $identifier);

                    continue;
                }

                $stream->getNext();
                $stream->skipWhitespace();

                if ('not' === strtolower($identifier)) {
                    if ($insideNegation) {
                        throw SyntaxErrorException::nestedNot();
                    }

                    list($argument, $argumentPseudoElement) = $this->parseSimpleSelector($stream, true);
                    $next = $stream->getNext();

                    if (null !== $argumentPseudoElement) {
                        throw SyntaxErrorException::pseudoElementFound($argumentPseudoElement, 'inside ::not()');
                    }

                    if (!$next->isDelimiter(array(')'))) {
                        throw SyntaxErrorException::unexpectedToken('")"', $next);
                    }

                    $result = new Node\NegationNode($result, $argument);
                } else {
                    $arguments = array();
                    $next = null;

                    while (true) {
                        $stream->skipWhitespace();
                        $next = $stream->getNext();

                        if ($next->isIdentifier()
                            || $next->isString()
                            || $next->isNumber()
                            || $next->isDelimiter(array('+', '-'))
                        ) {
                            $arguments[] = $next;
                        } elseif ($next->isDelimiter(array(')'))) {
                            break;
                        } else {
                            throw SyntaxErrorException::unexpectedToken('an argument', $next);
                        }
                    }

                    if (empty($arguments)) {
                        throw SyntaxErrorException::unexpectedToken('at least one argument', $next);
                    }

                    $result = new Node\FunctionNode($result, $identifier, $arguments);
                }
            } else {
                throw SyntaxErrorException::unexpectedToken('selector', $peek);
            }
        }

        if (count($stream->getUsed()) === $selectorStart) {
            throw SyntaxErrorException::unexpectedToken('selector', $stream->getPeek());
        }

        return array($result, $pseudoElement);
    }

    /**
     * Parses next element node.
     *
     * @param TokenStream $stream
     *
     * @return Node\ElementNode
     */
    private function parseElementNode(TokenStream $stream)
    {
        $peek = $stream->getPeek();

        if ($peek->isIdentifier() || $peek->isDelimiter(array('*'))) {
            if ($peek->isIdentifier()) {
                $namespace = $stream->getNext()->getValue();
            } else {
                $stream->getNext();
                $namespace = null;
            }

            if ($stream->getPeek()->isDelimiter(array('|'))) {
                $stream->getNext();
                $element = $stream->getNextIdentifierOrStar();
            } else {
                $element = $namespace;
                $namespace = null;
            }
        } else {
            $element = $namespace = null;
        }

        return new Node\ElementNode($namespace, $element);
    }

    /**
     * Parses next attribute node.
     *
     * @param Node\NodeInterface $selector
     * @param TokenStream        $stream
     *
     * @throws SyntaxErrorException
     *
     * @return Node\AttributeNode
     */
    private function parseAttributeNode(Node\NodeInterface $selector, TokenStream $stream)
    {
        $stream->skipWhitespace();
        $attribute = $stream->getNextIdentifierOrStar();

        if (null === $attribute && !$stream->getPeek()->isDelimiter(array('|'))) {
            throw SyntaxErrorException::unexpectedToken('"|"', $stream->getPeek());
        }

        if ($stream->getPeek()->isDelimiter(array('|'))) {
            $stream->getNext();

            if ($stream->getPeek()->isDelimiter(array('='))) {
                $namespace = null;
                $stream->getNext();
                $operator = '|=';
            } else {
                $namespace = $attribute;
                $attribute = $stream->getNextIdentifier();
                $operator = null;
            }
        } else {
            $namespace = $operator = null;
        }

        if (null === $operator) {
            $stream->skipWhitespace();
            $next = $stream->getNext();

            if ($next->isDelimiter(array(']'))) {
                return new Node\AttributeNode($selector, $namespace, $attribute, 'exists', null);
            } elseif ($next->isDelimiter(array('='))) {
                $operator = '=';
            } elseif ($next->isDelimiter(array('^', '$', '*', '~', '|', '!'))
                && $stream->getPeek()->isDelimiter(array('='))
            ) {
                $operator = $next->getValue().'=';
                $stream->getNext();
            } else {
                throw SyntaxErrorException::unexpectedToken('operator', $next);
            }
        }

        $stream->skipWhitespace();
        $value = $stream->getNext();

        if ($value->isNumber()) {
            // if the value is a number, it's casted into a string
            $value = new Token(Token::TYPE_STRING, (string) $value->getValue(), $value->getPosition());
        }

        if (!($value->isIdentifier() || $value->isString())) {
            throw SyntaxErrorException::unexpectedToken('string or identifier', $value);
        }

        $stream->skipWhitespace();
        $next = $stream->getNext();

        if (!$next->isDelimiter(array(']'))) {
            throw SyntaxErrorException::unexpectedToken('"]"', $next);
        }

        return new Node\AttributeNode($selector, $namespace, $attribute, $operator, $value->getValue());
    }
}
