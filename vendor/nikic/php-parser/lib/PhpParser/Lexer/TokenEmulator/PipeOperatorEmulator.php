<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Lexer\TokenEmulator\TokenEmulator;
use PhpParser\PhpVersion;
use PhpParser\Token;

class PipeOperatorEmulator extends TokenEmulator {
    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 5);
    }

    public function isEmulationNeeded(string $code): bool {
        return \strpos($code, '|>') !== false;
    }

    public function emulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->text === '|' && isset($tokens[$i + 1]) && $tokens[$i + 1]->text === '>') {
                array_splice($tokens, $i, 2, [
                    new Token(\T_PIPE, '|>', $token->line, $token->pos),
                ]);
                $c--;
            }
        }
        return $tokens;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->id === \T_PIPE) {
                array_splice($tokens, $i, 1, [
                    new Token(\ord('|'), '|', $token->line, $token->pos),
                    new Token(\ord('>'), '>', $token->line, $token->pos + 1),
                ]);
                $i++;
                $c++;
            }
        }
        return $tokens;
    }
}
