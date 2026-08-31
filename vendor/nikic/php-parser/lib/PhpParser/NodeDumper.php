<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\List_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;

class NodeDumper {
    private bool $dumpComments;
    private bool $dumpPositions;
    private bool $dumpOtherAttributes;
    private ?string $code;
    private string $res;
    private string $nl;

    private const IGNORE_ATTRIBUTES = [
        'comments' => true,
        'startLine' => true,
        'endLine' => true,
        'startFilePos' => true,
        'endFilePos' => true,
        'startTokenPos' => true,
        'endTokenPos' => true,
    ];

    /**
     * Constructs a NodeDumper.
     *
     * Supported options:
     *  * bool dumpComments: Whether comments should be dumped.
     *  * bool dumpPositions: Whether line/offset information should be dumped. To dump offset
     *                        information, the code needs to be passed to dump().
     *  * bool dumpOtherAttributes: Whether non-comment, non-position attributes should be dumped.
     *
     * @param array $options Options (see description)
     */
    public function __construct(array $options = []) {
        $this->dumpComments = !empty($options['dumpComments']);
        $this->dumpPositions = !empty($options['dumpPositions']);
        $this->dumpOtherAttributes = !empty($options['dumpOtherAttributes']);
    }

    /**
     * Dumps a node or array.
     *
     * @param array|Node $node Node or array to dump
     * @param string|null $code Code corresponding to dumped AST. This only needs to be passed if
     *                          the dumpPositions option is enabled and the dumping of node offsets
     *                          is desired.
     *
     * @return string Dumped value
     */
    public function dump($node, ?string $code = null): string {
        $this->code = $code;
        $this->res = '';
        $this->nl = "\n";
        $this->dumpRecursive($node, false);
        return $this->res;
    }

    /** @param mixed $node */
    protected function dumpRecursive($node, bool $indent = true): void {
        if ($indent) {
            $this->nl .= "    ";
        }
        if ($node instanceof Node) {
            $this->res .= $node->getType();
            if ($this->dumpPositions && null !== $p = $this->dumpPosition($node)) {
                $this->res .= $p;
            }
            $this->res .= '(';

            foreach ($node->getSubNodeNames() as $key) {
                $this->res .= "$this->nl    " . $key . ': ';

                $value = $node->$key;
                if (\is_int($value)) {
                    if ('flags' === $key || 'newModifier' === $key) {
                        $this->res .= $this->dumpFlags($value);
                        continue;
                    }
                    if ('type' === $key && $node instanceof Include_) {
                        $this->res .= $this->dumpIncludeType($value);
                        continue;
                    }
                    if ('type' === $key
                            && ($node instanceof Use_ || $node instanceof UseItem || $node instanceof GroupUse)) {
                        $this->res .= $this->dumpUseType($value);
                        continue;
                    }
                }
                $this->dumpRecursive($value);
            }

            if ($this->dumpComments && $comments = $node->getComments()) {
                $this->res .= "$this->nl    comments: ";
                $this->dumpRecursive($comments);
            }

            if ($this->dumpOtherAttributes) {
                foreach ($node->getAttributes() as $key => $value) {
                    if (isset(self::IGNORE_ATTRIBUTES[$key])) {
                        continue;
                    }

                    $this->res .= "$this->nl    $key: ";
                    if (\is_int($value)) {
                        if ('kind' === $key) {
                            if ($node instanceof Int_) {
                                $this->res .= $this->dumpIntKind($value);
                                continue;
                            }
                            if ($node instanceof String_ || $node instanceof InterpolatedString) {
                                $this->res .= $this->dumpStringKind($value);
                                continue;
                            }
                            if ($node instanceof Array_) {
                                $this->res .= $this->dumpArrayKind($value);
                                continue;
                            }
                            if ($node instanceof List_) {
                                $this->res .= $this->dumpListKind($value);
                                continue;
                            }
                        }
                    }
                    $this->dumpRecursive($value);
                }
            }
            $this->res .= "$this->nl)";
        } elseif (\is_array($node)) {
            $this->res .= 'array(';
            foreach ($node as $key => $value) {
                $this->res .= "$this->nl    " . $key . ': ';
                $this->dumpRecursive($value);
            }
            $this->res .= "$this->nl)";
        } elseif ($node instanceof Comment) {
            $this->res .= \str_replace("\n", $this->nl, $node->getReformattedText());
        } elseif (\is_string($node)) {
            $this->res .= \str_replace("\n", $this->nl, $node);
        } elseif (\is_int($node) || \is_float($node)) {
            $this->res .= $node;
        } elseif (null === $node) {
            $this->res .= 'null';
        } elseif (false === $node) {
            $this->res .= 'false';
        } elseif (true === $node) {
            $this->res .= 'true';
        } else {
            throw new \InvalidArgumentException('Can only dump nodes and arrays.');
        }
        if ($indent) {
            $this->nl = \substr($this->nl, 0, -4);
        }
    }

