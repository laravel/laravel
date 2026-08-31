<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;
use PhpParser\Token;

final class AsymmetricVisibilityTokenEmulator extends TokenEmulator {
    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 4);
    }
    public function isEmulationNeeded(string $code): bool {
        $code = strtolower($code);
        return strpos($code, 'public(set)') !== false ||
            strpos($code, 'protected(set)') !== false ||
            strpos($code, 'private(set)') !== false;
    }

    public function emulate(string $code, array $tokens): array {
        $map = [
            \T_PUBLIC => \T_PUBLIC_SET,
            \T_PROTECTED => \T_PROTECTED_SET,
            \T_PRIVATE => \T_PRIVATE_SET,
        ];
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if (isset($map[$token->id]) && $i + 3 < $c && $tokens[$i + 1]->text === '(' &&
                $tokens[$i + 2]->id === \T_STRING && \strtolower($tokens[$i + 2]->text) === 'set' &&
                $tokens[$i + 3]->text === ')' &&
                $this->isKeywordContext($tokens, $i)
            ) {
                array_splice($tokens, $i, 4, [
                    new Token(
                        $map[$token->id], $token->text . '(' . $tokens[$i + 2]->text . ')',
                        $token->line, $token->pos),
                ]);
                $c -= 3;
            }
        }

        return $tokens;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        $reverseMap = [
            \T_PUBLIC_SET => \T_PUBLIC,
            \T_PROTECTED_SET => \T_PROTECTED,
            \T_PRIVATE_SET => \T_PRIVATE,
        ];
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if (isset($reverseMap[$token->id]) &&
                \preg_match('/(public|protected|private)\((set)\)/i', $token->text, $matches)
            ) {
                [, $modifier, $set] = $matches;
                $modifierLen = \strlen($modifier);
                array_splice($tokens, $i, 1, [
                    new Token($reverseMap[$token->id], $modifier, $token->line, $token->pos),
                    new Token(\ord('('), '(', $token->line, $token->pos + $modifierLen),
                    new Token(\T_STRING, $set, $token->line, $token->pos + $modifierLen + 1),
                    new Token(\ord(')'), ')', $token->line, $token->pos + $modifierLen + 4),
                ]);
                $i += 3;
                $c += 3;
            }
        }

        return $tokens;
    }

    /** @param Token[] $tokens */
    protected function isKeywordContext(array $tokens, int $pos): bool {
        $prevToken = $this->getPreviousNonSpaceToken($tokens, $pos);
        if ($prevToken === null) {
            return false;
        }
        return $prevToken->id !== \T_OBJECT_OPERATOR
            && $prevToken->id !== \T_NULLSAFE_OBJECT_OPERATOR;
    }

    /** @param Token[] $tokens */
    private function getPreviousNonSpaceToken(array $tokens, int $start): ?Token {
        for ($i = $start - 1; $i >= 0; --$i) {
            if ($tokens[$i]->id === T_WHITESPACE) {
                continue;
            }

            return $tokens[$i];
        }

        return null;
    }
}
