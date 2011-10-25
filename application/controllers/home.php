<?php

class Home_Controller extends Controller {

	public function index()
	{
		return View::make('home.index');
	}

}