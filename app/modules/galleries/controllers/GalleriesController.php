<?php namespace App\Modules\Galleries\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Galleries\Models\Gallery;

class GalleriesController extends \BaseController {

 /*function for plugins*/
	public function getGalleryId(){
		return Gallery::get(array('id','title'));
	}
	
}