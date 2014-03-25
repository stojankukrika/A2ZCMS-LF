<?php namespace App\Modules\Galleries\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryImage;
use App\Modules\Galleries\Models\GalleryImageComment;
use App\Modules\Galleries\Models\ContentVote;
use App\Modules\Settings\Models\Setting;
use App\Modules\Users\Models\User;

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

	public function getView($id) {

		$settings = Setting::where('varname','pageitem')->first();
		
		$pageitem = ($settings -> value>0)?$settings -> value:2;
        
		$gallery = Gallery::where('id', '=', $id) -> first();

		if (is_null($gallery)) {
			// If we ended up in here, it means that
			// a page or a blog blog didn't exist.
			// So, this means that it is time for
			// 404 error page.
			return App::abort(404);
		}
		
		$gallery -> hits = $gallery -> hits +1;
		$gallery -> update();

		$gallery_images = GalleryImage::where('gallery_id',$id) -> orderBy('created_at', 'ASC') ->paginate($pageitem);

		// Get current user and check permission
		$user = Auth::user();
		$canGalleryComment = false;
		/*if (!empty($user)) {
			$canGalleryComment = $user -> can('post_gallery_comment');
		}*/
		$page = \App\Modules\Pages\Models\Page::first();
		$pagecontent = \BaseController::createSiderContent($page->id);
		
		// Show the page
		$data['sidebar_right'] = $pagecontent['sidebar_right'];
		$data['sidebar_left'] = $pagecontent['sidebar_left'];
		$data['page'] = $page;
		$data['canGalleryComment'] = $canGalleryComment;
		$data['gallery_images'] = $gallery_images;
		$data['gallery'] = $gallery;
		return View::make('galleries::site/index', $data);
	}

	public function getGalleryImage($galid,$imgid)
	{
		$settings = Setting::where('varname','pageitem')->first();
		
		$pageitem = ($settings -> value>0)?$settings -> value:2;
       	$gallery = Gallery::find($galid);
		$gallery_image = GalleryImage::find($imgid);

		// Check if the blog blog exists
		if (is_null($gallery) || is_null($gallery_image)) {
			// If we ended up in here, it means that
			// a page or a blog blog didn't exist.
			// So, this means that it is time for
			// 404 error page.
			return App::abort(404);
		}
		$gallery_image -> hits = $gallery_image -> hits +1;
		$gallery_image -> update();
		
		$gallery_comments = GalleryImageComment::where('gallery_image_id',$gallery_image->id)-> orderBy('created_at', 'ASC') ->paginate($pageitem);
		
		// Get current user and check permission
		$user = Auth::user();
		$canGalleryComment = false;
		/*if (!empty($user)) {
			$canGalleryComment = $user -> can('post_gallery_comment');
		}*/
		
		$canImageVote = false;
		/*if (!empty($user)) {
			$canImageVote = $user -> can('post_image_vote');
		}*/
		$page = \App\Modules\Pages\Models\Page::first();
		$pagecontent = \BaseController::createSiderContent($page->id);
		// Show the page
		$data['sidebar_right'] = $pagecontent['sidebar_right'];
		$data['sidebar_left'] = $pagecontent['sidebar_left'];
		$data['page'] = $page;
		$data['canImageVote'] = $canImageVote;
		$data['canGalleryComment'] = $canGalleryComment;
		$data['gallery_image'] = $gallery_image;
		$data['gallery'] = $gallery;
		$data['gallery_comments'] = $gallery_comments;
		return View::make('galleries::site/galleryimage', $data);
	}
	public function postGalleryImage($galid,$imgid)
	{
		$user = $this -> user -> currentUser();
		$canGalleryComment = $user -> can('post_gallery_comment');
		if (!$canGalleryComment) {
			return Redirect::to('galleryimage/'.$galid . '/'.$imgid.'#new_comment') -> with(Lang::get('site/gallery.error'), Lang::get('site/gallery.need_to_login'));
		}

		// Declare the rules for the form validation
		$rules = array('gallcomment' => 'required|min:3');
		
		$gallery = $this -> gallery -> where('id', '=', $galid) -> first();
		$gallery_image = GalleryImage::find($imgid);

		// Check if the blog blog exists
		if (is_null($gallery) || is_null($gallery_image)) {
			// If we ended up in here, it means that
			// a page or a blog blog didn't exist.
			// So, this means that it is time for
			// 404 error page.
			return App::abort(404);
		}
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			// Save the comment
			$gallery_image_comment = new GalleryImageComment;
			$gallery_image_comment -> user_id = Auth::user() -> id;
			$gallery_image_comment -> content = Input::get('gallcomment');
			$gallery_image_comment -> gallery_id = $galid;
			$gallery_image_comment -> gallery_image_id = $imgid;
			
			// Was the comment saved with success?
			if ($gallery_image_comment -> save()) {
				// Redirect to this blog blog page
				return Redirect::to('galleryimage/'.$galid . '/'.$imgid. '#new_comment') -> with(Lang::get('site/gallery.success'), Lang::get('site/gallery.comment_added'));
			}

			// Redirect to this blog blog page
			return Redirect::to('galleryimage/'.$galid . '/'.$imgid. '#new_comment') -> with(Lang::get('site/gallery.error'), Lang::get('site/gallery.add_comment_error'));
		}

		// Redirect to this blog blog page
		return Redirect::to('galleryimage/'.$galid . '/'.$imgid) -> withInput() -> withErrors($validator);
	}

	public function contentvote()
	{
		$id = Input::get('id');
		$updown = Input::get('updown');
		$content = Input::get('content');
		$user = $this -> user -> currentUser();
		$newvalue = 0;
		$exists = ContentVote::where('content','=',$content)
							->where('idcontent','=',$id)
							->where('user_id','=',$user->id)
							->get();
		switch ($content) {
			case 'gallery':
				$item = Gallery::find($id);
				break;
			case 'galleryimage':
				$item = GalleryImage::find($id);
				break;
			case 'gallerycomment':
				$item = GalleryImageComment::find($id);
				break;			
		}		
		$newvalue = $item->voteup - $item -> votedown;
		
		if($exists->count() == 0 ){
			$contentvote = new ContentVote;
			$contentvote -> user_id = $user->id;
			$contentvote -> updown = $updown;
			$contentvote -> content = $content;
			$contentvote -> idcontent = $id;
			$contentvote -> save();
					
			if($updown=='1')
				{
					$item -> voteup = $item -> voteup + 1;
					$item -> votedown = $item -> votedown;
				}
				else {
					$item -> votedown = $item -> votedown + 1;
					$item -> voteup = $item -> voteup;
				}
			
			$item->update();					
			$newvalue = $item->voteup - $item -> votedown;						
		}
		echo $newvalue;
	}
	 
	
}