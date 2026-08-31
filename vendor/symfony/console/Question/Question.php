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

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Represents a Question.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Question
{
    private ?int $attempts = null;
    private bool $hidden = false;
    private bool $hiddenFallback = true;
    /**
     * @var (\Closure(string):string[])|null
     */
    private ?\Closure $autocompleterCallback = null;
    /**
     * @var (\Closure(mixed):mixed)|null
     */
    private ?\Closure $validator = null;
    /**
     * @var (\Closure(mixed):mixed)|null
     */
    private ?\Closure $normalizer = null;
    private bool $trimmable = true;
    private bool $multiline = false;
    private ?int $timeout = null;

    /**
     * @param string                     $question The question to ask to the user
     * @param string|bool|int|float|null $default  The default answer to return if the user enters nothing
     */
    public function __construct(
        private string $question,
        private string|bool|int|float|null $default = null,
    ) {
    }

    /**
     * Returns the question.
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * Returns the default answer.
     */
    public function getDefault(): string|bool|int|float|null
    {
        return $this->default;
    }

    /**
     * Returns whether the user response accepts newline characters.
     */
    public function isMultiline(): bool
    {
        return $this->multiline;
    }

    /**
     * Sets whether the user response should accept newline characters.
     *
     * @return $this
     */
    public function setMultiline(bool $multiline): static
    {
        $this->multiline = $multiline;

        return $this;
    }

    /**
     * Returns the timeout in seconds.
     */
    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * Sets the maximum time the user has to answer the question.
     * If the user does not answer within this time, an exception will be thrown.
     *
     * @return $this
     */
    public function setTimeout(?int $seconds): static
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Returns whether the user response must be hidden.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Sets whether the user response must be hidden or not.
     *
     * @return $this
     *
     * @throws LogicException In case the autocompleter is also used
     */
    public function setHidden(bool $hidden): static
    {
        if ($this->autocompleterCallback) {
            throw new LogicException('A hidden question cannot use the autocompleter.');
        }

        $this->hidden = $hidden;

        return $this;
    }

    /**
     * In case the response cannot be hidden, whether to fallback on non-hidden question or not.
     */
    public function isHiddenFallback(): bool
    {
        return $this->hiddenFallback;
    }

    /**
     * Sets whether to fallback on non-hidden question if the response cannot be hidden.
     *
     * @return $this
     */
    public function setHiddenFallback(bool $fallback): static
    {
        $this->hiddenFallback = $fallback;

        return $this;
    }

    /**
     * Gets values for the autocompleter.
     */
    public function getAutocompleterValues(): ?iterable
    {
        $callback = $this->getAutocompleterCallback();

        return $callback ? $callback('') : null;
    }

    /**
     * Sets values for the autocompleter.
     *
     * @return $this
     *
     * @throws LogicException
     */
    public function setAutocompleterValues(?iterable $values): static
    {
        if (\is_array($values)) {
            $values = $this->isAssoc($values) ? array_merge(array_keys($values), array_values($values)) : array_values($values);

            $callback = static fn () => $values;
        } elseif ($values instanceof \Traversable) {
            $callback = static function () use ($values) {
                static $valueCache;

                return $valueCache ??= iterator_to_array($values, false);
            };
        } else {
            $callback = null;
        }

        return $this->setAutocompleterCallback($callback);
    }

    /**
     * Gets the callback function used for the autocompleter.
     *
     * @return (callable(string):string[])|null
     */
    public function getAutocompleterCallback(): ?callable
    {
        return $this->autocompleterCallback;
    }

    /**
     * Sets the callback function used for the autocompleter.
     *
     * The callback is passed the user input as argument and should return an iterable of corresponding suggestions.
     *
     * @param (callable(string):string[])|null $callback
     *
     * @return $this
     */
    public function setAutocompleterCallback(?callable $callback): static
    {
        if ($this->hidden && null !== $callback) {
            throw new LogicException('A hidden question cannot use the autocompleter.');
        }

        $this->autocompleterCallback = null === $callback ? null : $callback(...);

        return $this;
    }

    /**
     * Sets a validator for the question.
     *
     * @param (callable(mixed):mixed)|null $validator
     *
     * @return $this
     */
    public function setValidator(?callable $validator): static
    {
        $this->validator = null === $validator ? null : $validator(...);

        return $this;
    }

    /**
     * Gets the validator for the question.
     *
     * @return (callable(mixed):mixed)|null
     */
    public function getValidator(): ?callable
    {
        return $this->validator;
    }

    /**
     * Sets the maximum number of attempts.
     *
     * Null means an unlimited number of attempts.
     *
     * @return $this
     *
     * @throws InvalidArgumentException in case the number of attempts is invalid
     */
    public function setMaxAttempts(?int $attempts): static
    {
        if (null !== $attempts && $attempts < 1) {
            throw new InvalidArgumentException('Maximum number of attempts must be a positive value.');
        }

        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Gets the maximum number of attempts.
     *
     * Null means an unlimited number of attempts.
     */
    public function getMaxAttempts(): ?int
    {
        return $this->attempts;
    }

    /**
     * Sets a normalizer for the response.
     *
     * @param callable(mixed):mixed $normalizer
     *
     * @return $this
     */
    public function setNormalizer(callable $normalizer): static
    {
        $this->normalizer = $normalizer(...);

        return $this;
    }

    /**
     * Gets the normalizer for the response.
     *
     * @return (callable(mixed):mixed)|null
     */
    public function getNormalizer(): ?callable
    {
        return $this->normalizer;
    }

    protected function isAssoc(array $array): bool
    {
        return (bool) \count(array_filter(array_keys($array), 'is_string'));
    }

    public function isTrimmable(): bool
    {
        return $this->trimmable;
    }

    /**
     * @return $this
     */
    public function setTrimmable(bool $trimmable): static
    {
        $this->trimmable = $trimmable;

        return $this;
    }
}
