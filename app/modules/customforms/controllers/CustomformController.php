<?php namespace App\Modules\Customform\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Customform\Models\Customform;

class CustomformController extends \BaseController {

 /*function for plugins*/
	public function getCustomFormId(){
		return CustomForm::get(array('id','title'));
	}
	
}