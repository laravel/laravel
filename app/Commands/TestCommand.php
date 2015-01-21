<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class TestCommand extends Command implements SelfHandling {

    protected $_fields=['name', 'email'];

	public function handle()
	{
        var_dump($this->_data);

		die($this->email);
	}

}
