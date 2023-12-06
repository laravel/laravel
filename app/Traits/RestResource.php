<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Trait RestResource
 */

/*
|--------------------------------------------------------------------------
| DONT UPDATE ANYTHING HERE
|--------------------------------------------------------------------------
| !!! BE ALERT !!!
| !!!!!! BEWARE THIS IS A COMMON FOR ALL PAGES !!!!!
| BEFORE YOU WRITE ANY CODE HERE - DO VERY CAREFULLY
| OTHERWISE IT WILL AFFECT ALL PAGE
| HOPE EVERY ONE READ THIS COMMENT :)
| CONFIRM WITH PRAKASH BEFORE YOU CHANGE SOMETHING HERE
|
*/

trait RestResource
{
    protected string $modelClass;

    protected string $livewireForm;

    protected string $restPrefix;

    protected string $resourcePath;

    protected string $indexBladePath = "backend.crud.index";

    protected string $createBladePath = "backend.crud.create";

    protected string $editBladePath = "backend.crud.edit";

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Application|Factory|View
    {
        $data = $this->_getBladeVariables() + $this->_indexFormData();

        return view($this->indexBladePath, $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): Application|Factory|View
    {
        $data = $this->_getBladeVariables() + $this->_createFormData() + ['model' => $this->_getCreateModel()];

        return view($this->createBladePath, $data);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     *
     * @return Application|Factory|View
     */
    public function show(string $id): Application|Factory|View
    {
        $data = $this->_getBladeVariables() +
            ['id' => $id, 'model' => $this->getModel($id)];

        return view('backend.crud.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     *
     * @return Application|Factory|View
     */
    public function edit(string $id): Application|Factory|View
    {
        $data = $this->_getBladeVariables() +
            $this->_updateFormData($data['model']) +
            ['id' => $id, 'model' => $this->getModel($id)];

        return view($this->editBladePath, $data);
    }

    /***
     * @param string $id
     * @param array|string $with
     * @return Model
     */
    public function getModel(string $id, array|string $with = []): Model
    {
        return $this->modelClass::query()->with($with)->findOrFail($id);
    }

    /**
     * @return Model
     */
    protected function _getCreateModel(): Model
    {
        return (new $this->modelClass);
    }

    /**
     * @return array
     */
    protected function _indexFormData() :array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function _createFormData() :array
    {
        return [];
    }

    /***
     * @param $model
     * @return array
     */
    protected function _updateFormData(&$model) :array
    {
        return [];
    }

    /**
     * @return array
     */
    private function _getBladeVariables(): array
    {
        return get_object_vars($this) + [
            'modelIndexRoute' => sprintf("%s.index", $this->restPrefix),
            'modelCreateRoute' => sprintf("%s.create", $this->restPrefix),
            'modelStoreRoute' => sprintf("%s.store", $this->restPrefix),
            'modelEditRoute' => sprintf("%s.edit", $this->restPrefix),
            'modelUpdateRoute' => sprintf("%s.update", $this->restPrefix),
            'modelDeleteRoute' => sprintf("%s.destroy", $this->restPrefix),
        ];
    }
}
