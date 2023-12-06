<?php

namespace App\Livewire\Forms;

use App\Models\Country;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule as ValidationRule;

class CountryForm extends BaseFormComponent
{
    public string|null $name;

    public string|null $short_code;

    public function rules()
    {
        return [
            'name' => "required|max:100|string|unique:countries,name,{$this->model->id},id",
            'short_code' => 'required'
        ];
    }
}
