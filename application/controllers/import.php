<?php

class Import_Controller extends Base_Controller {

	public $restful = true;    

	public function get_index()
    {
    	$table_name = 'laravel_migrations';
    	$columns = DB::query("select column_name, data_type, character_maximum_length from INFORMATION_SCHEMA.COLUMNS where table_name = '{$table_name}'");
    	$forbidden_fields = array('id', 'created_at', 'updated_at');
    	$fields_list = array();
    	foreach ($columns as $column) {
    		$field_command = null;
    		if (!in_array($column->column_name, $forbidden_fields)) {
    			
    			switch (strtolower($column->data_type)) {
    				case 'varchar':
    				case 'character varying':
    					$data_type = 'string';
    				break;
    				
    				default:
    					;
    				break;
    			}
    			
    			if (!empty($column->character_maximum_length)) {
    				$data_type .= '=' . $column->character_maximum_length;
    			}
    			
    			$field_command = $column->column_name . ':' . $data_type;
    			$fields_list[] = $field_command;
    		}
    	}
    	Laravel\CLI\Command::run(array( 'generate:migration', "create_{$table_name}_table", $fields_list ));
    }    

	public function get_show()
    {

    }    

	public function get_edit()
    {

    }

}