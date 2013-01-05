<?php

class Import_Controller extends Base_Controller {

	public $restful = true;

	public function get_index()
    {
        $database = 'projecttables';
        $host = 'localhost';
        $username = 'postgres';
        $password = '3728';
        $schema = 'public';

        $options = array(
                PDO::ATTR_CASE => PDO::CASE_LOWER,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
        );

        $dsn = "pgsql:host={$host};dbname={$database}";

        $connection = new PDO($dsn, $username, $password, $options);
        $connection->prepare("SET search_path TO {$schema}")->execute();

        $tablestmt = $connection->prepare("select table_name
            			 from INFORMATION_SCHEMA.TABLES where table_schema = '{$schema}' ORDER BY INFORMATION_SCHEMA.TABLES.table_name ASC");
        $tablestmt->execute();
        $tables = $tablestmt->fetchAll();
        $references_arr = array();
        foreach ($tables as $table) {
        	$table_name = $table["table_name"];
	        $sth = $connection->prepare("select column_name, data_type, character_maximum_length, numeric_precision, numeric_scale
	    			 from INFORMATION_SCHEMA.COLUMNS where table_name = '{$table_name}'");
	        $sth->execute();
	        $columns = $sth->fetchAll();

	    	$forbidden_fields = array('id', 'created_at', 'updated_at');
	    	$fields_list = array();
	    	foreach ($columns as $column) {
	    		$field_command = null;
	    		if (!in_array($column["column_name"], $forbidden_fields)) {

	    			switch (strtolower($column["data_type"])) {
	    				case 'varchar':
	    				case 'character varying':
	    					$data_type = 'string';
	    				break;

	    				case 'int':
	    				case 'integer':
	    				case 'bigint':
	    					$data_type = 'integer';
	    				break;

	    				case 'real':
	    				case 'money':
	    					$data_type = 'float';
	    				break;

	    				case 'decimal':
	    				case 'numeric':
	    					$data_type = 'decimal';
	    					if (!empty($column["numeric_precision"])) {
	    						$data_type .= '=' . $column["numeric_precision"];
	    					}
	    					if (!empty($column["numeric_scale"])) {
	    						$data_type .= ', ' . $column["numeric_scale"];
	    					}
	    				break;

	    				case 'timestamp':
	    				case 'timestamp without time zone':
	    					$data_type = 'timestamp';
	    				break;

	    				case 'time':
	    				case 'time without time zone':
	    					$data_type = 'timestamp';
	    				break;

	    				default:
	    					$data_type = $column["data_type"];
	    				break;
	    			}

	    			if (!empty($column["character_maximum_length"])) {
	    				$data_type .= '=' . $column["character_maximum_length"];
	    			}

	    			$field_command = $column["column_name"] . ':' . $data_type;
	    			$fields_list[] = $field_command;
	    		}
	    	}
	    	Laravel\CLI\Command::run(array( 'generate:migration', "create_{$table_name}_table", $fields_list ));

	    	$sth = $connection->prepare("
					SELECT rc.constraint_catalog,
					       rc.constraint_schema||'.'||tc.table_name AS table_name,
					       kcu.column_name,
					       match_option,
					       update_rule,
					       delete_rule
					FROM information_schema.referential_constraints AS rc
					    JOIN information_schema.table_constraints AS tc USING(constraint_catalog,constraint_schema,constraint_name)
					    JOIN information_schema.key_column_usage AS kcu USING(constraint_catalog,constraint_schema,constraint_name)
					    JOIN information_schema.key_column_usage AS ccu ON(ccu.constraint_catalog=rc.unique_constraint_catalog AND ccu.constraint_schema=rc.unique_constraint_schema AND ccu.constraint_name=rc.unique_constraint_name)
					WHERE ccu.table_catalog='{$database}'
					    AND ccu.table_schema='{$schema}'
					    AND ccu.table_name='{$table_name}'
					    AND ccu.column_name='id';
	    	        ");
			$sth->execute();
    	    $references = $sth->fetchAll();
    	    foreach ($references as $reference) {
    	        array_push($references_arr, array(
    	        	"table_name" => $table_name,
	                "reference_table_name" => str_replace("$schema.",	"", $reference["table_name"]),
	                "reference_column_name" => $reference["column_name"],
	                "reference_update_rule" => $reference["update_rule"],
	                "reference_delete_rule" => $reference["delete_rule"],
    	        ));

  	        }
        }

        sleep(3);

        foreach ($references_arr as $reference) {
            $table_name = $reference["reference_table_name"];
        	Laravel\CLI\Command::run(array( 'generate:reference', "create_{$table_name}_table", array(
                "table_name" => $table_name,
                "reference_table_name" => $reference["table_name"],
                "reference_column_name" => $reference["reference_column_name"],
                "reference_update_rule" => $reference["reference_update_rule"],
                "reference_delete_rule" => $reference["reference_delete_rule"],
	        )));
        }
    }

	public function get_show()
    {

    }

	public function get_edit()
    {

    }

}