<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;
use PhpParser\Token;

class VoidCastEmulator extends TokenEmulator {
    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 5);
    }

    public function isEmulationNeeded(string $code): bool {
        return (bool)\preg_match('/\([ \t]*void[ \t]*\)/i', $code);
    }

    public function emulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->text !== '(') {
                continue;
            }

            $numTokens = 1;
            $text = '(';
            $j = $i + 1;
            if ($j < $c && $tokens[$j]->id === \T_WHITESPACE && preg_match('/[ \t]+/', $tokens[$j]->text)) {
                $text .= $tokens[$j]->text;
                $numTokens++;
                $j++;
            }

            if ($j >= $c || $tokens[$j]->id !== \T_STRING || \strtolower($tokens[$j]->text) !== 'void') {
                continue;
            }

            $text .= $tokens[$j]->text;
            $numTokens++;
            $k = $j + 1;
            if ($k < $c && $tokens[$k]->id === \T_WHITESPACE && preg_match('/[ \t]+/', $tokens[$k]->text)) {
                $text .= $tokens[$k]->text;
                $numTokens++;
                $k++;
            }

            if ($k >= $c || $tokens[$k]->text !== ')') {
                continue;
            }

            $text .= ')';
            $numTokens++;
            array_splice($tokens, $i, $numTokens, [
                new Token(\T_VOID_CAST, $text, $token->line, $token->pos),
            ]);
            $c -= $numTokens - 1;
        }
        return $tokens;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->id !== \T_VOID_CAST) {
                continue;
            }

            if (!preg_match('/^\(([ \t]*)(void)([ \t]*)\)$/i', $token->text, $match)) {
                throw new \LogicException('Unexpected T_VOID_CAST contents');
            }

            $newTokens = [];
            $pos = $token->pos;

            $newTokens[] = new Token(\ord('('), '(', $token->line, $pos);
            $pos++;

            if ($match[1] !== '') {
                $newTokens[] = new Token(\T_WHITESPACE, $match[1], $token->line, $pos);
                $pos += \strlen($match[1]);
            }

            $newTokens[] = new Token(\T_STRING, $match[2], $token->line, $pos);
            $pos += \strlen($match[2]);

            if ($match[3] !== '') {
                $newTokens[] = new Token(\T_WHITESPACE, $match[3], $token->line, $pos);
                $pos += \strlen($match[3]);
            }

            $newTokens[] = new Token(\ord(')'), ')', $token->line, $pos);

            array_splice($tokens, $i, 1, $newTokens);
            $i += \count($newTokens) - 1;
            $c += \count($newTokens) - 1;
        }
        return $tokens;
    }
}
