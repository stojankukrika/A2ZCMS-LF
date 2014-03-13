<?php namespace App\Modules\Customforms\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Customforms\Models\Customform;

class CustomformsController extends \BaseController {

 /*function for plugins*/
	public function getCustomFormId(){
		return Customform::get(array('id','title'));
	}
	
}