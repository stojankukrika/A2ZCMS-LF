<?php namespace App\Modules\Pages\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Str;
use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Models\Navigation;
use App\Modules\Pages\Models\NavigationGroup;
use App\Modules\Pages\Models\PagePluginFunction;
use App\Modules\Pages\Models\PluginFunction;

class AdminNavigationGroupController extends \AdminController {

	/**
	 * Navigation_group Repository
	 *
	 * @var Navigation_group
	 */
	protected $navigationGroup;

	public function __construct(NavigationGroup $navigationGroup,\AdminController $admin) {
		parent::__construct();
		if (!array_key_exists('manage_navigation_groups',$admin->roles)){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit ;
		}
		$this -> navigationGroup = $navigationGroup;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex() {
		$title = 'Navigation group management';
		$navigationGroups = $this -> navigationGroup -> all();

		return View::make('pages::admin/navigationgroups/index', compact('title', 'navigationGroups'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate() {
		$title = 'Create a new navigation group';

		// Show the navigation group
		return View::make('pages::admin/navigationgroups/create_edit', compact('title'));
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
			// Create a new blog post
			$this -> navigationGroup -> title = Input::get('title');
			$this -> navigationGroup -> slug = Str::slug(Input::get('title'));
			$this -> navigationGroup -> showmenu = Input::get('showmenu');
			$this -> navigationGroup -> showfooter = Input::get('showfooter');
			$this -> navigationGroup -> showsidebar = Input::get('showsidebar');
			
			$this -> navigationGroup -> save();

			if ($this -> navigationGroup -> id) {
				// Redirect to the new navigationGroup
				return Redirect::to('admin/pages/navigationgroups/' . $this -> navigationGroup -> id . '/edit') -> with('success', 'Success');
			} 
			else {
				// Get validation errors (see Ardent package)
				$error = $this -> navigationGroup -> errors() -> all();

				return Redirect::to('admin/pages/navigationgroups/create') -> with('error', $error);
			}
		}
		// Form validation failed
		return Redirect::to('admin/pages/navigationgroups/create') -> withInput() -> withErrors($validator);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $id
	 * @return Response
	 */
	public function getEdit($id) {
		if ($id) {
			$navigationGroup = NavigationGroup::find($id);
			// Title
			$title = 'Navigation group update';
			// mode
			$mode = 'edit';

			return View::make('pages::admin/navigationgroups/create_edit', compact('navigationGroup', 'title', 'mode'));
		} else {
			return Redirect::to('admin/pages/navigationgroups') -> with('error', 'Does not exist');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $navigationGroup
	 * @return Response
	 */
	public function postEdit($id) {
		// Declare the rules for the form validation
		$rules = array('title' => 'required|min:3');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		$navigationGroup = NavigationGroup::find($id);

		$inputs = Input::all();

		// Check if the form validates with success
		if ($validator -> passes()) {
			$navigationGroup -> slug = Str::slug(Input::get('title'));
			// Was the page updated?
			if ($navigationGroup -> update($inputs)) {
				// Redirect to the navigationGroup navigationGroup
				return Redirect::to('admin/pages/navigationgroups/' . $navigationGroup -> id . '/edit') -> with('success', 'Success');
			} else {
				// Redirect to the navigationGroup navigationGroup
				return Redirect::to('admin/pages/navigationgroups/' . $navigationGroup -> id . '/edit') -> with('error', 'Error');
			}
		}

		// Form validation failed
		return Redirect::to('admin/pages/navigationgroups/' . $navigationGroup -> id . '/edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Remove the specified user from storage.
	 *
	 * @param $role
	 * @internal param $id
	 * @return Response
	 */
	public function getDelete($id) {
		$navigationGroup = NavigationGroup::find($id);
		// Was the role deleted?
		if ($navigationGroup -> delete()) {
			// Redirect to the role management page
			return Redirect::to('admin/pages/navigationgroups') -> with('success', 'Success');
		}

		// There was a problem deleting the role
		return Redirect::to('admin/pages/navigationgroups') -> with('error', 'Error');
	}

	/**
	 * Show a list of all the pages formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$navs = NavigationGroup::select(array('navigation_groups.id', 'navigation_groups.title','navigation_groups.slug','navigation_groups.created_at'));

		return Datatables::of($navs) -> add_column('actions', '<a href="{{{ URL::to(\'admin/pages/navigationgroups/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-default btn-sm"><i class="icon-edit "></i></a>
                               <a href="{{{ URL::to(\'admin/pages/navigationgroups/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
                               
            ') -> remove_column('id') -> make();
	}

}
