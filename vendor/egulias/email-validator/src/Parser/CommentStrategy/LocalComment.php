<?php

namespace Egulias\EmailValidator\Parser\CommentStrategy;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\Result;
use Egulias\EmailValidator\Result\ValidEmail;
use Egulias\EmailValidator\Warning\CFWSNearAt;
use Egulias\EmailValidator\Result\InvalidEmail;
use Egulias\EmailValidator\Result\Reason\ExpectingATEXT;
use Egulias\EmailValidator\Warning\Warning;

class LocalComment implements CommentStrategy
{
    /**
     * @var array<int, Warning>
     */
    private $warnings = [];

    public function exitCondition(EmailLexer $lexer, int $openedParenthesis): bool
    {
        return !$lexer->isNextToken(EmailLexer::S_AT);
    }

    public function endOfLoopValidations(EmailLexer $lexer): Result
    {
        if (!$lexer->isNextToken(EmailLexer::S_AT)) {
            return new InvalidEmail(new ExpectingATEXT('ATEX is not expected after closing comments'), $lexer->current->value);
        }
        $this->warnings[CFWSNearAt::CODE] = new CFWSNearAt();
        return new ValidEmail();
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
