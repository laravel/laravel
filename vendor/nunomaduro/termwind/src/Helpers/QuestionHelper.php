<?php

declare(strict_types=1);

namespace Termwind\Helpers;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @internal
 */
final class QuestionHelper extends SymfonyQuestionHelper
{
    /**
     * {@inheritdoc}
     */
    protected function writePrompt(OutputInterface $output, Question $question): void
    {
        $text = OutputFormatter::escapeTrailingBackslash($question->getQuestion());
        $output->write($text);
    }
}
