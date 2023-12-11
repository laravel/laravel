<?php

namespace App\Livewire\Datatable;

use App\Models\Country;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class CountriesTable extends BaseTable
{
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
            $this->_getActionColumn()
        ];
    }
}
