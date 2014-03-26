<?php namespace App\Modules\Galleries\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryImage;
use App\Modules\Galleries\Models\GalleryImageComment;

class AdminGalleryImageCommentController extends \AdminController {

	/**
	 * Comment Model
	 * @var Comment
	 */
	protected $gallery_comment;

	/**
	 * Inject the models.
	 * @param Comment $comment
	 */
	public function __construct(GalleryImageComment $gallery_comment,\AdminController $admin) {
		parent::__construct();
		if (!array_key_exists('manage_gallery_imagecomments',$admin->roles)){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit ;
		}
		$this -> gallery_comment = $gallery_comment;
	}

	/**
	 * Show a list of all the comment images.
	 *
	 * @return View
	 */
	public function getIndex() {
		// Title
		$title = 'Comment management';

		// Grab all the comment posts
		$gallery_comment = $this -> gallery_comment;

		// Show the page
		return View::make('galleries::admin/galleryimagecomments/index', compact('gallery_comment', 'title'));
	}

	/**
	 * Show a list of all the comment for the selected gallery.
	 *
	 * @return View
	 */
	public function getCommentsforgallery($id) {
		// Title
		$title = 'Comment management for gallery';
		$gallery = Gallery::find($id);
		// Show the page
		return View::make('galleries::admin/galleryimagecomments/commentsforgallery', compact('title', 'gallery'));
	}

	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $comment
	 * @return Response
	 */
	public function getDelete($gallery_comment) {
		
			$id = $gallery_comment->id;
			$gallerycomment = GalleryImageComment::find($id);
			// Was the role deleted?
			if ($gallerycomment -> delete()) {
				// Redirect to the comment posts management page
				return Redirect::to('admin/galleries/galleryimagecomments') -> with('success', Lang::get('admin/galleryimagecomments/messages.delete.success'));
			}
		// There was a problem deleting the comment post
		return Redirect::to('admin/galleries/galleryimagecomments') -> with('error', Lang::get('admin/galleryimagecomments/messages.delete.error'));
	}


	/**
	 * Show a list of all the comments formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$comments = GalleryImageComment::join('galleries', 'galleries.id', '=', 'gallery_images_comments.gallery_id') -> join('users', 'users.id', '=', 'gallery_images_comments.user_id') -> select(array('gallery_images_comments.id as id', 'gallery_images_comments.content as post', 'galleries.title as gallerytitle', 'galleries.id as galleryid', 'users.id as userid', 'users.username as poster_name', 'gallery_images_comments.created_at'));

		return Datatables::of($comments) -> edit_column('gallerytitle', '<a href="{{{ URL::to(\'admin/galleries/\'. $galleryid .\'/edit\') }}}" class="iframe cboxElement">{{{ Str::limit($gallerytitle, 40, \'...\') }}}</a>') 
				-> edit_column('post', '<a href="{{{ URL::to(\'admin/galleries/\'. $galleryid .\'/edit\') }}}" class="iframe cboxElement">{{{ Str::limit($post, 40, \'...\') }}}</a>') 
				-> edit_column('poster_name', '<a href="{{{ URL::to(\'admin/users/\'. $userid .\'/edit\') }}}" class="iframe cboxElement">{{{ $poster_name }}}</a>') 
				-> add_column('actions', '<a href="{{{ URL::to(\'admin/galleryimagecomments/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> remove_column('galleryid') -> remove_column('userid') -> make();
	}

	/**
	 * Show a list of all the blog comments for selected blog formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getDataforgallery($gallery_id) {
		$comments = GalleryImageComment::join('users', 'gallery_images_comments.user_id', '=', 'users.id') -> join('galleries', 'gallery_images_comments.gallery_id', '=', 'galleries.id') -> where('gallery_images_comments.gallery_id', '=', $gallery_id) -> where('gallery_images_comments.deleted_at', '=', NULL) -> select(array('gallery_images_comments.id as id', 'gallery_images_comments.gallery_id as gallery_id', 'gallery_images_comments.content as comment', 'galleries.title as content', 'users.id as userid', 'users.username as poster_name', 'gallery_images_comments.created_at'));

		return Datatables::of($comments) -> edit_column('content', '<a href="{{{ URL::to(\'admin/galleries/\'. $gallery_id .\'/edit\') }}}" class="iframe cboxElement">{{{ Str::limit($content, 40, \'...\') }}}</a>') 
				-> edit_column('comment', '<a href="{{{ URL::to(\'admin/galleryimagecomments/\'. $id .\'/edit\') }}}" class="iframe cboxElement">{{{ Str::limit($comment, 40, \'...\') }}}</a>') 
				-> edit_column('poster_name', '<a href="{{{ URL::to(\'admin/users/\'. $userid .\'/edit\') }}}" class="iframe cboxElement">{{{ $poster_name }}}</a>') 
				-> add_column('actions', '<a href="{{{ URL::to(\'admin/galleryimagecomments/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-default btn-sm"><i class="icon-edit "></i></a>
                <a href="{{{ URL::to(\'admin/galleryimagecomments/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> remove_column('gallery_id') -> remove_column('userid') -> make();
	}

}
