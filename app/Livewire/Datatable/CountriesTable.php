<?php

namespace App\Livewire\Datatable;

use App\Models\Country;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class CountriesTable extends BaseTable
{
    protected $model = Country::class;

    public function columns(): array
    {
        return [
            Column::make("Id", "id")->deselected(),
            Column::make("Name", "name")
                ->searchable()
                ->sortable(),
            Column::make("Short code", "short_code")
                ->searchable()
                ->sortable(),
            BooleanColumn::make('Active', 'active'),
            Column::make('Action')
                ->label(
                    fn ($row, Column $column) => view('livewire.datatables.action-column')->with(
                        [
                            'row' => $row,
                            'modelClass' => $this->model,
                            'viewLink' => route('countries.show', $row),
                            'editLink' => route('countries.edit', $row),
                            'deleteLink' => route('countries.destroy', $row),
                        ]
                    )
                )->html(),
        ];
    }
}
