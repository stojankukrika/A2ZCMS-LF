<?php namespace App\Modules\Blogs\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Blogs\Models\Blog;
use App\Modules\Blogs\Models\BlogCategory;

class BlogsController extends \BaseController {

 /*function for plugins*/
	public function getBlogId(){
		return Blog::get(array('id','title'));
	}	
	
	public function getBlogGroupId(){
		return BlogCategory::get(array('id','title'));
	}
	
	public function newBlogs($params)
	{
		$param = $this->splitParams($params);
		$newBlogs = Blog::orderBy($param['order'],$param['sort'])->take($param['limit'])->select(array('id','title','slug'))->get();
		return View::make('site.partial_views.sidebar.newBlogs', compact('newBlogs'));
	}
	
	public function showBlogs($ids,$grids,$sorts,$limits,$orders)
	{
		$showBlogs = array();
		$ids = rtrim($ids, ",");

		if($ids!="" && $grids==""){
			$ids = rtrim($ids, ",");
			$ids = explode(',', $ids);
			
			$showBlogs = Blog::whereIn('id', $ids)->orderBy($orders,$sorts)->select(array('id','slug','title','content'))->get();
		}
		else if($limits!=0) {
			$showBlogs = Blog::orderBy($orders,$sorts)->take($limits)->select(array('id','slug','title','content'))->get();
		}
		return View::make('site.partial_views.content.showBlogs', compact('showBlogs'));
	}
	
}