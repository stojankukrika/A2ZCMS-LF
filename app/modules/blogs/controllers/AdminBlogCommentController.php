<?php namespace App\Modules\Blogs\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Blogs\Models\Blog;
use App\Modules\Blogs\Models\BlogBlogCategory;
use App\Modules\Blogs\Models\BlogCategory;
use App\Modules\Blogs\Models\BlogComment;

class AdminBlogCommentController extends \AdminController {

	/**
	 * Comment Model
	 * @var Comment
	 */
	protected $blog_comment;

	/**
	 * Inject the models.
	 * @param Comment $comment
	 */
	public function __construct(BlogComment $blog_comment) {
		parent::__construct();
		$this -> blog_comment = $blog_comment;
	}

	/**
	 * Show a list of all the comment blogs.
	 *
	 * @return View
	 */
	public function getIndex() {
		// Title
		$title = 'Comment management';

		// Grab all the comment posts
		$blog_comment = $this -> blog_comment;

		// Show the page
		return View::make('blogs::admin/blogcomments/index', compact('blog_comment', 'title'));
	}

	/**
	 * Show a list of all the comment for the selected blog.
	 *
	 * @return View
	 */
	public function getCommentsForBlog($id) {
		$blog = Blog::find($id);
		// Title
		$title = 'Comment management for blog';

		// Show the page
		return View::make('blogs::admin/blogcomments/commentsforblog', compact('title', 'blog'));
	}

	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $comment
	 * @return Response
	 */
	public function getDelete($id) {
		
		$blogcomment = BlogComment::find($id);
		// Was the role deleted?
		if ($blogcomment -> delete()) {
			// Redirect to the comment posts management page
			return Redirect::to('admin/blogs/blogcomments') -> with('success', 'Success');
		}
		// There was a problem deleting the comment post
		return Redirect::to('admin/blogs/blogcomments') -> with('error', 'Error');
	}

	/**
	 * Show a list of all the comments formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$comments = BlogComment::join('blogs', 'blogs.id', '=', 'blog_comments.blog_id') -> join('users', 'users.id', '=', 'blog_comments.user_id') -> select(array('blog_comments.id as id', 'blogs.id as blogid', 'users.id as userid', 'blog_comments.content', 'blogs.title as post_name', 'users.username as poster_name', 'blog_comments.created_at'));

		return Datatables::of($comments) -> edit_column('content', '<a href="{{{ URL::to(\'admin/blogs/blogcomments/\'. $id .\'/edit\') }}}" class="iframe cboxElement">{{{ Str::limit($content, 40, \'...\') }}}</a>') 
			-> edit_column('poster_name', '<a href="{{{ URL::to(\'admin/users/\'. $userid .\'/edit\') }}}" class="cboxElement">{{{ $poster_name }}}</a>') -> add_column('actions', '<a href="{{{ URL::to(\'admin/blogs/blogcomments/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> remove_column('blogid') -> remove_column('userid') -> make();
	}

	/**
	 * Show a list of all the blog comments for selected blog formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getDataforblog($blog_id) {
		$comments = BlogComment::join('blogs', 'blogs.id', '=', 'blog_comments.blog_id') -> join('users', 'users.id', '=', 'blog_comments.user_id') -> where('blogs.id', '=', $blog_id) -> select(array('blog_comments.id as id', 'blogs.id as postid', 'users.id as userid', 'blog_comments.content', 'users.username as poster_name', 'blog_comments.created_at'));

		return Datatables::of($comments) -> edit_column('content', '<a href="{{{ URL::to(\'admin/blogs/blogcomments/\'. $id .\'/edit\') }}}" class="iframe cboxElement">{{{ Str::limit($content, 40, \'...\') }}}</a>') 
			-> edit_column('poster_name', '<a href="{{{ URL::to(\'admin/users/\'. $userid .\'/edit\') }}}" class="iframe cboxElement">{{{ $poster_name }}}</a>') 
			-> add_column('actions', '<a href="{{{ URL::to(\'admin/blogs/blogcomments/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> remove_column('postid') -> remove_column('userid') -> make();
	}

}
