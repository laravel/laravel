<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends BaseController
{
    protected string $modelClass = Country::class;

    protected string $livewireForm = 'forms.country-form';

    protected string $restPrefix = 'countries';

    protected string $resourcePath = 'backend.countries';
}
