<?php

namespace App\Livewire\Forms;

use App\Models\Country;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule as ValidationRule;

class CountryForm extends BaseFormComponent
{
    #[Validate('required|max:100|string|unique:countries,name,{model.id},id')]
    public string|null $name;

    #[Validate('required|max:10')]
    public string|null $short_code;
}
