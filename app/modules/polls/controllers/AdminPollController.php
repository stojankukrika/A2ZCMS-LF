<?php namespace App\Modules\Polls\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Polls\Models\Poll;
use App\Modules\Polls\Models\Polloption;

class AdminPollController extends \AdminController {
		
	/**
	 * Poll
	 *
	 * @var poll
	 */
	protected $poll;
	public function __construct(Poll $poll) {
		parent::__construct();
		$this -> poll = $poll;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex() {

		$title = 'Poll management';
		
		// Grab all the custom form
		$poll = $this -> poll;

		return View::make('polls::admin/index', compact('title', 'poll'));
	}
	
	public function getCreate() {

		$title = 'Contact form management';
		return View::make('polls::admin/create_edit', compact('title','customform'));
	}
	

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate() {
		// Declare the rules for the poll
		$rules = array('title' => 'required');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			// Create a new poll
			$user = Auth::user();

			// Update the poll
			$this -> poll -> title = Input::get('title');
			$this -> poll -> active = Input::get('active');
			$this -> poll -> user_id = $user -> id;
			
			// Was the poll created?
			if ($this -> poll -> save()) {
					
				//add fileds to form
				if(Input::get('pagecontentorder')!=""){
					$this->saveFilds(Input::get('pagecontentorder'),Input::get('count'),$this -> poll -> id,$user -> id);
				}				
			}
			// Redirect to the poll
			return Redirect::to('admin/polls') -> with('error', 'Error');
		}

		// Form validation failed
		return Redirect::to('admin/polls') -> withInput() -> withErrors($validator);
	}	

	public function getEdit($id) {

		$title = 'Polls management';
		
		$poll = Poll::find($id);
		$polloption = Polloption::where('poll_id','=',$id)->get();
		$poll_options_count =$polloption->count();
		
		return View::make('polls::admin/create_edit', compact('title', 'poll','polloption','poll_options_count'));
	}
		
	/**
	 * Update the specified resource in storage.
	 *
	 * @param $form
	 * @return Response
	 */
	public function postEdit($id) {

		$rules = array('title' => 'required');
		
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);
		// Check if the form validates with success
		if ($validator -> passes()) {
	
			$poll = Poll::find($id);
			$user = Auth::user();
			$poll -> title = Input::get('title');
			$poll -> active = Input::get('active');
			$poll -> user_id = $user -> id;
			
			if ($poll -> save()) {
				
				Polloption::where('poll_id','=',$id)->delete();
				//add fileds to form
				if(Input::get('pagecontentorder')!=""){
					$this->saveFilds(Input::get('pagecontentorder'),Input::get('count'),$id,$user -> id);
				}	
				
				return Redirect::to('admin/polls/' . $poll -> id . '/edit') -> with('success', 'Success');
			}
		}

		// Form validation failed
		return Redirect::to('admin/polls/' . $id . '/edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $blog
	 * @return Response
	 */
	public function getDelete($id) {
			
		$poll = Poll::find($id);
		// Was the role deleted?
		if ($poll -> delete()) {

			// Redirect to the custom form
			return Redirect::to('admin/polls') -> with('success', 'Success');
		}
		// There was a problem deleting the custom form
		return Redirect::to('admin/polls') -> with('error', 'Error');
	}
	
	
	/**
	 * Show a list of all the custom form formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$polls = Poll::select(array('title', 'id as fields', 'id as id', 'created_at'));

		return Datatables::of($polls) 
			-> edit_column('fields', '{{ App\Modules\Polls\Models\Polloption::where(\'poll_id\', \'=\', $id)->count() }}') 
			-> add_column('actions', '<a href="{{{ URL::to(\'admin/polls/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-sm iframe" ><i class="icon-edit "></i></a>
                <a href="{{{ URL::to(\'admin/polls/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') 
            -> remove_column('id') -> make();
	}
	
	public function saveFilds($pagecontentorder,$count,$poll_id,$user_id)
	{
		$params = explode(',', $pagecontentorder);
		$order = 1;
		for ($i=0; $i <= $count*2-1; $i=$i+2) {
			$polloption = new Polloption;
			$polloption->title = $params[$i];
			$polloption->order = $order;
			$polloption->poll_id = $poll_id;
			$polloption -> save();	
			$order++;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postDeleteItem($formId) {
		
		$oppoption = Polloption::find(Input::get('id'));
		if ($oppoption -> delete()) {
			return 0;
		}
		return 1;		
	}	
}
	