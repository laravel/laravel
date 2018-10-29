<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScaffoldController extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'Scaffold:Controller {tableName} {modelName}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Controller Scaffolding, with this options: {tableName} {modelName}';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$tableName = $this->argument('tableName');
		$modelName = $this->argument('modelName');
		#
		mkdir("resources/views/{$modelName}", 0777, true);
		#
		copy("resources/views/Model/layout.blade.php", "resources/views/{$modelName}/layout.blade.php");
		#
		$index = file_get_contents("resources/views/Model/index.blade.php");
		$index = str_replace("<!MODEL!>", $modelName, $index);
		file_put_contents("resources/views/$modelName/index.blade.php", $index);
		#
		$create = file_get_contents("resources/views/Model/create.blade.php");
		$create = str_replace("<!MODEL!>", $modelName, $create);
		file_put_contents("resources/views/$modelName/create.blade.php", $create);
		#
		$ctl = file_get_contents("app/Http/Controllers/ModelController.php");
		$ctl = str_replace("<!MODEL!>", $modelName, $ctl);
		$ctl = str_replace("<!TBL!>", $tableName, $ctl);
		file_put_contents("app/Http/Controllers/{$modelName}Controller.php", $ctl);
		#
		$route = "
Route::get('/{$modelName}/', '{$modelName}Controller@index');
Route::get('/{$modelName}/create', '{$modelName}Controller@create');
Route::post('/{$modelName}/create', '{$modelName}Controller@store');
Route::post('/{$modelName}/update', '{$modelName}Controller@update');
Route::get('/{$modelName}/delete/{id}', '{$modelName}Controller@delete');
		";
		$f = fopen('routes/web.php', 'a');
		fwrite($f, $route);
	}
}
