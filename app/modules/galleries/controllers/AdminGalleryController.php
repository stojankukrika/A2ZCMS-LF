<?php namespace App\Modules\Galleries\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,File,Fineuploader,Thumbnail,SplFileInfo,Response;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryImage;
use App\Modules\Galleries\Models\GalleryImageComment;

class AdminGalleryController extends \AdminController {

	/**
	 * Gallery Model
	 * @var Gallery
	 */
	protected $gallery;
	protected $gallery_image;

	/**
	 * Inject the models.
	 * @param Gallery $post
	 */
	public function __construct(Gallery $gallery, GalleryImage $gallery_image,\AdminController $admin) {
		parent::__construct();
		if (!array_key_exists('manage_galleries',$admin->roles)){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit ;
		}
		$this -> gallery = $gallery;
		$this -> gallery_image = $gallery_image;
	}

	/**
	 * Show a list of all the gallery posts.
	 *
	 * @return View
	 */
	public function getIndex() {
		// Title
		$title = 'Gallery management';

		// Grab all the gallery posts
		$galleries = $this -> gallery;

		// Show the page
		return View::make('galleries::admin/galleries/index', compact('galleries', 'title'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate() {
		// Title
		$title = 'Create a new gallery';

		// Show the page
		return View::make('galleries::admin/galleries/create_edit', compact('title'));
	}

	public function postCreate() {
		// Declare the rules for the form validation
		$rules = array('title' => 'required|min:3|max:250');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {

			// Create a new gallery
			$user = Auth::user();
			$this -> gallery -> title = Input::get('title');
			$this -> gallery -> start_publish = (Input::get('start_publish') == '') ? date('Y-m-d') : Input::get('start_publish');
			$this -> gallery -> end_publish = (Input::get('end_publish') == '') ? null : Input::get('end_publish');
			$this -> gallery -> user_id = $user -> id;
			$this -> gallery -> folderid = sha1($this -> gallery -> title . $this -> gallery -> start_publish);
			// Was the gallery created?
			if ($this -> gallery -> save()) {
				File::makeDirectory(public_path() . '\gallery\/' . $this -> gallery -> folderid);
				File::makeDirectory(public_path() . '\gallery\/' . $this -> gallery -> folderid . '\/thumbs');

				// Redirect to the new gallery post page
				return Redirect::to('admin/galleries/' . $this -> gallery -> id . '/edit') -> with('success', 'Success');
			}

			// Redirect to the gallery create page
			return Redirect::to('admin/galleries/create') -> with('error', 'Error');
		}

		// Form validation failed
		return Redirect::to('admin/galleries/create') -> withInput() -> withErrors($validator);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $blog
	 * @return Response
	 */
	public function getEdit($id) {
		
		$galleries = Gallery::find($id);
		// Title
		$title = 'Gallery update';

		// Show the page
		return View::make('galleries::admin/galleries/create_edit', compact('galleries', 'title'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $id
	 * @return Response
	 */
	public function postEdit($id) {

		// Declare the rules for the form validation
		$rules = array('title' => 'required|min:3|max:250');

		$validator = Validator::make(Input::all(), $rules);

		$gallery = Gallery::find($id);

		$inputs = Input::all();

		// Check if the form validates with success
		if ($validator -> passes()) {
			// Was the page updated?
			if ($gallery -> update($inputs)) {
				// Redirect to the new gallery
				return Redirect::to('admin/galleries/' . $galleries -> id . '/edit') -> with('success', 'Success');
			}

			// Redirect to the gallery
			return Redirect::to('admin/galleries/' . $galleries -> id . '/edit') -> with('error', 'Error');
		}

		// Form validation failed
		return Redirect::to('admin/galleries/' . $galleries -> id . '/edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $blog
	 * @return Response
	 */
	public function getDelete($id) {
		
		$gallery = Gallery::find($id);
		// Was the role deleted?
		if ($gallery -> delete()) {
			// Redirect to the gallery
			return Redirect::to('admin/galleries') -> with('success', 'Success');
		}
		// There was a problem deleting the gallery
		return Redirect::to('admin/galleries') -> with('error', 'Error');
	}

	/**
	 * Show a list of all the gallery formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$galleries = Gallery::select(array('galleries.id', 'galleries.title', 'galleries.id as images_count', 'galleries.id as comments_count', 'galleries.created_at'));

		return Datatables::of($galleries) -> edit_column('images_count', '<a href="{{{ URL::to(\'admin/galleries/\' . $id . \'/imagesforgallery\' ) }}}" class="btn btn-link btn-sm" >{{ DB::table(\'gallery_images\')->where(\'gallery_id\', \'=\', $id)->where(\'deleted_at\', \'=\', NULL)->count() }}</a>') 
			-> edit_column('comments_count', '<a href="{{{ URL::to(\'admin/galleries/galleryimagecomments/\' . $id . \'/commentsforgallery\' ) }}}" class="btn btn-link btn-sm" >{{ App\Modules\Galleries\Models\GalleryImageComment::where(\'gallery_id\', \'=\', $id)->where(\'deleted_at\', \'=\', NULL)->count() }}</a>') 
			-> add_column('actions', '<a href="{{{ URL::to(\'admin/galleries/\' . $id . \'/upload\' ) }}}" class="btn btn-info btn-sm iframe" ><i class="icon-picture "></i></a>
        		<a href="{{{ URL::to(\'admin/galleries/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-sm iframe" ><i class="icon-edit "></i></a>
                <a href="{{{ URL::to(\'admin/galleries/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> make();
	}

	/*
	 * Get upload pictures for gallery
	 * */
	public function getUpload($id) {
		
		$galleries = Gallery::find($id);
		// Title
		$title = 'Add picture';
		// Show the page
		return View::make('galleries::admin/galleries/upload', compact('galleries', 'title'));
	}

	/*
	 * Upload pictures for gallery
	 * */
	public function postUpload() {
		$rules = array('gid' => 'required|integer', 'qqfile' => 'required|image|max:3000', );
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {

			$id = Input::get('gid');

			$galleries = Gallery::find($id);

			$path = public_path() . '\gallery\\/' . $galleries -> folderid;
			Fineuploader::init($path);

			$name = Fineuploader::getName();

			$info = new SplFileInfo($name);
			$extension = $info -> getExtension();

			$name = sha1($name . $galleries -> folderid . time()) . '.' . $extension;

			$user = Auth::user();
			$this -> gallery_image -> gallery_id = $id;
			$this -> gallery_image -> content = $name;
			$this -> gallery_image -> user_id = $user -> id;
			$this -> gallery_image -> save();

			Fineuploader::upload($name);

			$path2 = public_path() . '\gallery\\/' . $galleries -> folderid . '\thumbs\/';
			Fineuploader::init($path2);
			$upload_success = Fineuploader::upload($name);

			Thumbnail::generate_image_thumbnail($path2 . $name, $path2 . $name);

			return Response::json($upload_success);
		} else {
			return Response::json('error', 400);
		}
	}

	public function getImagesForGallery($id) {
		// Title
		$title = 'Gallery management for category';
		$galleries = Gallery::find($id);
		// Show the page
		return View::make('galleries::admin/galleries/imagesforgallery', compact('galleries', 'title'));
	}
}
