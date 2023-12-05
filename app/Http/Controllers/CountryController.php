<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends BaseController
{
    protected string $modelClass = Country::class;

    protected string $livewireForm = 'forms.country-form';

    protected string $restPrefix = 'countries';

    protected string $resourcePath = 'backend.countries';

//    protected string $modelIndexRoute = 'countries.index';
//
//    protected string $modelCreateRoute = 'countries.create';
//
//    protected string $modelStoreRoute = 'countries.store';
//
//    protected string $modelEditRoute = 'countries.edit';
//
//    protected string $modelUpdateRoute = 'countries.update';
//
//    protected string $modelDeleteRoute = 'countries.destroy';

    /**
     * @param $request
     * @param null $id
     *
     * @return array
     */
    protected function _rules($request, $id = null): array
    {
        return [
            'name' => 'required|max:100',
            'short_code' => 'required|max:10',
        ];
    }
}
