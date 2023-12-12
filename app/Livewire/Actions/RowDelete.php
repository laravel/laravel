<?php

namespace App\Livewire\Actions;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class RowDelete extends Component
{
    public Model $model;

    public function render()
    {
        return view('livewire.actions.row-delete');
    }

    public function destroy()
    {
        // todo:: need to check the route access
        $this->model->delete();
        $this->dispatch('refresh-datatable');
    }
}
