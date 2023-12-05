<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;

class CountryForm extends BaseFormComponent
{
    #[Validate('required|max:3')]
    public string|null $name;

    #[Validate('required')]
    public string|null $short_code;
}
