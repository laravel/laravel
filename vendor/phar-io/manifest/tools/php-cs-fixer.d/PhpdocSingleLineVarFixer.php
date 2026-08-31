<?php
namespace PharIo\CSFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Token;

/**
 * Main implementation taken from kubawerlos/php-cs-fixer-customere-fixers
 * Copyright (c) 2018 Kuba Werłos
 *
 * Slightly modified to work without the gazillion of composer dependencies
 *
 * Original:
 * https://github.com/kubawerlos/php-cs-fixer-custom-fixers/blob/master/src/Fixer/PhpdocSingleLineVarFixer.php
 *
 */
class PhpdocSingleLineVarFixer implements FixerInterface {

    public function getDefinition(): FixerDefinition {
        return new FixerDefinition(
            '`@var` annotation must be in single line when is the only content.',
            [new CodeSample('<?php
                    /**
                     * @var string
                     */
                ')]
        );
    }

    public function isCandidate(Tokens $tokens): bool {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function isRisky(): bool {
        return false;
    }

    public function fix(\SplFileInfo $file, Tokens $tokens): void {
        foreach($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }
            if (\stripos($token->getContent(), '@var') === false) {
                continue;
            }

            if (preg_match('#^/\*\*[\s\*]+(@var[^\r\n]+)[\s\*]*\*\/$#u', $token->getContent(), $matches) !== 1) {
                continue;
            }
            $newContent = '/** ' . \rtrim($matches[1]) . ' */';
            if ($newContent === $token->getContent()) {
                continue;
            }
            $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
        }
    }

    public function getPriority(): int {
        return 0;
    }

    public function getName(): string {
        return 'PharIo/phpdoc_single_line_var_fixer';
    }

    public function supports(\SplFileInfo $file): bool {
        return true;
    }

}
