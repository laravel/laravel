<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Namespace_ extends Node\Stmt {
    /* For use in the "kind" attribute */
    public const KIND_SEMICOLON = 1;
    public const KIND_BRACED = 2;

    /** @var null|Node\Name Name */
    public ?Node\Name $name;
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs a namespace node.
     *
     * @param null|Node\Name $name Name
     * @param null|Node\Stmt[] $stmts Statements
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(?Node\Name $name = null, ?array $stmts = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = $name;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames(): array {
        return ['name', 'stmts'];
    }

    public function getType(): string {
        return 'Stmt_Namespace';
    }
}
