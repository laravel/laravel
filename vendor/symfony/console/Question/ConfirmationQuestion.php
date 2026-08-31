<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Question;

/**
 * Represents a yes/no question.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ConfirmationQuestion extends Question
{
    /**
     * @param string $question        The question to ask to the user
     * @param bool   $default         The default answer to return, true or false
     * @param string $trueAnswerRegex A regex to match the "yes" answer
     */
    public function __construct(
        string $question,
        bool $default = true,
        private string $trueAnswerRegex = '/^y/i',
    ) {
        parent::__construct($question, $default);

        $this->setNormalizer($this->getDefaultNormalizer());
    }

    /**
     * Returns the default answer normalizer.
     */
    private function getDefaultNormalizer(): callable
    {
        $default = $this->getDefault();
        $regex = $this->trueAnswerRegex;

        return static function ($answer) use ($default, $regex) {
            if (\is_bool($answer)) {
                return $answer;
            }

            $answerIsTrue = (bool) preg_match($regex, $answer);
            if (false === $default) {
                return $answer && $answerIsTrue;
            }

            return '' === $answer || $answerIsTrue;
        };
    }
}
