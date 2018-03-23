<?php

namespace App\DataTables\Part;

use Yajra\DataTables\Services\DataTable;
use App\Models\DataTables\Exam;
use App\User;

class ExamDataTable extends DataTable
{

    protected $thead ;
    public $NotEditables = 'editor';
    public $table_collumns = '';
    public $table_editor = '';

    /**
     * ExamDataTable constructor.
     * @param Exam $model
     * @param User $user
     */
    public function __construct(Exam $model, User $user)
    {
        //1e
        //  get info: table 1 user , table 2 exam
        $temp_part1 = $user->getColumnsAndLabels();   //user info left site of HTML table
        $temp_part2 = $model->getColumnsAndLabels();  //exam info right site of HTML table with user // Eloquent: Relationships   https://laravel.com/docs/5.6/eloquent-relationships#eager-loading , to reduce number of queries
        //dd($temp_part1 , $temp_part2 , __FILE__ , __LINE__ );

        //  merge tables
        $this->thead = array_merge($temp_part1['thead'] ,$temp_part2['thead']);                   //model fields for builder html table head
        $this->table_collumns = $temp_part1['table_collumns'] . $temp_part2['table_collumns'] ;  //   data for table collumns
        $this->table_editor   = $temp_part1['table_editor'] . $temp_part2['table_editor'] ;      //   editor labels
        //dd( $user->getColumnsAndLabels() , $model->getColumnsAndLabels());
        //dd($this->thead , $this->table_collumns , $this->table_editor );

        /** 2e must be after 1e
         * combine tables not editable fields
         */
        $this->NotEditable = array_merge($model->getnoteditable() , $user->getnoteditable());
        foreach($this->NotEditable as $key=>$val){
            $this->NotEditables .= '.disable ("'.$val.'")';
        }
        //dd($this->thead , $this->NotEditables);
        //dd('table_collumns',$this->table_collumns , 'table_editor' , $this->table_editor ,'NotEditable',$this->NotEditable);
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        //dd(new \ReflectionClass($query));  // laravel Object  Illuminate\Database\Eloquent\Collection

        // datatables collection
        $tjeu =  datatables($query);
            //->addColumn('delete', 'aaaaaaaaaaa');         // werkt data for Table columns
            //->editColumn('delete', 'Tjeu')                       // werkt data for Table columns
            /*->editColumn('delete', function( $model) {           // werkt https://yajrabox.com/docs/laravel-datatables/master/edit-column#closure
                return 'Hi ' . $model->user->name . '!';
            })*/

        //dd($tjeu);
        //dd($tjeu->collection); // Collection  The results of Eloquent queries are always returned as Collection instances.
        //dd($tjeu->original);   // vendor/yajra/laravel-datatables-oracle/src/CollectionDataTable.php
        //dd($tjeu->results());
        //dd($tjeu->results()[0]);
        //dd($tjeu->results()[0]->user);
        //dd($tjeu->results()[0]->user->name);

        //dd($tjeu->results()[0]->header);

        /*foreach($tjeu->results() as $key=>$val){
            echo '<br>'.$key.' | header: '.$val->header.' | user name: '.$val->user->name;
        }*/

        //dd($tjeu->results()->user()->name);
        //dd($tjeu->collection->totalCount());
        //dd( 'Visible in XHR  , Object of vendor/yajra/laravel-datatables-oracle/src/CollectionDataTable.php',$tjeu->results()[0] ,   new \ReflectionClass($tjeu));

        return $tjeu;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Exam $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Exam $model)
    {

        // Illuminate\Database\Eloquent\Builder
        // https://laravel.com/docs/5.6/queries#joins

        /*$tjeu = $model->newQuery()->select('id', 'user_id', 'header' , 'text' );
        dd(\get_class($tjeu)); // Illuminate\Database\Eloquent\Builder*/

        //dd(\get_class($model)); // App\Models\DataTables\Exam
        //dd(\get_class($model->newQuery())); // Illuminate\Database\Eloquent\Builder

        //return $model->newQuery()->pluck('text' );          //https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html  pluck
        //return $model->newQuery()->applyScopes();

        // Eloquent: Relationships   https://laravel.com/docs/5.6/eloquent-relationships#eager-loading , to reduce number of queries
        // https://editor.datatables.net/examples/advanced/joinArray

        return $model->newQuery()
            //->with('user:id,name,email')    // protected property $with in $model , eager loading , always
            ->get();

        /*return $model->newQuery()
            ->where('header','=','tjeu')                                 // exams table
            ->leftJoin('users', 'users.id', '=', 'exams.user_id')       //users table
            ->whereBetween('users.id',[1,35])                           /users table
            ->get();*/
            // werkt zoek alle columns 'headers' waar een t in zit
        /*return $model->newQuery()
            ->where('header', 'like', 'T%')
            ->get();*/

        // Illuminate\Database\Eloquent\Builder
        //dd(new \ReflectionClass($model->newQuery()->select('id', 'user_id', 'header' , 'text' )));

        //return $model->newQuery()->select('id', 'user_id', 'header' , 'text' );
    }

    /**
     * Optional method if you want to use html builder.   !! only for html !!    in view  $dataTable->table()
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $tjeu = $this->builder()
                    //->columns($this->getColumns())
                    ->columns($this->thead)
                    ->minifiedAjax()
                    ->addAction(['width' => '80px','title'=>'delete'])
                    ->parameters($this->getBuilderParameters());

        //dd( $tjeu->getTableAttributes()  ,  new \ReflectionClass($tjeu) , __FILE__ , __LINE__ );
        //$tjeu->setTableAttribute(['color'=>'blue' ]);

            return $tjeu ;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return $this->thead;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Part/Exam_' . date('YmdHis');
    }

}
