<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class HaltCompiler extends Stmt {
    /** @var string Remaining text after halt compiler statement. */
    public string $remaining;

    /**
     * Constructs a __halt_compiler node.
     *
     * @param string $remaining Remaining text after halt compiler statement.
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(string $remaining, array $attributes = []) {
        $this->attributes = $attributes;
        $this->remaining = $remaining;
    }

    public function getSubNodeNames(): array {
        return ['remaining'];
    }

    public function getType(): string {
        return 'Stmt_HaltCompiler';
    }
}
