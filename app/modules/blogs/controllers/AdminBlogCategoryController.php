<?php namespace App\Modules\Blogs\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Blogs\Models\Blog;
use App\Modules\Blogs\Models\BlogBlogCategory;
use App\Modules\Blogs\Models\BlogCategory;
use App\Modules\Blogs\Models\BlogComment;

class AdminBlogCategoryController extends \AdminController {

	/**
	 * BlogCategory Model
	 * @var BlogCategory
	 */
	protected $blog_category;

	/**
	 * Inject the models.
	 * @param BlogCategory $blog_category
	 */
	public function __construct(BlogCategory $blog_category) {
		if (!Session::get('manage_blog_categris')){
			URL::to($_SERVER['HTTP_REFERER']);
		}
		parent::__construct();
		$this -> blog_category = $blog_category;
	}

	/**
	 * Show a list of all the blog_category posts.
	 *
	 * @return View
	 */
	public function getIndex() {
		// Title
		$title = 'Category management';

		// Grab all the blog_category posts
		$blogcategorys = $this -> blog_category;

		// Show the page
		return View::make('blogs::admin/blogcategorys/index', compact('blogcategorys', 'title'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate() {
		// Title
		// Title
		$title = 'Create a new category';

		// Show the page
		return View::make('blogs::admin/blogcategorys/edit', compact('title'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate() {
		// Declare the rules for the form validation
		$rules = array('title' => 'required|min:3');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			// Update the blog post data
			$this -> blog_category -> title = Input::get('title');
			// Was the blog post created?
			if ($this -> blog_category -> save()) {
				// Redirect to the new blog post page
				return Redirect::to('admin/blogs/blogcategorys/' . $this -> blog_category -> id . '/edit') -> with('success', 'Success');
			}

			// Redirect to the blog post create page
			return Redirect::to('admin/blogs/blogcategorys/create') -> with('error', 'Error');
		}

		// Form validation failed
		return Redirect::to('admin/blogs/blogcategorys/create') -> withInput() -> withErrors($validator);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $blog_category
	 * @return Response
	 */
	public function getEdit($id) {
		$blog_category = BlogCategory::find($id);
		// Title
		$title = 'Category update';

		// Show the page
		return View::make('blogs::admin/blogcategorys/edit', compact('blog_category', 'title'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $blog_category
	 * @return Response
	 */
	public function postEdit($id) {
		// Declare the rules for the form validation
		$rules = array('title' => 'required|min:3');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		$blogcategory = BlogCategory::find($id);

		$inputs = Input::all();

		// Check if the form validates with success
		if ($validator -> passes()) {
			// Was the page updated?
			if ($blogcategory -> update($inputs)) {
				// Redirect to the new blog_category post page
				return Redirect::to('admin/blogs/blogcategorys/' . $blog_category -> id . '/edit') -> with('success', Lang::get('admin/blogcategorys/messages.update.success'));
			}

			// Redirect to the comments post management page
			return Redirect::to('admin/blogs/blogcategorys/' . $blog_category -> id . '/edit') -> with('error', Lang::get('admin/blogcategorys/messages.update.error'));
		}

		// Form validation failed
		return Redirect::to('admin/blog/blogcategorys/' . $blog_category -> id . '/edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $comment
	 * @return Response
	 */
	public function getDelete($id) {
		
		$blogcategory = BlogCategory::find($id);
		// Was the role deleted?
		if ($blogcategory -> delete()) {
			// Redirect to the comment posts management page
			return Redirect::to('admin/blogs/blogcategorys') -> with('success', 'Success');
		}
		// There was a problem deleting the comment post
		return Redirect::to('admin/blogs/blogcategorys') -> with('error', 'Error');
	}


	/**
	 * Show a list of all the comments formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$blogcategorys = BlogCategory::select(array('blog_categories.id', 'blog_categories.title', 'blog_categories.id as blog_count', 'blog_categories.created_at'));

		return Datatables::of($blogcategorys) -> edit_column('blog_count', '<a href="{{{ URL::to(\'admin/blogs/\' . $id . \'/blogsforcategory\' ) }}}" class="btn btn-link btn-sm" >{{ App\Modules\Blogs\Models\BlogBlogCategory::where("blog_category_id", "=", $id)->count() }}</a>') 
				-> add_column('actions', '<a href="{{{ URL::to(\'admin/blogs/blogcategorys/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-sm iframe" ><i class="icon-edit "></i></a>
                <a href="{{{ URL::to(\'admin/blogs/blogcategorys/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> make();
	}

}
