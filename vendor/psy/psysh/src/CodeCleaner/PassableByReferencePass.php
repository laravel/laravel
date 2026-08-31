<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeCleaner;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\VariadicPlaceholder;
use Psy\Exception\FatalErrorException;

/**
 * Validate that only variables (and variable-like things) are passed by reference.
 */
class PassableByReferencePass extends CodeCleanerPass
{
    const EXCEPTION_MESSAGE = 'Only variables can be passed by reference';

    /**
     * @throws FatalErrorException if non-variables are passed by reference
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        // @todo support MethodCall and StaticCall as well.
        if ($node instanceof FuncCall) {
            // if function name is an expression or a variable, give it a pass for now.
            if ($node->name instanceof Expr || $node->name instanceof Variable) {
                return null;
            }

            $name = (string) $node->name;

            if ($name === 'array_multisort') {
                return $this->validateArrayMultisort($node);
            }

            try {
                $refl = new \ReflectionFunction($name);
            } catch (\ReflectionException $e) {
                // Well, we gave it a shot!
                return null;
            }

            $args = [];
            foreach ($node->args as $position => $arg) {
                if ($arg instanceof VariadicPlaceholder) {
                    continue;
                }

                // Named arguments were added in php-parser 4.1, so we need to check if the property exists
                $key = (\property_exists($arg, 'name') && $arg->name !== null) ? $arg->name->name : $position;
                $args[$key] = $arg;
            }

            foreach ($refl->getParameters() as $key => $param) {
                if (\array_key_exists($key, $args) || \array_key_exists($param->name, $args)) {
                    $arg = $args[$param->name] ?? $args[$key];
                    if ($param->isPassedByReference() && !$this->isPassableByReference($arg)) {
                        throw new FatalErrorException(self::EXCEPTION_MESSAGE, 0, \E_ERROR, null, $node->getStartLine());
                    }
                }
            }
        }

        return null;
    }

    private function isPassableByReference(Node $arg): bool
    {
        if (!\property_exists($arg, 'value')) {
            return false;
        }

        // Unpacked arrays can be passed by reference
        if ($arg->value instanceof Array_) {
            return \property_exists($arg, 'unpack') && $arg->unpack;
        }

        // FuncCall, MethodCall and StaticCall are all PHP _warnings_ not fatal errors, so we'll let
        // PHP handle those ones :)
        return $arg->value instanceof ClassConstFetch ||
            $arg->value instanceof PropertyFetch ||
            $arg->value instanceof Variable ||
            $arg->value instanceof FuncCall ||
            $arg->value instanceof MethodCall ||
            $arg->value instanceof StaticCall ||
            $arg->value instanceof ArrayDimFetch;
    }

    /**
     * Because array_multisort has a problematic signature...
     *
     * The argument order is all sorts of wonky, and whether something is passed
     * by reference or not depends on the values of the two arguments before it.
     * We'll do a good faith attempt at validating this, but err on the side of
     * permissive.
     *
     * This is why you don't design languages where core code and extensions can
     * implement APIs that wouldn't be possible in userland code.
     *
     * @throws FatalErrorException for clearly invalid arguments
     *
     * @param Node $node
     */
    private function validateArrayMultisort(Node $node)
    {
        $nonPassable = 2; // start with 2 because the first one has to be passable by reference
        foreach ($node->args as $arg) {
            if ($this->isPassableByReference($arg)) {
                $nonPassable = 0;
            } elseif (++$nonPassable > 2) {
                // There can be *at most* two non-passable-by-reference args in a row. This is about
                // as close as we can get to validating the arguments for this function :-/
                throw new FatalErrorException(self::EXCEPTION_MESSAGE, 0, \E_ERROR, null, $node->getStartLine());
            }
        }
    }
}
