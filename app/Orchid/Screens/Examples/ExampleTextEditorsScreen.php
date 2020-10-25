<?php

namespace App\Orchid\Screens\Examples;

use Illuminate\Support\Str;
use Orchid\Screen\Action;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\SimpleMDE;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ExampleTextEditorsScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Form Text Editors';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Examples for creating a wide variety of forms.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'quill'     => 'Hello! We collected all the fields in one place',
            'simplemde' => '# Big header',
            'code'      => Str::limit(file_get_contents(__FILE__), 500),
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @throws \Throwable
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                SimpleMDE::make('simplemde')
                    ->title('SimpleMDE')
                    ->popover('SimpleMDE is a simple, embeddable, and beautiful JS markdown editor'),

                Quill::make('quill')
                    ->title('Quill')
                    ->popover('Quill is a free, open source WYSIWYG editor built for the modern web.'),

                Code::make('code')
                    ->title('Name Articles'),

            ]),
        ];
    }
}
