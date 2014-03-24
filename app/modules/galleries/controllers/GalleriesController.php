<?php namespace App\Modules\Galleries\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryImage;

class GalleriesController extends \BaseController {

 /*function for plugins*/
	public function getGalleryId(){
		return Gallery::get(array('id','title'));
	}
	
	public function newGallery($params)
	{
		$param = $this->splitParams($params);
		$newGallerys = Gallery::where('start_publish','<=','CURDATE()')->whereRaw('(end_publish IS NULL OR end_publish >= CURDATE())')->orderBy($param['order'],$param['sort'])->take($param['limit'])->select(array('id','title'))->get();
		return View::make('galleries::site.newGallerys', compact('newGallerys'));
	}
	
	public function showGalleries($ids="",$grids="",$sorts,$limits,$orders)
	{
		$showGallery =array();
		$showImages =array();
			
		if($ids!="" && $grids==""){
			$ids = rtrim($ids, ",");
			$ids = explode(',', $ids);
			$showGallery = Gallery::where('start_publish','<=','CURDATE()')->whereRaw('(end_publish IS NULL OR end_publish >= CURDATE())')->whereIn('id', $ids)->orderBy($orders,$sorts)->select(array('id','title','folderid'))->get();
			foreach ($ids as $value) {
				$showImages[$value] = GalleryImage::where('gallery_id', $value)->select(array('id','content'))->get();
			}
			
		}
		else if($limits!=0)
		{
			$showGallery = Gallery::where('start_publish','<=','CURDATE()')->whereRaw('(end_publish IS NULL OR end_publish >= CURDATE())')->orderBy($orders,$sorts)->take($limits)->select(array('id','title','folderid'))->get();
		}
		return View::make('galleries::site.showGallery', compact('showGallery','showImages'));
	}
	
}