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
    public string $restPrefix;

    public string $modelClass;

    protected $model = ''; // We must specify this one and should not null (we just override - need to check any issue occurs)

    public function mount(string $restPrefix, string $modelClass): void
    {
        $this->restPrefix = $restPrefix;
        $this->modelClass = $this->model = $modelClass;
    }

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
            $this->_getActionColumn(),
        ];
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->modelClass;
    }

    /**
     * @param $id
     * @return void
     */
    public function destroy($id)
    {
        // @todo:: need to optimize more
        $this->modelClass::find($id)->delete();
        $this->dispatch('refreshDatatable');
    }

    protected function _getActionColumn()
    {
        return Column::make('Action')
            ->label(
                fn ($row, Column $column) => view('livewire.datatables.action-column')->with(
                    [
                        'row' => $row,
                        'modelClass' => $this->model,
                        'viewLink' => route(sprintf("%s.show", $this->restPrefix), $row),
                        'editLink' => route(sprintf("%s.edit", $this->restPrefix), $row),
                        'deleteLink' => route(sprintf("%s.destroy", $this->restPrefix), $row),
                    ]
                )
            )->html();
    }
}
