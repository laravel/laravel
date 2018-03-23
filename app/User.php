<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];
    /**
     * The attributes that should be hidden for arrays.
     * data wil not be found by dataTables
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
    /*
     * dataTables , fields not visible for the user
     */
    protected $dataTablesHidden = [
        'id'  , 'password', 'remember_token' , 'created_at' , 'updated_at' //, 'name' , 'email'
    ];
    /**
     * editor
     * choose fields wich are editable , must be in $fillable (exept: 'remember_token' , 'created_at' , 'updated_at')
     *
     * @var array
     */
    protected $editable = [

    ];
    /** loaded on runtime getColumnsAndLabels()
     * @var array  , it`s a must
     */
    protected $th_with_name = [];




    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        //$this->filterMustsHave();  //dd($this->dataTablesHidden , $this->editable ,  __FILE__ , __LINE__ );
    }

    public function exam(){
        return $this->hasMany('App\Models\DataTables\Exam');
    }

    public function getColumnsAndLabels(){

        //dd( $this->newQuery()->fromQuery('select * FROM exams WHERE user_id like 11  ')    );  //Database/Eloquent/Builder.html#method_fromQuery
        $tempo = $this->newQuery()->fromQuery("SHOW FIELDS FROM ".$this->getTable());
        //dd($tempo->all());



        $table_collumns ='';$table_editor = '';$thead = array();
        //https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Collection.html#method_all
        foreach($tempo->all() as $key=>$val){
            //echo '<br>field name: '.$val->Field;
            //echo '<br>field type: '.$val->Type;
            if( !in_array( $val->Field ,  $this->dataTablesHidden   ) ) {

                //echo '<br>field name: '.$val->Field;
                //echo '<br>field type: '.$val->Type;
                if ($val->Type == 'text' || $val->Type == 'varchar') {
                    $this->th_with_name[] = 'user.' . $val->Field;

                    $thead[] = $val->Field;
                    $table_collumns .= '{data: "user.' . $val->Field . '"},';
                    $table_editor .= '{label: "' . $val->Field . '" , name: "user.' . $val->Field . '" , type: "textarea"},';
                } else {
                    $this->th_with_name[] = 'user.' . $val->Field;

                    $thead[] = $val->Field;
                    $table_collumns .= '{data: "user.' . $val->Field . '"},';
                    $table_editor .= '{label: "' . $val->Field . '" , name: "user.' . $val->Field . '"},';
                }
            }
        }
        //dd($tempo->all(), new \ReflectionClass($tempo )   );  //Database\Eloquent\Collection

        //dd( 'Table head',$thead , 'table_collumns',$table_collumns ,'table_editor', $table_editor ,  __FILE__ , __LINE__ );

        $x['table_collumns'] = $table_collumns;
        $x['table_editor'] = $table_editor;
        $x['thead'] = $thead;


        //dd('$table_collumns',$x[1] ,'$table_editor',$x[2]  ,'THead',$x[3] );
        //dd($x);

        return $x ;
    }
    /*
     * editor
     * $this->th_with_name  , runtime property ,  public function getColumnsAndLabels()
     */
    public function getnoteditable(){
        $editableWithName = [];
        if(count($this->editable) > 0 ){
            foreach($this->editable as $key=>$val){
                $editableWithName[] = 'user.'.$val;
            }
        }

        //dd(  $this->th_with_name , $editableWithName , array_diff($this->th_with_name, $editableWithName)  );
        return (array_diff($this->th_with_name, $editableWithName));
    }

    /**
     * id
     */
    protected function filterMustsHave(){

        foreach ($this->dataTablesHidden as $key => $value) {
            if ( 'id' == $value) {unset($this->dataTablesHidden[$key]    ) ;}
        }
        foreach ($this->editable as $key => $value) {
            if ( 'id' == $value) {unset($this->editable[$key]    ) ;}
        }
    }

}
