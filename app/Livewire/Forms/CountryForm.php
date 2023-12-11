<?php

namespace App\Livewire\Forms;

use App\Models\Country;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule as ValidationRule;

class CountryForm extends BaseFormComponent
{
    #[Validate]
    public string|null $name;

    #[Validate]
    public string|null $short_code;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'max:100',
                ValidationRule::unique('countries', 'name')->ignore($this->model->id, 'id')->whereNull('deleted_at'),
            ],
            'short_code' => 'required|max:10',
        ];
    }
}
