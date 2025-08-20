<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueWithTrashed implements ValidationRule
{
    protected string $modelClass;
    protected string $column;
    protected ?int $ignoreId;

    /**
     * @param string $modelClass Eloquent model class
     * @param string $column Column name to check uniqueness
     * @param int|null $ignoreId ID to ignore (useful for updates)
     */
    public function __construct(string $modelClass, string $column = 'name', ?int $ignoreId = null)
    {
        $this->modelClass = $modelClass;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = $this->modelClass::withTrashed()
            ->where($this->column, $value)
            ->whereNull('deleted_at');

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail("The $attribute has already been taken.");
        }
    }
}
