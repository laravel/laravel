<?php declare(strict_types = 1);
namespace TheSeer\Tokenizer;

class Tokenizer {

    /**
     * Token Map for "non-tokens"
     *
     * @var array
     */
    private const MAP = [
        '(' => 'T_OPEN_BRACKET',
        ')' => 'T_CLOSE_BRACKET',
        '[' => 'T_OPEN_SQUARE',
        ']' => 'T_CLOSE_SQUARE',
        '{' => 'T_OPEN_CURLY',
        '}' => 'T_CLOSE_CURLY',
        ';' => 'T_SEMICOLON',
        '.' => 'T_DOT',
        ',' => 'T_COMMA',
        '=' => 'T_EQUAL',
        '<' => 'T_LT',
        '>' => 'T_GT',
        '+' => 'T_PLUS',
        '-' => 'T_MINUS',
        '*' => 'T_MULT',
        '/' => 'T_DIV',
        '?' => 'T_QUESTION_MARK',
        '!' => 'T_EXCLAMATION_MARK',
        ':' => 'T_COLON',
        '"' => 'T_DOUBLE_QUOTES',
        '@' => 'T_AT',
        '&' => 'T_AMPERSAND',
        '%' => 'T_PERCENT',
        '|' => 'T_PIPE',
        '$' => 'T_DOLLAR',
        '^' => 'T_CARET',
        '~' => 'T_TILDE',
        '`' => 'T_BACKTICK'
    ];

    public function parse(string $source): TokenCollection {
        $result = new TokenCollection();

        if ($source === '') {
            return $result;
        }

        $tokens = \token_get_all($source);

        $lastToken = new Token(
            $tokens[0][2],
            'Placeholder',
            ''
        );

        foreach ($tokens as $pos => $tok) {
            if (\is_string($tok)) {
                $token = new Token(
                    $lastToken->getLine(),
                    self::MAP[$tok],
                    $tok
                );
                $result->addToken($token);
                $lastToken = $token;

                continue;
            }

            $line   = $tok[2];
            $values = \preg_split('/\R+/Uu', $tok[1]);

            if (!$values) {
                $result->addToken(
                    new Token(
                        $line,
                        \token_name($tok[0]),
                        '{binary data}'
                    )
                );

                continue;
            }

            foreach ($values as $v) {
                $token = new Token(
                    $line,
                    \token_name($tok[0]),
                    $v
                );
                $lastToken = $token;
                $line++;

                if ($v === '') {
                    continue;
                }

                $result->addToken($token);
            }
        }

        return $this->fillBlanks($result, $lastToken->getLine());
    }

    private function fillBlanks(TokenCollection $tokens, int $maxLine): TokenCollection {
        $prev = new Token(
            0,
            'Placeholder',
            ''
        );

        $final = new TokenCollection();
        $prevLine = $prev->getLine();

        foreach ($tokens as $token) {
            $line = $token->getLine();
            $gap = $line - $prevLine;

            while ($gap > 1) {
                $linebreak = new Token(
                    $prevLine + 1,
                    'T_WHITESPACE',
                    ''
                );
                $final->addToken($linebreak);
                $prevLine = $linebreak->getLine();
                $gap--;
            }

            $final->addToken($token);
            $prevLine = $line;
        }

        $gap = $maxLine - $prevLine;

        while ($gap > 0) {
            $linebreak = new Token(
                $prevLine + 1,
                'T_WHITESPACE',
                ''
            );
            $final->addToken($linebreak);
            $prevLine = $linebreak->getLine();
            $gap--;
        }

        return $final;
    }
}