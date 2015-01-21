<?php namespace App\Commands;

abstract class Command {


    /**
     * list of the command data fields.
     *
     * @var array
     */
    protected $_fields=[];

    /**
     * an associative array of the command data fields and their values,
     * that is filled by the constructor from its $input.
     *
     * @var array
     */
    protected $_data=[];

    /**
     * Populate $this->_data from $input
     *
     * @param array|ArrayAccess $input
     * @return Command
     */
    public function __construct($input)
    {
        foreach($this->_fields as $field)
        {
            $this->_data[$field]=isset($input[$field])?$input[$field]:null;
        }
    }

    /**
     * Retrieve data fields.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_data[$key];
    }
}
