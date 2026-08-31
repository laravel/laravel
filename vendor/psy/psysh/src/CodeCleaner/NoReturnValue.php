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

use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified as FullyQualifiedName;

/**
 * A class used internally by CodeCleaner to represent input, such as
 * non-expression statements, with no return value.
 *
 * Note that user code returning an instance of this class will act like it
 * has no return value, so you prolly shouldn't do that.
 */
class NoReturnValue
{
    /**
     * Get PhpParser AST expression for creating a new NoReturnValue.
     */
    public static function create(): New_
    {
        return new New_(new FullyQualifiedName(self::class));
    }
}
