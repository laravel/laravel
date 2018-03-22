<?php

namespace App\Http\Controllers\Frontend\Datatables;

use App\DataTables\Part\ExamDataTable;
use App\DataTables\Part\ExamDataTablesEditor;
use App\DataTables\Part\UserDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\DataTables\Exam;
use Illuminate\Support\Facades\Auth;


class ExamController extends Controller
{

    /**
     * ExamController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param ExamDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ExamDataTable $dataTable)
    {
        // JsonResponse
        //dd($this->getExamAjax($dataTable) ,'The same',$dataTable->ajax() , new \ReflectionClass($dataTable));


        $table_collumns = $dataTable->table_collumns;
        //dd($table_collumns);
        $table_editor = $dataTable->table_editor;
        $editor_disable = $dataTable->NotEditables;
        //dd($editor_disable);
        //$editor_disable = '';

        return $dataTable->render('frontend.datatables.exampopupedit' , compact('table_collumns' ,'table_editor' ,'editor_disable' ));
    }

    /**
     * @param ExamDataTable $dataTable
     * @param UserDataTable $tableuser
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExamAjax(ExamDataTable $dataTable ){

        return $dataTable->ajax();
    }

    /**
     * @param ExamDataTablesEditor $editor
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function Exameditor(ExamDataTablesEditor $editor,Request $request){            // !! Route::POST

        //dd($request->all(),  __FILE__ , __LINE__ ); // XHR Preview , what is send to the server

        return $editor->process(request());
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
