<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueWithTrashed implements ValidationRule
{
    protected string $modelClass;
    protected string $column;
    protected ?int $ignoreId;


    public function __construct(string $modelClass, string $column = 'name', ?int $ignoreId = null)
    {
        $this->modelClass = $modelClass;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

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
