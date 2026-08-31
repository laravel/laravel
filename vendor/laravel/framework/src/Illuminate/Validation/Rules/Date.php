<?php

namespace Illuminate\Validation\Rules;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Stringable;

class Date implements Stringable
{
    use Conditionable, Macroable;

    /**
     * The format of the date.
     */
    protected ?string $format = null;

    /**
     * The constraints for the date rule.
     */
    protected array $constraints = [];

    /**
     * Ensure the date has the given format.
     */
    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Ensure the date is before today.
     */
    public function beforeToday(): static
    {
        return $this->before('today');
    }

    /**
     * Ensure the date is after today.
     */
    public function afterToday(): static
    {
        return $this->after('today');
    }

    /**
     * Ensure the date is before or equal to today.
     */
    public function todayOrBefore(): static
    {
        return $this->beforeOrEqual('today');
    }

    /**
     * Ensure the date is after or equal to today.
     */
    public function todayOrAfter(): static
    {
        return $this->afterOrEqual('today');
    }

    /**
     * Ensure the date is in the past.
     */
    public function past(): static
    {
        return $this->before('now');
    }

    /**
     * Ensure the date is in the future.
     */
    public function future(): static
    {
        return $this->after('now');
    }

    /**
     * Ensure the date is now or in the past.
     */
    public function nowOrPast(): static
    {
        return $this->beforeOrEqual('now');
    }

    /**
     * Ensure the date is now or in the future.
     */
    public function nowOrFuture(): static
    {
        return $this->afterOrEqual('now');
    }

    /**
     * Ensure the date is before the given date or date field.
     */
    public function before(DateTimeInterface|string $date): static
    {
        return $this->addRule('before:'.$this->formatDate($date));
    }

    /**
     * Ensure the date is after the given date or date field.
     */
    public function after(DateTimeInterface|string $date): static
    {
        return $this->addRule('after:'.$this->formatDate($date));
    }

    /**
     * Ensure the date is on or before the specified date or date field.
     */
    public function beforeOrEqual(DateTimeInterface|string $date): static
    {
        return $this->addRule('before_or_equal:'.$this->formatDate($date));
    }

    /**
     * Ensure the date is on or after the given date or date field.
     */
    public function afterOrEqual(DateTimeInterface|string $date): static
    {
        return $this->addRule('after_or_equal:'.$this->formatDate($date));
    }

    /**
     * Ensure the date is between two dates or date fields.
     */
    public function between(DateTimeInterface|string $from, DateTimeInterface|string $to): static
    {
        return $this->after($from)->before($to);
    }

    /**
     * Ensure the date is between or equal to two dates or date fields.
     */
    public function betweenOrEqual(DateTimeInterface|string $from, DateTimeInterface|string $to): static
    {
        return $this->afterOrEqual($from)->beforeOrEqual($to);
    }

    /**
     * Add custom rules to the validation rules array.
     */
    protected function addRule(array|string $rules): static
    {
        $this->constraints = array_merge($this->constraints, Arr::wrap($rules));

        return $this;
    }

    /**
     * Format the date for the validation rule.
     */
    protected function formatDate(DateTimeInterface|string $date): string
    {
        return $date instanceof DateTimeInterface
            ? $date->format($this->format ?? 'Y-m-d')
            : $date;
    }

    /**
     * Convert the rule to a validation string.
     */
    public function __toString(): string
    {
        return implode('|', [
            $this->format === null ? 'date' : 'date_format:'.$this->format,
            ...$this->constraints,
        ]);
    }
}
