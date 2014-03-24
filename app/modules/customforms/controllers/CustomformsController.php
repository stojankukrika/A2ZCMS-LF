<?php namespace App\Modules\Customforms\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Customforms\Models\Customform;
use App\Modules\Customforms\Models\Customformfield;

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
			$showCustomFormId = Customform::whereIn('id', $ids)->select(array('id','recievers','title','message'))->get();
			foreach ($ids as $id){
				$showCustomFormFildId[$id] = Customformfield::where('customform_id',$id)->orderBy('order','ASC')->select(array('id','name','options','type','order','mandatory'))->get();
			}
		}
		return View::make('customforms::site.showCustomFormId', compact('showCustomFormId','showCustomFormFildId'));
	 }
	
}