<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\UseItem;

require __DIR__ . '/../UseItem.php';

if (false) {
    /**
     * For classmap-authoritative support.
     *
     * @deprecated use \PhpParser\Node\UseItem instead.
     */
    class UseUse extends UseItem {
    }
}
