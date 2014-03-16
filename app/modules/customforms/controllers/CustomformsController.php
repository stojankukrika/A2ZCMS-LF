<?php namespace App\Modules\Customforms\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Customforms\Models\Customform;

class CustomformsController extends \BaseController {

 /*function for plugins*/
	public function getCustomFormId(){
		return Customform::get(array('id','title'));
	}
	
	public function showCustomFormId($ids,$grids,$sorts,$limits,$orders)
 	{
 		
		$showCustomFormId ="";
		$showCustomFormFildId ="";
		$ids = rtrim($ids, ",");

		if($ids!=""){
			$ids = rtrim($ids, ",");
			$ids = explode(',', $ids);
			$showCustomFormId = CustomForm::whereIn('id', $ids)->select(array('id','recievers','title','message'))->get();
			foreach ($ids as $id){
				$showCustomFormFildId[$id] = CustomFormField::where('custom_form_id',$id)->orderBy('order','ASC')->select(array('id','name','options','type','order','mandatory'))->get();
			}
		}
		return View::make('site.partial_views.content.showCustomFormId', compact('showCustomFormId','showCustomFormFildId'));
	 }
	
}