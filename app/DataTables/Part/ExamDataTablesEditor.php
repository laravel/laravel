<?php

namespace App\DataTables\Part;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataTables\Exam;

class ExamDataTablesEditor extends DataTablesEditor
{
    protected $model = Exam::class;

    /**
     * Get create action validation rules.   https://laravel.com/docs/5.6/validation#available-validation-rules
     *
     * @return array
     */
    public function createRules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            //'user.name' => 'required|exists:users,name',
            'header' => 'between:1,60',
            'text'  => 'between:1,20000'
        ];
    }

    /**
     * Get edit action validation rules.
     *
     * @param Model $model
     * @return array
     */
    public function editRules(Model $model)
    {

        //dd(class_basename($model) , __FILE__ , __LINE__ );
        return [
            'header'  => 'between:1,60',
            'text'   => 'between:1,20000'
        ];
    }

    /**
     * Get remove action validation rules.
     *
     * @param Model $model
     * @return array
     */
    public function removeRules(Model $model)
    {

        return [

        ];
    }
    /****** tjeu
    public function process(Request $request , Model $model)
    {


        $action = $request->get('action');
        $data = $request->get('data');

        foreach($data as $key=>$val){

            echo '<br>'.$val['user_id'];
            echo '<br>'.$val['user']['name'];
            echo '<br>'.$val['user']['id'];

        }

        dd( $request->all() , $action , $data ,  __FILE__ , __LINE__ );

        return $this->{$action}($request);
    }
       */
}
