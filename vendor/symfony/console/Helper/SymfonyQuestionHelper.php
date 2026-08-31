<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Symfony Style Guide compliant question helper.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class SymfonyQuestionHelper extends QuestionHelper
{
    protected function writePrompt(OutputInterface $output, Question $question): void
    {
        $text = OutputFormatter::escapeTrailingBackslash($question->getQuestion());
        $default = $question->getDefault();

        if ($question->isMultiline()) {
            $text .= \sprintf(' (press %s to continue)', $this->getEofShortcut($output));
        }

        switch (true) {
            case null === $default:
                $text = \sprintf(' <info>%s</info>:', $text);

                break;

            case $question instanceof ConfirmationQuestion:
                $text = \sprintf(' <info>%s (yes/no)</info> [<comment>%s</comment>]:', $text, $default ? 'yes' : 'no');

                break;

            case $question instanceof ChoiceQuestion && $question->isMultiselect():
                $choices = $question->getChoices();
                $default = explode(',', $default);

                foreach ($default as $key => $value) {
                    $default[$key] = $choices[trim($value)];
                }

                $text = \sprintf(' <info>%s</info> [<comment>%s</comment>]:', $text, implode(', ', $default));

                break;

            case $question instanceof ChoiceQuestion:
                $choices = $question->getChoices();
                $text = \sprintf(' <info>%s</info> [<comment>%s</comment>]:', $text, $choices[$default] ?? $default);

                break;

            default:
                $text = \sprintf(' <info>%s</info> [<comment>%s</comment>]:', $text, OutputFormatter::escape($default));
        }

        $output->writeln($text);

        $prompt = ' > ';

        if ($question instanceof ChoiceQuestion) {
            $output->writeln($this->formatChoiceQuestionChoices($question, 'comment'));

            $prompt = $question->getPrompt();
        }

        $output->write($prompt);
    }

    protected function writeError(OutputInterface $output, \Exception $error): void
    {
        if ($output instanceof SymfonyStyle) {
            $output->newLine();
            $output->error($error->getMessage());

            return;
        }

        parent::writeError($output, $error);
    }

    private function getEofShortcut(OutputInterface $output): string
    {
        if ('\\' === \DIRECTORY_SEPARATOR && !$output->isDecorated()) {
            return '<comment>Ctrl+Z</comment> then <comment>Enter</comment>';
        }

        return '<comment>Ctrl+D</comment>';
    }
}
