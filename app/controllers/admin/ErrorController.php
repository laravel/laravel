<?php 

class ErrorController extends BaseController
{

	public function defaultError()
	{
		return View::make('error.defaulterror');
	}
}