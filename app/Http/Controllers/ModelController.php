<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\<!MODEL!>;

class <!MODEL!>Controller extends Controller {
	public function __construct(){
		// $this->middleware('auth');
	}

	public function index(Request $request) {
		$pdo = DB::connection()->getPdo();
		$desc = $pdo->query('describe <!TBL!>')->fetchAll();
		return view('<!MODEL!>.index', ['objects' => $pdo->query('select * from <!TBL!>')->fetchAll(), 'desc' => $desc]);
	}

	public function create(Request $request) {
		$pdo = DB::connection()->getPdo();
		$desc = $pdo->query('describe <!TBL!>')->fetchAll();
		return view('<!MODEL!>.create', ['desc' => $desc]);
	}

	public function store(Request $request) {
		$obj = new <!MODEL!>();
		$obj->fill($request->all());
		$obj->save();
		return back();
	}

	public function update(Request $request) {
		$obj = <!MODEL!>::find($request->id);
		$obj->fill($request->all());
		$obj->save();
		return back();
	}

	public function delete(Request $request, $id) {
		$obj = <!MODEL!>::find($id);
		$obj->delete();
		return back();
	}

}
