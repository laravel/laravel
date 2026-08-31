<?php declare(strict_types=1);

namespace PhpParser\Node\Expr\BinaryOp;

use PhpParser\Node\Expr\BinaryOp;

class NotIdentical extends BinaryOp {
    public function getOperatorSigil(): string {
        return '!==';
    }

    public function getType(): string {
        return 'Expr_BinaryOp_NotIdentical';
    }
}
