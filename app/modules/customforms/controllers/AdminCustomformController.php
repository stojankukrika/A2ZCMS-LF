<?php namespace App\Modules\Customforms\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;
use App\Modules\Customforms\Models\Customform;
use App\Modules\Customforms\Models\Customformfield;

class AdminCustomformController extends \AdminController {

	/**
	 * Custom form
	 *
	 * @var Page
	 */
	protected $customform;

	//public $restful = true;

	public function __construct(CustomForm $customform) {
		parent::__construct();
		$this -> customform = $customform;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex() {

		$title = 'Contact form management';
		
		// Grab all the custom form
		$customform = $this -> customform;

		return View::make('customforms::admin/index', compact('title', 'customform'));
	}
	
	public function getCreate() {

		$title = 'Contact form management';
		return View::make('customforms::admin/create_edit', compact('title','customform'));
	}
	

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate() {
		// Declare the rules for the form validation
		$rules = array('title' => 'required', 'message' => 'required');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			// Create a new custom form
			$user = Auth::user();

			// Update the custom form
			$this -> customform -> title = Input::get('title');
			$this -> customform -> message = Input::get('message');
			$this -> customform -> recievers = Input::get('recievers');
			$this -> customform -> user_id = $user -> id;
			
			// Was the custom form created?
			if ($this -> customform -> save()) {
					
				//add fileds to form
				if(Input::get('pagecontentorder')!=""){
					$this->saveFilds(Input::get('pagecontentorder'),Input::get('count'),$this -> customform -> id,$user -> id);
				}				
				
				// Redirect to the new custom form
				return Redirect::to('admin/customforms/' . $this -> customform -> id . '/edit') -> with('success', 'Success');
			}

			// Redirect to the custom form
			return Redirect::to('admin/customforms') -> with('error', 'Error');
		}

		// Form validation failed
		return Redirect::to('admin/customforms') -> withInput() -> withErrors($validator);
	}	

	public function getEdit($id) {

		$title = 'Contact form management';
		
		$customform = Customform::find($id);
		$customformfields = Customformfield::where('customform_id','=',$id)->get();
		
		return View::make('customforms::admin/create_edit', compact('title', 'customform','customformfields'));
	}
		
	/**
	 * Update the specified resource in storage.
	 *
	 * @param $form
	 * @return Response
	 */
	public function postEdit($id) {

		$rules = array('title' => 'required', 'message' => 'required');
		
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);
		// Check if the form validates with success
		if ($validator -> passes()) {
	
			$customform = Customform::find($id);
			$user = Auth::user();
			$customform -> title = Input::get('title');
			$customform -> message = Input::get('message');
			$customform -> recievers = Input::get('recievers');
			$customform -> user_id = $user -> id;
			
			if ($customform -> save()) {
				
				CustomFormField::where('customform_id','=',$id)->delete();
				//add fileds to form
				if(Input::get('pagecontentorder')!=""){
					$this->saveFilds(Input::get('pagecontentorder'),Input::get('count'),$id,$user -> id);
				}	
				
				return Redirect::to('admin/customforms/' . $customform -> id . '/edit') -> with('success', 'Success');
			}
		}

		// Form validation failed
		return Redirect::to('admin/customforms/' . $id . '/edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $blog
	 * @return Response
	 */
	public function getDelete($id) {
			
		$customform = Customform::find($id);
		// Was the role deleted?
		if ($customform -> delete()) {

			// Redirect to the custom form
			return Redirect::to('admin/customforms') -> with('success', 'Success');
		}
		// There was a problem deleting the custom form
		return Redirect::to('admin/customforms') -> with('error', 'Error');
	}
	
	
	/**
	 * Show a list of all the custom form formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$blogs = CustomForm::select(array('title', 'id as fields', 'id as id', 'created_at'));

		return Datatables::of($blogs) 
			-> edit_column('fields', '{{ App\Modules\Customforms\Models\Customformfield::where(\'customform_id\', \'=\', $id)->count() }}') 
			-> add_column('actions', '<a href="{{{ URL::to(\'admin/customforms/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-sm iframe" ><i class="icon-edit "></i></a>
                <a href="{{{ URL::to(\'admin/customforms/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
            ') 
            -> remove_column('id') -> make();
	}
	
	public function saveFilds($pagecontentorder,$count,$customform_id,$user_id)
	{
		$params = explode(',', $pagecontentorder);
		$order = 1;
		for ($i=0; $i <= $count*4-1; $i=$i+4) {
			 
			$customformfield = new CustomFormField;
			$customformfield -> name = $params[$i];
			$customformfield -> mandatory = $params[$i+1];
			$customformfield -> type = $params[$i+2];
			$customformfield -> options = $params[$i+3];
			$customformfield -> order = $order;
			$customformfield -> customform_id = $customform_id;
			$customformfield -> user_id = $user_id;						
			$customformfield -> save();	
			$order++;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postDeleteItem($formId) {
		
		$customformfield = Customformfield::find(Input::get('id'));
		if ($customformfield -> delete()) {
			return 0;
		}
		return 1;		
	}	
}