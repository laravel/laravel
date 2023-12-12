<?php

namespace App\Livewire\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Livewire\Features\SupportRedirects\Redirector;

class BaseFormComponent extends Component
{
    public string $modelClass;

    public string $modelIndexRoute;

    public string $livewireForm;

    public Model $model;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->_setFormProperties();
    }

    /**
     * @return Factory|View
     */
    public function render(): Factory|View
    {
        return view('livewire.forms.country');
    }

    /**
     * @return Redirector|RedirectResponse
     */
    public function submitForm() : Redirector|RedirectResponse
    {
        $this->validate();

        $isNew = !$this->model->exists;

        $this->_save();

        request()->session()->flash('success', sprintf("%s %s successfully", class_basename($this->model), ($isNew ? 'created' : 'updated')));

        return redirect()->route($this->modelIndexRoute);
    }

    /**
     * @return void
     */
    public function formReset(): void
    {
        $this->model->refresh();
        $this->_setFormProperties();
    }

    /**
     * @return Model
     */
    protected function _save(): Model
    {
        // Update the property
        foreach ($this->_getFromProperties() as $fromProperty) {
            $this->model->$fromProperty = $this->$fromProperty;
        }

        // Save the form
        $this->model->save();
        $this->model->refresh();

        return $this->model;
    }

    /**
     * @return array
     */
    protected function _getFromProperties(): array
    {
        return $this->model->getFillable();
    }

    /**
     * @return void
     */
    protected function _setFormProperties(): void
    {
        foreach ($this->_getFromProperties() as $fromProperty) {
            $this->$fromProperty = $this->model->$fromProperty;
        }
    }
}
