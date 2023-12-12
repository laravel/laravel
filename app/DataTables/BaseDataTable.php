<?php

namespace App\DataTables;

use Livewire\Livewire;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BaseDataTable extends DataTable
{
    protected string $routePrefix;

    protected int $defaultOrderIndex = 0;

    protected string $defaultOrderDirection = 'asc';

    protected int $actionColumnWidth = 200;

    protected array $excludeDOMButtons = [];

    public function getColumns(): array
    {
        return  [
            Column::make('id'),
            Column::make('created_at'),
        ];
    }

    protected function getActionHtml($model, $additionalButtons = []) :string
    {
        $routePrefix = $this->routePrefix;
        $editRouteName = sprintf("%s.edit", $routePrefix);

        $editRoute = route($editRouteName, $model->id);
        $viewRoute = route(sprintf("%s.show", $routePrefix), $model->id);

        $action = "<div style='display: flex; gap:10px;'>";
        $action .= sprintf('<a wire:navigate class="btn btn-primary btn-sm mr-1" href="%s">Edit</a>', $editRoute);
        $action .= Livewire::mount('actions.row-delete', compact('model'));
        $action .= sprintf('<a wire:navigate class="btn btn-info btn-sm mr-1" href="%s">View</a>', $viewRoute);
        $action .= "</div>";

        return $action;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    protected function getBuilderHtml()
    {
        $params = $this->getBuilderParameters();

        return $this->builder()
            ->setTableId(sprintf("%s-table", $this->routePrefix))
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy($this->defaultOrderIndex, $this->defaultOrderDirection)
            ->selectStyleSingle()
            ->drawCallbackWithLivewire()
            ->parameters($params);
    }

    public function myCustomAction()
    {
        //...your code here.
    }

    /**
     * Get default builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters(): array
    {
        $parameters = config('datatables-buttons.parameters', []);

        // Exclude DOM Buttons
        if ($excludeDOMButtons = $this->excludeDOMButtons) {
            if (array_key_exists('buttons', $parameters)) {
                $parameters['buttons'] = array_values(array_filter($parameters['buttons'], function ($value) use ($excludeDOMButtons) {
                    return !in_array($value, $excludeDOMButtons);
                }));
            }
        }

        $func = sprintf("datatable%sDrawCallback", $this->resourceName ?? 'Model');
        $buttons = json_encode($parameters['buttons']);

        // Draw callback
        $parameters['drawCallback'] = 'function(settings){
            const api = this.api();
            const func = "'.$func.'";
            const buttonConfig = '.$buttons.';
            const commonFunc = "datatableDrawCallback";
            console.debug("callback func name", func);
            // For common draw callback
            if($.isFunction(window[commonFunc])) {
                eval(commonFunc + "(settings, api, buttonConfig)");
            }
            // For individual draw callback
            if($.isFunction(window[func])) {
                eval(func + "(settings, api)");
            }
        }';

        return $parameters;
    }

    protected function timestampColumn($model, $column)
    {
        $date = '';

        try {
            if ($model->$column) {
                $date = $model->$column->format('d-m-Y h:i A');
            }
        } catch (\Exception $e) {
            //
        }

        return $date;
    }
}
