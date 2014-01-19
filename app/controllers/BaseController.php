<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ($this->layout !== null)
		{
			$this->layout = View::make($this->layout);
		}
	}

}
