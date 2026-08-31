<?php declare(strict_types=1);

namespace PhpParser\Node\Name;

class Relative extends \PhpParser\Node\Name {
    /**
     * Checks whether the name is unqualified. (E.g. Name)
     *
     * @return bool Whether the name is unqualified
     */
    public function isUnqualified(): bool {
        return false;
    }

    /**
     * Checks whether the name is qualified. (E.g. Name\Name)
     *
     * @return bool Whether the name is qualified
     */
    public function isQualified(): bool {
        return false;
    }

    /**
     * Checks whether the name is fully qualified. (E.g. \Name)
     *
     * @return bool Whether the name is fully qualified
     */
    public function isFullyQualified(): bool {
        return false;
    }

    /**
     * Checks whether the name is explicitly relative to the current namespace. (E.g. namespace\Name)
     *
     * @return bool Whether the name is relative
     */
    public function isRelative(): bool {
        return true;
    }

    public function toCodeString(): string {
        return 'namespace\\' . $this->toString();
    }

    public function getType(): string {
        return 'Name_Relative';
    }
}
