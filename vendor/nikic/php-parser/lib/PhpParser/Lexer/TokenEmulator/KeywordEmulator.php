<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\Token;

abstract class KeywordEmulator extends TokenEmulator {
    abstract public function getKeywordString(): string;
    abstract public function getKeywordToken(): int;

    public function isEmulationNeeded(string $code): bool {
        return strpos(strtolower($code), $this->getKeywordString()) !== false;
    }

    /** @param Token[] $tokens */
    protected function isKeywordContext(array $tokens, int $pos): bool {
        $prevToken = $this->getPreviousNonIgnorableToken($tokens, $pos);
        if ($prevToken === null) {
            return false;
        }
        return $prevToken->id !== \T_OBJECT_OPERATOR
            && $prevToken->id !== \T_NULLSAFE_OBJECT_OPERATOR;
    }

    public function emulate(string $code, array $tokens): array {
        $keywordString = $this->getKeywordString();
        foreach ($tokens as $i => $token) {
            if ($token->id === T_STRING && strtolower($token->text) === $keywordString
                    && $this->isKeywordContext($tokens, $i)) {
                $token->id = $this->getKeywordToken();
            }
        }

        return $tokens;
    }

    /** @param Token[] $tokens */
    private function getPreviousNonIgnorableToken(array $tokens, int $start): ?Token {
        for ($i = $start - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            if ($token->id === T_WHITESPACE || $token->id == T_COMMENT || $token->id === T_DOC_COMMENT) {
                continue;
            }

            return $token;
        }

        return null;
    }

    public function reverseEmulate(string $code, array $tokens): array {
        $keywordToken = $this->getKeywordToken();
        foreach ($tokens as $token) {
            if ($token->id === $keywordToken) {
                $token->id = \T_STRING;
            }
        }

        return $tokens;
    }
}
