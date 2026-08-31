<?php

namespace Egulias\EmailValidator\Parser;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\ValidEmail;
use Egulias\EmailValidator\Result\InvalidEmail;
use Egulias\EmailValidator\Warning\CFWSWithFWS;
use Egulias\EmailValidator\Warning\QuotedString;
use Egulias\EmailValidator\Result\Reason\ExpectingATEXT;
use Egulias\EmailValidator\Result\Reason\UnclosedQuotedString;
use Egulias\EmailValidator\Result\Result;

class DoubleQuote extends PartParser
{
    public function parse(): Result
    {

        $validQuotedString = $this->checkDQUOTE();
        if ($validQuotedString->isInvalid()) {
            return $validQuotedString;
        }

        $special = [
            EmailLexer::S_CR => true,
            EmailLexer::S_HTAB => true,
            EmailLexer::S_LF => true
        ];

        $invalid = [
            EmailLexer::C_NUL => true,
            EmailLexer::S_HTAB => true,
            EmailLexer::S_CR => true,
            EmailLexer::S_LF => true
        ];

        $setSpecialsWarning = true;

        $this->lexer->moveNext();

        while (!$this->lexer->current->isA(EmailLexer::S_DQUOTE) && !$this->lexer->current->isA(EmailLexer::S_EMPTY)) {
            if (isset($special[$this->lexer->current->type]) && $setSpecialsWarning) {
                $this->warnings[CFWSWithFWS::CODE] = new CFWSWithFWS();
                $setSpecialsWarning = false;
            }
            if ($this->lexer->current->isA(EmailLexer::S_BACKSLASH) && $this->lexer->isNextToken(EmailLexer::S_DQUOTE)) {
                $this->lexer->moveNext();
            }

            $this->lexer->moveNext();

            if (!$this->escaped() && isset($invalid[$this->lexer->current->type])) {
                return new InvalidEmail(new ExpectingATEXT("Expecting ATEXT between DQUOTE"), $this->lexer->current->value);
            }
        }

        $prev = $this->lexer->getPrevious();

        if ($prev->isA(EmailLexer::S_BACKSLASH)) {
            $validQuotedString = $this->checkDQUOTE();
            if ($validQuotedString->isInvalid()) {
                return $validQuotedString;
            }
        }

        if (!$this->lexer->isNextToken(EmailLexer::S_AT) && !$prev->isA(EmailLexer::S_BACKSLASH)) {
            return new InvalidEmail(new ExpectingATEXT("Expecting ATEXT between DQUOTE"), $this->lexer->current->value);
        }

        return new ValidEmail();
    }

    protected function checkDQUOTE(): Result
    {
        $previous = $this->lexer->getPrevious();

        if ($this->lexer->isNextToken(EmailLexer::GENERIC) && $previous->isA(EmailLexer::GENERIC)) {
            $description = 'https://tools.ietf.org/html/rfc5322#section-3.2.4 - quoted string should be a unit';
            return new InvalidEmail(new ExpectingATEXT($description), $this->lexer->current->value);
        }

        try {
            $this->lexer->find(EmailLexer::S_DQUOTE);
        } catch (\Exception $e) {
            return new InvalidEmail(new UnclosedQuotedString(), $this->lexer->current->value);
        }
        $this->warnings[QuotedString::CODE] = new QuotedString($previous->value, $this->lexer->current->value);

        return new ValidEmail();
    }
}
