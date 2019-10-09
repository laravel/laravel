<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Form;

class HtmlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('bsText', 'components.form.text', [
            'name',
            'params' => []
        ]);
        Form::component('bsNumber', 'components.form.number', [
            'name',
            'params' => []
        ]);
        Form::component('bsEmail', 'components.form.email', [
            'name',
            'params' => []
        ]);
        Form::component('bsPassword', 'components.form.password', [
            'name',
            'params' => []
        ]);
        Form::component('bsFile', 'components.form.file', [
            'name',
            'params' => []
        ]);
        Form::component('bsSelect', 'components.form.select', [
            'name',
            'options' => [],
            'params' => []
        ]);
        Form::component('bsTextArea', 'components.form.textarea', [
            'name',
            'params' => []
        ]);
        Form::component('bsSearchText', 'components.form.search-text', [
            'name',
            'params' => []
        ]);
        Form::component('bsSearchSelect', 'components.form.search-select', [
            'name',
            'options' => [],
            'params' => []
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
