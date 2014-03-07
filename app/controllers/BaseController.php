<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	
	public function __construct() {
		$user = new User;
		$this -> user = $user;
		$this -> beforeFilter('csrf', array('on' => 'post'));
		$this -> beforeFilter('detectLang');
		// Redirect to /install if the db isn't setup.
		if (Config::get("a2zcms.installed") !== true) {
			$url = URL::to('install');
			header('Location: '.$url);
			exit ;
		}
	}

}