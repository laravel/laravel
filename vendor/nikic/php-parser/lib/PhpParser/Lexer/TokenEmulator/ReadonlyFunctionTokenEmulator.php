<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;

/*
 * In PHP 8.1, "readonly(" was special cased in the lexer in order to support functions with
 * name readonly. In PHP 8.2, this may conflict with readonly properties having a DNF type. For
 * this reason, PHP 8.2 instead treats this as T_READONLY and then handles it specially in the
 * parser. This emulator only exists to handle this special case, which is skipped by the
 * PHP 8.1 ReadonlyTokenEmulator.
 */
class ReadonlyFunctionTokenEmulator extends KeywordEmulator {
    public function getKeywordString(): string {
        return 'readonly';
    }

    public function getKeywordToken(): int {
        return \T_READONLY;
    }

    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 2);
    }

    public function reverseEmulate(string $code, array $tokens): array {
        // Don't bother
        return $tokens;
    }
}
