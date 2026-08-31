<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;
use PhpParser\Token;

class ExplicitOctalEmulator extends TokenEmulator {
    public function getPhpVersion(): PhpVersion {
        return PhpVersion::fromComponents(8, 1);
    }

    public function isEmulationNeeded(string $code): bool {
        return strpos($code, '0o') !== false || strpos($code, '0O') !== false;
    }

    public function emulate(string $code, array $tokens): array {
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            $token = $tokens[$i];
            if ($token->id == \T_LNUMBER && $token->text === '0' &&
                isset($tokens[$i + 1]) && $tokens[$i + 1]->id == \T_STRING &&
                preg_match('/[oO][0-7]+(?:_[0-7]+)*/', $tokens[$i + 1]->text)
            ) {
                $tokenKind = $this->resolveIntegerOrFloatToken($tokens[$i + 1]->text);
                array_splice($tokens, $i, 2, [
                    new Token($tokenKind, '0' . $tokens[$i + 1]->text, $token->line, $token->pos),
                ]);
                $c--;
            }
        }
        return $tokens;
    }

    private function resolveIntegerOrFloatToken(string $str): int {
        $str = substr($str, 1);
        $str = str_replace('_', '', $str);
        $num = octdec($str);
        return is_float($num) ? \T_DNUMBER : \T_LNUMBER;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        // Explicit octals were not legal code previously, don't bother.
        return $tokens;
    }
}
