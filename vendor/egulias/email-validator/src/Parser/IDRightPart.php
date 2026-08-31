<?php

namespace Egulias\EmailValidator\Parser;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\Result;
use Egulias\EmailValidator\Result\ValidEmail;
use Egulias\EmailValidator\Result\InvalidEmail;
use Egulias\EmailValidator\Result\Reason\ExpectingATEXT;

class IDRightPart extends DomainPart
{
    protected function validateTokens(bool $hasComments): Result
    {
        $invalidDomainTokens = [
            EmailLexer::S_DQUOTE => true,
            EmailLexer::S_SQUOTE => true,
            EmailLexer::S_BACKTICK => true,
            EmailLexer::S_SEMICOLON => true,
            EmailLexer::S_GREATERTHAN => true,
            EmailLexer::S_LOWERTHAN => true,
        ];

        if (isset($invalidDomainTokens[$this->lexer->current->type])) {
            return new InvalidEmail(new ExpectingATEXT('Invalid token in domain: ' . $this->lexer->current->value), $this->lexer->current->value);
        }
        return new ValidEmail();
    }
}
