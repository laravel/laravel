<?php

namespace App\Orchid\Screens\Examples;

use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Map;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Fields\UTM;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ExampleFieldsAdvancedScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Advanced form controls';

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
            'name'  => 'Hello! We collected all the fields in one place',
            'place' => [
                'lat' => 37.181244855427394,
                'lng' => -3.6021993309259415,
            ],
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

                Group::make([
                    Input::make('phone')
                        ->mask('(999) 999-9999')
                        ->title('Phone')
                        ->placeholder('Enter phone number')
                        ->help('Number Phone'),

                    Input::make('ip_address')
                        ->title('IP address:')
                        ->placeholder('Enter address')
                        ->help('Specifies an address in IPv4 format')
                        ->mask([
                            'alias' => 'ip',
                        ]),

                    Input::make('license_plate')
                        ->title('License plate:')
                        ->mask([
                            'mask' => '[9-]AAA-999',
                        ]),
                ]),

                Group::make([

                    Input::make('credit_card')
                        ->mask('9999-9999-9999-9999')
                        ->title('Credit card:')
                        ->placeholder('Credit card number')
                        ->help('Number is the long set of digits displayed across the front your plastic card'),

                    Input::make('currency')
                        ->title('Currency dollar:')
                        ->mask([
                            'alias' => 'currency',
                        ])->help('Some aliases found in the extensions are: email, currency, decimal, integer, date, datetime, dd/mm/yyyy, etc.'),

                    Input::make('currency')
                        ->title('Currency euro:')
                        ->mask([
                            'mask'         => '€ 999.999.999,99',
                            'numericInput' => true,
                        ]),
                ]),

            ])->title('Input mask'),

            Layout::rows([

                Group::make([
                    DateTimer::make('open')
                        ->title('Opening date')
                        ->help('The opening event will take place'),

                    DateTimer::make('allowInput')
                        ->title('Allow input')
                        ->required()
                        ->allowInput(),

                    DateTimer::make('enabledTime')
                        ->title('Enabled time')
                        ->enableTime(),
                ]),

                Group::make([
                    DateTimer::make('format24hr')
                        ->title('Format 24hr')
                        ->enableTime()
                        ->format24hr(),

                    DateTimer::make('custom')
                        ->title('Custom format')
                        ->noCalendar()
                        ->format('h:i K'),

                    DateRange::make('rangeDate')
                        ->title('Range date'),
                ]),

            ])->title('DateTime'),

            Layout::columns([
                Layout::rows([
                    Select::make('robot.')
                        ->options([
                            'index'   => 'Index',
                            'noindex' => 'No index',
                        ])
                        ->multiple()
                        ->title('Multiple select')
                        ->help('Allow search bots to index'),

                    Relation::make('user')
                        ->fromModel(User::class, 'name')
                        ->title('Select for Eloquent model'),
                ])->title('Select'),
                Layout::rows([

                    Group::make([
                        CheckBox::make('free-checkbox')
                            ->sendTrueOrFalse()
                            ->title('Free checkbox')
                            ->placeholder('Event for free')
                            ->help('Event for free'),

                        Switcher::make('free-switch')
                            ->sendTrueOrFalse()
                            ->title('Free switch')
                            ->placeholder('Event for free')
                            ->help('Event for free'),
                    ]),

                    RadioButtons::make('radioButtons')
                        ->options([
                            1 => 'Enabled',
                            0 => 'Disabled',
                            3 => 'Pause',
                            4 => 'Work',
                        ])
                        ->help('Radio buttons are normally presented in radio groups'),

                ])->title('Status'),
            ]),

            Layout::rows([

                Picture::make('picture')
                    ->title('Picture')
                    ->horizontal(),

                Cropper::make('cropper')
                    ->title('Cropper')
                    ->width(500)
                    ->height(300)
                    ->horizontal(),

                Upload::make('files')
                    ->title('Upload files')
                    ->horizontal(),

            ])->title('File upload'),

            Layout::rows([

                UTM::make('link')
                    ->title('UTM link')
                    ->help('Generated UTM link'),

                Matrix::make('matrix')
                    ->columns([
                        'Attribute',
                        'Value',
                        'Units',
                    ]),

                Map::make('place')
                    ->title('Object on the map')
                    ->help('Enter the coordinates, or use the search'),

            ])->title('Advanced'),
        ];
    }
}
