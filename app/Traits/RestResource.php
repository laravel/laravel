<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

//    protected string $modelIndexRoute;
//
//    protected string $modelCreateRoute;
//
//    protected string $modelStoreRoute;
//
//    protected string $modelEditRoute;
//
//    protected string $modelUpdateRoute;
//
//    protected string $modelDeleteRoute;
//
    protected string $resourcePath;
//
//    protected bool $indexIsDatatable = true;
//
//    protected bool $enableAdvanceSearch = false;

    protected string $indexBladePath = "backend.crud.index";

    protected string $createBladePath = "backend.crud.create";

    protected string $editBladePath = "backend.crud.edit";

//    protected array $redirectQueryParams = [];

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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse|mixed
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $model = $this->_getCreateModel();

        $this->_modifyFormRequest($model, $request);

        $this->_validate($request);

        $model->create($this->_formData($request));

        return redirect()->route($this->modelIndexRoute ?: sprintf("%s.index", $this->resourcePath));
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

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     *
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $model = $this->getModel($id);

        $this->_modifyFormRequest($model, $request);

        $this->_validate($request, $id);

        $model->update($this->_formData($request));

        return redirect()->route($this->modelIndexRoute ?: sprintf("%s.index", $this->resourcePath), $this->redirectQueryParams);
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        $modal = $this->getModel($id);
        $modal->delete();

        return $modal;
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
     * @param $model
     * @param Request $request
     */
    protected function _modifyFormRequest($model, Request &$request)
    {
        //
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
    protected function _indexDataTableParams() :array
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
     * @param $request
     * @param null $id
     *
     * @return array
     */
    protected function _rules($request, $id = null) :array
    {
        return [];
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return array
     */
    protected function _ruleMessages($request, $id = null) :array
    {
        return [];
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return array
     */
    protected function _ruleAttributes($request, $id = null) :array
    {
        return [];
    }

    /**
     * @param $request
     * @param null $id
     * @return void
     * @throws ValidationException
     */
    protected function _validate($request, $id = null): void
    {
        $this->validate($request, $this->_rules($request, $id), $this->_ruleMessages($request, $id), $this->_ruleAttributes($request, $id));
    }

    /**
     * @param $request
     * @param $model
     * @return Model
     */
    protected function _save($request, $model): Model
    {
        $data = $request->except(['_token']);

        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    protected function _formData(Request $request, $id = null): array
    {
        return $request->except(['_token']);
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
