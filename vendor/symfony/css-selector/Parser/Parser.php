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
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/scrapy/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class Parser implements ParserInterface
{
    private Tokenizer $tokenizer;

    public function __construct(?Tokenizer $tokenizer = null)
    {
        $this->tokenizer = $tokenizer ?? new Tokenizer();
    }

    public function parse(string $source): array
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
     */
    public static function parseSeries(array $tokens): array
    {
        foreach ($tokens as $token) {
            if ($token->isString()) {
                throw SyntaxErrorException::stringAsFunctionArgument();
            }
        }

        $joined = trim(implode('', array_map(static fn (Token $token) => $token->getValue(), $tokens)));

        $int = static function ($string) {
            if (!is_numeric($string)) {
                throw SyntaxErrorException::stringAsFunctionArgument();
            }

            return (int) $string;
        };

        switch (true) {
            case 'odd' === $joined:
                return [2, 1];
            case 'even' === $joined:
                return [2, 0];
            case 'n' === $joined:
                return [1, 0];
            case !str_contains($joined, 'n'):
                return [0, $int($joined)];
        }

        $split = explode('n', $joined);
        $first = $split[0] ?? null;

        return [
            $first ? ('-' === $first || '+' === $first ? $int($first.'1') : $int($first)) : 1,
            isset($split[1]) && $split[1] ? $int($split[1]) : 0,
        ];
    }

    private function parseSelectorList(TokenStream $stream, bool $isArgument = false): array
    {
        $stream->skipWhitespace();
        $selectors = [];

        while (true) {
            if ($isArgument && $stream->getPeek()->isDelimiter([')'])) {
                break;
            }

            $selectors[] = $this->parserSelectorNode($stream, $isArgument);

            if ($stream->getPeek()->isDelimiter([','])) {
                $stream->getNext();
                $stream->skipWhitespace();
            } else {
                break;
            }
        }

        return $selectors;
    }

    private function parserSelectorNode(TokenStream $stream, bool $isArgument = false): Node\SelectorNode
    {
        [$result, $pseudoElement] = $this->parseSimpleSelector($stream, false, $isArgument);

        while (true) {
            $stream->skipWhitespace();
            $peek = $stream->getPeek();

            if (
                $peek->isFileEnd()
                || $peek->isDelimiter([','])
                || ($isArgument && $peek->isDelimiter([')']))
            ) {
                break;
            }

            if (null !== $pseudoElement) {
                throw SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }

            if ($peek->isDelimiter(['+', '>', '~'])) {
                $combinator = $stream->getNext()->getValue();
                $stream->skipWhitespace();
            } else {
                $combinator = ' ';
            }

            [$nextSelector, $pseudoElement] = $this->parseSimpleSelector($stream, false, $isArgument);
            $result = new Node\CombinedSelectorNode($result, $combinator, $nextSelector);
        }

        return new Node\SelectorNode($result, $pseudoElement);
    }

    /**
     * Parses next simple node (hash, class, pseudo, negation).
     *
     * @throws SyntaxErrorException
     */
    private function parseSimpleSelector(TokenStream $stream, bool $insideNegation = false, bool $isArgument = false): array
    {
        $stream->skipWhitespace();

        $selectorStart = \count($stream->getUsed());
        $result = $this->parseElementNode($stream);
        $pseudoElement = null;

        while (true) {
            $peek = $stream->getPeek();
            if ($peek->isWhitespace()
                || $peek->isFileEnd()
                || $peek->isDelimiter([',', '+', '>', '~'])
                || ($isArgument && $peek->isDelimiter([')']))
            ) {
                break;
            }

            if (null !== $pseudoElement) {
                throw SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }

            if ($peek->isHash()) {
                $result = new Node\HashNode($result, $stream->getNext()->getValue());
            } elseif ($peek->isDelimiter(['.'])) {
                $stream->getNext();
                $result = new Node\ClassNode($result, $stream->getNextIdentifier());
            } elseif ($peek->isDelimiter(['['])) {
                $stream->getNext();
                $result = $this->parseAttributeNode($result, $stream);
            } elseif ($peek->isDelimiter([':'])) {
                $stream->getNext();

                if ($stream->getPeek()->isDelimiter([':'])) {
                    $stream->getNext();
                    $pseudoElement = $stream->getNextIdentifier();

                    continue;
                }

                $identifier = $stream->getNextIdentifier();
                if (\in_array(strtolower($identifier), ['first-line', 'first-letter', 'before', 'after'], true)) {
                    // Special case: CSS 2.1 pseudo-elements can have a single ':'.
                    // Any new pseudo-element must have two.
                    $pseudoElement = $identifier;

                    continue;
                }

                if (!$stream->getPeek()->isDelimiter(['('])) {
                    $result = new Node\PseudoNode($result, $identifier);
                    if ('Pseudo[Element[*]:scope]' === $result->__toString()) {
                        $used = \count($stream->getUsed());
                        if (!(2 === $used
                           || 3 === $used && $stream->getUsed()[0]->isWhiteSpace()
                           || $used >= 3 && $stream->getUsed()[$used - 3]->isDelimiter([','])
                           || $used >= 4
                                && $stream->getUsed()[$used - 3]->isWhiteSpace()
                                && $stream->getUsed()[$used - 4]->isDelimiter([','])
                        )) {
                            throw SyntaxErrorException::notAtTheStartOfASelector('scope');
                        }
                    }
                    continue;
                }

                $stream->getNext();
                $stream->skipWhitespace();

                if ('not' === strtolower($identifier)) {
                    if ($insideNegation) {
                        throw SyntaxErrorException::nestedNot();
                    }

                    [$argument, $argumentPseudoElement] = $this->parseSimpleSelector($stream, true, true);
                    $next = $stream->getNext();

                    if (null !== $argumentPseudoElement) {
                        throw SyntaxErrorException::pseudoElementFound($argumentPseudoElement, 'inside ::not()');
                    }

                    if (!$next->isDelimiter([')'])) {
                        throw SyntaxErrorException::unexpectedToken('")"', $next);
                    }

                    $result = new Node\NegationNode($result, $argument);
                } elseif ('is' === strtolower($identifier)) {
                    $selectors = $this->parseSelectorList($stream, true);

                    $next = $stream->getNext();
                    if (!$next->isDelimiter([')'])) {
                        throw SyntaxErrorException::unexpectedToken('")"', $next);
                    }

                    $result = new Node\MatchingNode($result, $selectors);
                } elseif ('where' === strtolower($identifier)) {
                    $selectors = $this->parseSelectorList($stream, true);

                    $next = $stream->getNext();
                    if (!$next->isDelimiter([')'])) {
                        throw SyntaxErrorException::unexpectedToken('")"', $next);
                    }

                    $result = new Node\SpecificityAdjustmentNode($result, $selectors);
                } else {
                    $arguments = [];
                    $next = null;

                    while (true) {
                        $stream->skipWhitespace();
                        $next = $stream->getNext();

                        if ($next->isIdentifier()
                            || $next->isString()
                            || $next->isNumber()
                            || $next->isDelimiter(['+', '-'])
                        ) {
                            $arguments[] = $next;
                        } elseif ($next->isDelimiter([')'])) {
                            break;
                        } else {
                            throw SyntaxErrorException::unexpectedToken('an argument', $next);
                        }
                    }

                    if (!$arguments) {
                        throw SyntaxErrorException::unexpectedToken('at least one argument', $next);
                    }

                    $result = new Node\FunctionNode($result, $identifier, $arguments);
                }
            } else {
                throw SyntaxErrorException::unexpectedToken('selector', $peek);
            }
        }

        if (\count($stream->getUsed()) === $selectorStart) {
            throw SyntaxErrorException::unexpectedToken('selector', $stream->getPeek());
        }

        return [$result, $pseudoElement];
    }

    private function parseElementNode(TokenStream $stream): Node\ElementNode
    {
        $peek = $stream->getPeek();

        if ($peek->isIdentifier() || $peek->isDelimiter(['*'])) {
            if ($peek->isIdentifier()) {
                $namespace = $stream->getNext()->getValue();
            } else {
                $stream->getNext();
                $namespace = null;
            }

            if ($stream->getPeek()->isDelimiter(['|'])) {
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

    private function parseAttributeNode(Node\NodeInterface $selector, TokenStream $stream): Node\AttributeNode
    {
        $stream->skipWhitespace();
        $attribute = $stream->getNextIdentifierOrStar();

        if (null === $attribute && !$stream->getPeek()->isDelimiter(['|'])) {
            throw SyntaxErrorException::unexpectedToken('"|"', $stream->getPeek());
        }

        if ($stream->getPeek()->isDelimiter(['|'])) {
            $stream->getNext();

            if ($stream->getPeek()->isDelimiter(['='])) {
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

            if ($next->isDelimiter([']'])) {
                return new Node\AttributeNode($selector, $namespace, $attribute, 'exists', null);
            } elseif ($next->isDelimiter(['='])) {
                $operator = '=';
            } elseif ($next->isDelimiter(['^', '$', '*', '~', '|', '!'])
                && $stream->getPeek()->isDelimiter(['='])
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

        if (!$next->isDelimiter([']'])) {
            throw SyntaxErrorException::unexpectedToken('"]"', $next);
        }

        return new Node\AttributeNode($selector, $namespace, $attribute, $operator, $value->getValue());
    }
}
