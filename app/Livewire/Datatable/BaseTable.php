<?php

namespace App\Livewire\Datatable;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BaseTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")->deselected(),
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

    /**
     * @param $id
     * @return void
     */
    public function destroy($id)
    {
        // @todo:: need to optimize more
        $this->model::find($id)->delete();
        $this->dispatch('refreshDatatable');
    }
}
