<?php namespace App\Modules\Blogs\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Blogs\Models\Blog;

class BlogsController extends \BaseController {

 /*function for plugins*/
	public function getBlogId(){
		return Blog::get(array('id','title'));
	}	
	
	public function getBlogGroupId(){
		return BlogCategory::get(array('id','title'));
	}
	
}