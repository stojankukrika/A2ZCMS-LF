<?php namespace App\Modules\Customforms\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Customform\Models\Customform;

class CustomformsController extends \BaseController {

 /*function for plugins*/
	public function getCustomFormId(){
		return CustomForm::get(array('id','title'));
	}
	
}