    protected function dumpFlags(int $flags): string {
        $strs = [];
        if ($flags & Modifiers::PUBLIC) {
            $strs[] = 'PUBLIC';
        }
        if ($flags & Modifiers::PROTECTED) {
            $strs[] = 'PROTECTED';
        }
        if ($flags & Modifiers::PRIVATE) {
            $strs[] = 'PRIVATE';
        }
        if ($flags & Modifiers::ABSTRACT) {
            $strs[] = 'ABSTRACT';
        }
        if ($flags & Modifiers::STATIC) {
            $strs[] = 'STATIC';
        }
        if ($flags & Modifiers::FINAL) {
            $strs[] = 'FINAL';
        }
        if ($flags & Modifiers::READONLY) {
            $strs[] = 'READONLY';
        }
        if ($flags & Modifiers::PUBLIC_SET) {
            $strs[] = 'PUBLIC_SET';
        }
        if ($flags & Modifiers::PROTECTED_SET) {
            $strs[] = 'PROTECTED_SET';
        }
        if ($flags & Modifiers::PRIVATE_SET) {
            $strs[] = 'PRIVATE_SET';
        }

        if ($strs) {
            return implode(' | ', $strs) . ' (' . $flags . ')';
        } else {
            return (string) $flags;
        }
    }

    /** @param array<int, string> $map */
    private function dumpEnum(int $value, array $map): string {
        if (!isset($map[$value])) {
            return (string) $value;
        }
        return $map[$value] . ' (' . $value . ')';
    }

    private function dumpIncludeType(int $type): string {
        return $this->dumpEnum($type, [
            Include_::TYPE_INCLUDE      => 'TYPE_INCLUDE',
            Include_::TYPE_INCLUDE_ONCE => 'TYPE_INCLUDE_ONCE',
            Include_::TYPE_REQUIRE      => 'TYPE_REQUIRE',
            Include_::TYPE_REQUIRE_ONCE => 'TYPE_REQUIRE_ONCE',
        ]);
    }

    private function dumpUseType(int $type): string {
        return $this->dumpEnum($type, [
            Use_::TYPE_UNKNOWN  => 'TYPE_UNKNOWN',
            Use_::TYPE_NORMAL   => 'TYPE_NORMAL',
            Use_::TYPE_FUNCTION => 'TYPE_FUNCTION',
            Use_::TYPE_CONSTANT => 'TYPE_CONSTANT',
        ]);
    }

    private function dumpIntKind(int $kind): string {
        return $this->dumpEnum($kind, [
            Int_::KIND_BIN => 'KIND_BIN',
            Int_::KIND_OCT => 'KIND_OCT',
            Int_::KIND_DEC => 'KIND_DEC',
            Int_::KIND_HEX => 'KIND_HEX',
        ]);
    }

    private function dumpStringKind(int $kind): string {
        return $this->dumpEnum($kind, [
            String_::KIND_SINGLE_QUOTED => 'KIND_SINGLE_QUOTED',
            String_::KIND_DOUBLE_QUOTED => 'KIND_DOUBLE_QUOTED',
            String_::KIND_HEREDOC => 'KIND_HEREDOC',
            String_::KIND_NOWDOC => 'KIND_NOWDOC',
        ]);
    }

    private function dumpArrayKind(int $kind): string {
        return $this->dumpEnum($kind, [
            Array_::KIND_LONG => 'KIND_LONG',
            Array_::KIND_SHORT => 'KIND_SHORT',
        ]);
    }

    private function dumpListKind(int $kind): string {
        return $this->dumpEnum($kind, [
            List_::KIND_LIST => 'KIND_LIST',
            List_::KIND_ARRAY => 'KIND_ARRAY',
        ]);
    }

    /**
     * Dump node position, if possible.
     *
     * @param Node $node Node for which to dump position
     *
     * @return string|null Dump of position, or null if position information not available
     */
    protected function dumpPosition(Node $node): ?string {
        if (!$node->hasAttribute('startLine') || !$node->hasAttribute('endLine')) {
            return null;
        }

        $start = $node->getStartLine();
        $end = $node->getEndLine();
        if ($node->hasAttribute('startFilePos') && $node->hasAttribute('endFilePos')
            && null !== $this->code
        ) {
            $start .= ':' . $this->toColumn($this->code, $node->getStartFilePos());
            $end .= ':' . $this->toColumn($this->code, $node->getEndFilePos());
        }
        return "[$start - $end]";
    }

    // Copied from Error class
    private function toColumn(string $code, int $pos): int {
        if ($pos > strlen($code)) {
            throw new \RuntimeException('Invalid position information');
        }

        $lineStartPos = strrpos($code, "\n", $pos - strlen($code));
        if (false === $lineStartPos) {
            $lineStartPos = -1;
        }

        return $pos - $lineStartPos;
    }
}
