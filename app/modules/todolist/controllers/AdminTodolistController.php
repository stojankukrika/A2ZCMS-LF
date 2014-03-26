<?php namespace App\Modules\Todolist\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Todolist\Models\Todolist;

class AdminTodolistController extends \AdminController{

	/**
	 * Post Model
	 * @var Post
	 */
	protected $todolist;
	/**
	 * Inject the models.
	 * @param Post $post
	 */
	public function __construct(Todolist $todolist,\AdminController $admin) {
		parent::__construct();
		if (!array_key_exists('manage_todolists',$admin->roles)){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit ;
		}
		$this -> todolist = $todolist;
	}

	/**
	 * Show a list of all the todo list.
	 *
	 * @return View
	 */
	public function getIndex() {
		// Title
		$title = 'To do management';
		// Show the page
		return View::make('todolist::admin/index', compact('title'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate() {
		// Title
		$title = 'Create a new to do';

		// Show the page
		return View::make('todolist::admin/create_edit', compact('title'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate() {
		// Declare the rules for the form validation
		$rules = array('content' => 'required');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			$user = Auth::user();
			
			$this -> todolist -> title = Input::get('title');
			$this -> todolist -> content = Input::get('content');
			$this -> todolist -> finished = Input::get('finished');
			$this -> todolist -> work_done = (Input::get('finished')==100.00)?'1':'0';
			$this -> todolist -> user_id = $user -> id;
			// create todo list
			$this -> todolist -> save();
			
		}
		// Form validation failed
		return Redirect::to('admin/todolist') -> withInput() -> withErrors($validator);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $blog_category
	 * @return Response
	 */
	public function getEdit($id) {
		// Title
		$title = 'To do update';
		$todolist = Todolist::find($id);
		// Show the page
		return View::make('todolist::admin/create_edit', compact('todolist', 'title'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $blog_category
	 * @return Response
	 */
	public function postEdit($id) {
	
		// Declare the rules for the form validation
		$rules = array('content' => 'required');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		$todolist = Todolist::find($id);

		$inputs = Input::all();

		// Check if the form validates with success
		if ($validator -> passes()) {
				$user = Auth::user();
				$todolist -> title = Input::get('title');
				$todolist -> content = Input::get('content');
				$todolist -> finished = Input::get('finished');
				$todolist -> user_id = $user -> id;
				$todolist -> work_done = (Input::get('finished')==100.00)?'1':'0';
				
			if (!$todolist -> save()) {
				return Redirect::to('admin/todolist/' . $todolist -> id . '/edit') -> with('error', Lang::get('admin/todolists/messages.update.error'));
			}
		// Form validation failed
		return Redirect::to('admin/todolist/' . $todolist -> id . '/edit') -> withInput() -> withErrors($validator);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $comment
	 * @return Response
	 */
	public function getDelete($id) {
		
		$todo_list = Todolist::find($id);
		// Was the role deleted?
		if ($todo_list -> delete()) {
			// Redirect to the role management page
			return Redirect::to('admin/todolist') -> with('success', Lang::get('admin/todolists/messages.delete.success'));
		}

		// There was a problem deleting the role
		return Redirect::to('admin/todolist') -> with('error', Lang::get('admin/todolists/messages.delete.error'));
	}

	/** Change to-do to work ore done
	 * @param $todolist
	 * @return Redirect
	 * */
	public function getChange($id) {

		$this -> todolist = Todolist::find($id);
		$this -> todolist -> work_done = ($this -> todolist -> work_done + 1) % 2;
		$this -> todolist -> finished = $this -> todolist -> work_done *100.00;
		$this -> todolist -> save();

		// Form validation failed
		return Redirect::to('admin/todolist');

	}

	/**
	 * Show a list of all the comments formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$todolists = Todolist::select(array('todolists.id', 'todolists.title', 'todolists.work_done','todolists.finished', 'todolists.created_at'))->where('user_id','=',Auth::user()->id);

		return Datatables::of($todolists) -> edit_column('work_done', '@if ($work_done==0){{ "Work" }} @else {{ "Done" }} @endif') 
		-> add_column('actions', '<a href="{{{ URL::to(\'admin/todolist/\' . $id . \'/change\' ) }}}" class="btn btn-link btn-sm" ><i class="icon-retweet "></i></a>
        <a href="{{{ URL::to(\'admin/todolist/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-sm iframe" ><i class="icon-edit "></i></a>
                <a href="{{{ URL::to(\'admin/todolist/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') -> remove_column('id') -> make();
	}
}
