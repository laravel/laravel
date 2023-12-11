<?php

namespace App\Livewire\Forms;

use App\Models\Country;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule as ValidationRule;

class CountryForm extends BaseFormComponent
{
    #[Validate]
    public string|null $name = null;

    #[Validate('required|max:10')]
    public string|null $short_code = null;

    #[Validate('nullable|max:25')]
    public string|null $color = null;

    #[Validate('nullable|date_format:Y-m-d')]
    public string|null $date = null;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'max:100',
                ValidationRule::unique('countries', 'name')->ignore($this->model->id, 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
