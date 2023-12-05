<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;

class CountryForm extends BaseFormComponent
{
    #[Validate('required')]
    public string|null $name;

    #[Validate('required')]
    public string|null $short_code;
}
