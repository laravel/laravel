<?php declare(strict_types=1);

namespace PhpParser\Node\Expr\Cast;

use PhpParser\Node\Expr\Cast;

class Double extends Cast {
    // For use in "kind" attribute
    public const KIND_DOUBLE = 1; // "double" syntax
    public const KIND_FLOAT = 2;  // "float" syntax
    public const KIND_REAL = 3; // "real" syntax

    public function getType(): string {
        return 'Expr_Cast_Double';
    }
}
