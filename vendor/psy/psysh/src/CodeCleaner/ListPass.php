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
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
// @todo Drop PhpParser\Node\Expr\ArrayItem once we drop support for PHP-Parser 4.x
use PhpParser\Node\Expr\ArrayItem as LegacyArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\List_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use Psy\Exception\ParseErrorException;

/**
 * Validate that the list assignment.
 */
class ListPass extends CodeCleanerPass
{
    /**
     * Validate use of list assignment.
     *
     * @throws ParseErrorException if the user used empty with anything but a variable
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Assign) {
            return null;
        }

        if (!$node->var instanceof Array_ && !$node->var instanceof List_) {
            return null;
        }

        // Polyfill for PHP-Parser 2.x
        $items = isset($node->var->items) ? $node->var->items : (\property_exists($node->var, 'vars') ? $node->var->vars : []);

        if ($items === [] || $items === [null]) {
            throw new ParseErrorException('Cannot use empty list', ['startLine' => $node->var->getStartLine(), 'endLine' => $node->var->getEndLine()]);
        }

        $itemFound = false;
        foreach ($items as $item) {
            if ($item === null) {
                continue;
            }

            $itemFound = true;

            if (!self::isValidArrayItem($item)) {
                $msg = 'Assignments can only happen to writable values';
                throw new ParseErrorException($msg, ['startLine' => $item->getStartLine(), 'endLine' => $item->getEndLine()]);
            }
        }

        if (!$itemFound) {
            throw new ParseErrorException('Cannot use empty list');
        }

        return null;
    }

    /**
     * Validate whether a given item in an array is valid for short assignment.
     *
     * @param Node $item
     */
    private static function isValidArrayItem(Node $item): bool
    {
        $value = ($item instanceof ArrayItem || $item instanceof LegacyArrayItem) ? $item->value : $item;

        while ($value instanceof ArrayDimFetch || $value instanceof PropertyFetch) {
            $value = $value->var;
        }

        // We just kind of give up if it's a method call. We can't tell if it's
        // valid via static analysis.
        return $value instanceof Variable || $value instanceof MethodCall || $value instanceof FuncCall;
    }
}
