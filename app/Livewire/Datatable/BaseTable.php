<?php

namespace App\Livewire\Datatable;

use App\Exports\TableExport;
use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BaseTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setBulkActionsStatus(true);
        $this->setBulkActions([
            'exportSelected' => 'Export',
        ]);

        $this->setPrimaryKey('id');
    }

    public function exportSelected()
    {
        return Excel::download(new TableExport($this->model, $this->getSelected()), sprintf("%s.xlsx", Str::lower(class_basename($this->model))));
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
