<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;
use PhpParser\Token;

final class NullsafeTokenEmulator extends TokenEmulator {
    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 0);
    }

    public function isEmulationNeeded(string $code): bool {
        return strpos($code, '?->') !== false;
    }

    public function emulate(string $code, array $tokens): array {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->text === '?' && isset($tokens[$i + 1]) && $tokens[$i + 1]->id === \T_OBJECT_OPERATOR) {
                array_splice($tokens, $i, 2, [
                    new Token(\T_NULLSAFE_OBJECT_OPERATOR, '?->', $token->line, $token->pos),
                ]);
                $c--;
                continue;
            }

            // Handle ?-> inside encapsed string.
            if ($token->id === \T_ENCAPSED_AND_WHITESPACE && isset($tokens[$i - 1])
                && $tokens[$i - 1]->id === \T_VARIABLE
                && preg_match('/^\?->([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)/', $token->text, $matches)
            ) {
                $replacement = [
                    new Token(\T_NULLSAFE_OBJECT_OPERATOR, '?->', $token->line, $token->pos),
                    new Token(\T_STRING, $matches[1], $token->line, $token->pos + 3),
                ];
                $matchLen = \strlen($matches[0]);
                if ($matchLen !== \strlen($token->text)) {
                    $replacement[] = new Token(
                        \T_ENCAPSED_AND_WHITESPACE,
                        \substr($token->text, $matchLen),
                        $token->line, $token->pos + $matchLen
                    );
                }
                array_splice($tokens, $i, 1, $replacement);
                $c += \count($replacement) - 1;
                continue;
            }
        }

        return $tokens;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        // ?-> was not valid code previously, don't bother.
        return $tokens;
    }
}
