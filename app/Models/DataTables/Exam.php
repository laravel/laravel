<?php

namespace App\Models\DataTables;


use App\User;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['user_id','header','text'];         // ! attention !

    //protected $with = 'user:id,name,email' ;
    protected $with = ['user'] ; // Eloquent: Relationships   https://laravel.com/docs/5.6/eloquent-relationships#eager-loading , to reduce number of queries
    /**
     * The attributes that should be hidden for arrays
     * data wil not be found by dataTables
     * @var array
     */
    protected $hidden = [

    ];
    /*
    * dataTables , fields not visible for the user exept user_id
    */
    protected $dataTablesHidden = [
       'id' ,  'created_at' //, 'header','text','updated_at'
    ];
    /**
     * editor
     * choose fields wich are editable , must be in $fillable, exept 'user_id'
     *
     * @var array
     */
    protected $editable = [
        'header' , 'text'
    ];
    /** loaded on runtime getColumnsAndLabels()
     * @var array  , it` a must
     */
    protected $th_with_name = [];

    protected $thfields = [] ;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->filterMustsHave();  //dd($this->dataTablesHidden , $this->editable ,  __FILE__ , __LINE__ );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getColumnsAndLabels(){

        //dd( $this->newQuery()->fromQuery('select * FROM exams WHERE user_id like 11  ')    );  //Database/Eloquent/Builder.html#method_fromQuery
        $tempo = $this->newQuery()->fromQuery("SHOW FIELDS FROM ".$this->getTable());
        //dd($tempo);

        //dd($this ,  new \ReflectionClass($this));

        //https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Collection.html#method_all
        foreach($tempo->all() as $key=>$val){
            //echo '<br>field name: '.$val->Field;
            //echo '<br>field type: '.$val->Type;

            if( !in_array( $val->Field ,  $this->dataTablesHidden   ) ) {
                //echo '<br>'.$val->Field;
                if ($val->Type == 'text') {
                    $this->th_with_name[] = 'exam.' . $val->Field;

                    $thead[] = $val->Field;          // voeg iets toe
                    $table[] = '{data: "' . $val->Field . '"}';
                    $editor[] = '{label: "' . $val->Field . '" , name: "' . $val->Field . '" , type: "textarea"}';
                } else {
                    $this->th_with_name[] = 'exam.' . $val->Field;

                    $thead[] = $val->Field;
                    $table[] = '{data: "' . $val->Field . '"}';
                    $editor[] = '{label: "' . $val->Field . '" , name: "' . $val->Field . '"}';
                }
            }else{
                //echo '<h1>'.$val->Field.'</h1>';
            }
        }
        //dd($tempo->all(), new \ReflectionClass($tempo )   );  //Database\Eloquent\Collection

        //dd( 'table_collumns',$table ,'table_editor', $editor ,  __FILE__ , __LINE__ );


        $table_collumns = '';
        foreach($table as $key=>$val){
            if($key != (count($table)-1)) {
                $table_collumns .= $val.',';
            }else{
                $table_collumns .= $val;
            }
        }
        $table_editor = '';
        foreach($editor as $key=>$val){
            if($key != (count($editor)-1)) {
                $table_editor .= $val.',';
            }else{
                $table_editor .= $val;
            }
        }
        $x['table_collumns'] = $table_collumns;
        $x['table_editor'] = $table_editor;
        $x['thead'] = $thead;
        $this->thfields = $thead;
        //dd($x['thead'] , $this->thfields );
        //dd( $this->th_with_name , $x['table_collumns'] ,$x['table_editor']  , $x[3] , $this->thfields );
        //dd($x);

        return $x ;
    }

    /**
     * editor
     * $this->thfields , runtime public function getColumnsAndLabels()
     */
    public function getnoteditable(){

        return (array_diff($this->thfields, $this->editable));
    }

    /**
     * id and user_id
     */
    protected function filterMustsHave(){

        foreach ($this->dataTablesHidden as $key => $value) {
            if ('user_id' == $value ) {unset($this->dataTablesHidden[$key]) ;}
            //if ('user_id' == $value OR 'id' == $value) {unset($this->dataTablesHidden[$key]) ;}
        }
        foreach ($this->editable as $key => $value) {
            if ('user_id' == $value ) {unset($this->editable[$key]) ;}
            //if ('user_id' == $value OR 'id' == $value) {unset($this->editable[$key]) ;}
        }
    }


}
