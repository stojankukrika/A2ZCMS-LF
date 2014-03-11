<?php namespace App\Modules\Users\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Confide;

use App\Modules\Users\Models\User;
use App\Modules\Users\Models\AssignedRoles;
use App\Modules\Roles\Models\Role;
use App\Modules\Roles\Models\Permission;

class AdminUserController extends \AdminController {

	/**
	 * User Model
	 * @var User
	 */
	public function __construct(User $user) {
		parent::__construct();
		$this -> user = $user;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex() {
		
		// Grab all the users
		$users = $this -> user;
		
		$title = "Users menagement";
		// Show the page
		return View::make('users::admin/index', compact('users', 'title'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getUsersForRole($user_role) {
		// Title
		$title = "User management for role";

		// Show the page
		return View::make('users::admin/usersforrole', compact('title', 'user_role'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate() {
		$roles = Role::all();
		// Selected groups
		$selectedRoles = Input::old('roles', array());
		// Selected permissions
		$mode = 'create';
		$title  = "Add new user";
		return View::make('users::admin/create_edit', compact('roles', 'mode','selectedRoles', 'title'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate() {
		
		$rules = array(
		'name' => 'required|min:4',
		'surname' => 'required|min:4',
        'username' => 'required|alpha_dash|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|between:4,11|confirmed',
        'password_confirmation' => 'between:4,11',
    );
	$validator = Validator::make(Input::all(), $rules);

		if ($validator -> passes()) {
	
			$this -> user -> name = Input::get('name');
			$this -> user -> surname = Input::get('surname');
			$this -> user -> username = Input::get('username');
			$this -> user -> email = Input::get('email');
			$this -> user -> password = Input::get('password');
			// The password confirmation will be removed from model
			// before saving. This field will be used in Ardent's
			// auto validation.
			$this -> user -> confirmation_code = Input::get('password');
			$this -> user -> confirmed = Input::get('confirm');
			
			// Save if valid. Password field will be hashed before save
			$this -> user -> save();
	
			if ($this -> user -> id) {
				// Save roles. Handles updating.			
				foreach(Input::get('roles') as $item)
				{
					$role = new AssignedRoles;
					$role -> role_id = $item;
					$role -> user_id = $this -> user -> id;
					$role -> save();
				}
			}
		} else {
			// Get validation errors (see Ardent package)
			$error = $this -> user -> errors() -> all();

			return Redirect::to('admin/users/create') -> withInput(Input::except('password')) -> with('error', $error);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $user
	 * @return Response
	 */
	public function getEdit($id) {
		
			$user = User::find($id);
			$roles = Role::all();
			$selectedRoles = AssignedRoles::where('user_id','=',$user->id)->lists('role_id');
			// Title
			$title = 'Update user';
			// mode
			$mode = 'edit';

			return View::make('users::admin/create_edit', compact('user', 'roles', 'selectedRoles', 'title', 'mode'));
		}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $user
	 * @return Response
	 */
	public function postEdit($id) {
		// Validate the inputs
		$user = User::find($id);
		
		$rules = array(
		'name' => 'required|min:4',
		'surname' => 'required|min:4',
        'password' => 'between:4,11|confirmed',
        'password_confirmation' => 'between:4,11',
    	);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator -> passes()) {
			$oldUser = clone $user;
			$user -> name = Input::get('name');
			$user -> surname = Input::get('surname');
			$user -> username = Input::get('username');
			$user -> email = Input::get('email');
			$user -> confirmed = Input::get('confirm');

			$password = Input::get('password');
			$passwordConfirmation = Input::get('password_confirmation');

			if (!empty($password)) {
				if ($password === $passwordConfirmation) {
					$user -> password = $password;
					// The password confirmation will be removed from model
					// before saving. This field will be used in Ardent's
					// auto validation.
					$user -> password_confirmation = $passwordConfirmation;
				} else {
					// Redirect to the new user page
					return Redirect::to('admin/users/' . $user -> id . '/edit') -> with('error', Lang::get('admin/users/messages.password_does_not_match'));
				}
			} else {
				unset($user -> password);
				unset($user -> password_confirmation);
			}

			if ($user -> confirmed == null) {
				$user -> confirmed = $oldUser -> confirmed;
			}

			// Save if valid. Password field will be hashed before save
			$user -> update();
			
			AssignedRoles::where('user_id','=',$user->id)->delete();
			
			// Save roles. Handles updating.
			foreach(Input::get('roles') as $item)
			{
				$role = new AssignedRoles;
				$role -> role_id = $item;
				$role -> user_id = $user -> id;
				$role -> save();
			}
		}
	}

	/**
	 * Remove user page.
	 *
	 * @param $user
	 * @return Response
	 */
	public function getDelete($id) {
		$user = User::find($id);
		// Check if we are not trying to delete ourselves
		if ($user -> id === Confide::user() -> id) {
			// Redirect to the user management page
			return Redirect::to('admin/users') -> with('error', Lang::get('admin/users/messages.delete.impossible'));
		}

		AssignedRoles::where('user_id', $user -> id) -> delete();

		$user -> delete();
		
		if (empty($user)) {
			return Redirect::to('admin/users') -> with('success', 'Success');
		} else {
			// There was a problem deleting the user
			return Redirect::to('admin/users') -> with('error', 'Error');
		}
	}

	/**
	 * Show a list of all the users formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$users = User::select(array('users.id', 'users.name', 'users.surname', 'users.username', 'users.email', 'users.confirmed','users.last_login','users.created_at'));

		return Datatables::of($users)
						-> edit_column('confirmed', '@if($confirmed) Yes @else No @endif') 
						 -> add_column('actions', '
                                <a href="{{{ URL::to(\'admin/users/\' . $id . \'/usershistory\' ) }}}" class="btn btn-sm btn-link"><i class="icon-signal "></i></a>                               
						 		<a href="{{{ URL::to(\'admin/users/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-sm btn-default"><i class="icon-edit "></i></a>
                                @if($id == \'1\')
                                @else
                                    <a href="{{{ URL::to(\'admin/users/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
                                @endif             ') -> remove_column('id') -> make();
	}

	/**
	 * Show a list of all the users with role formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getDataforrole($role_id) {
		$users = User::leftjoin('assigned_roles', 'assigned_roles.user_id', '=', 'users.id') 
					-> leftjoin('roles', 'roles.id', '=', 'assigned_roles.role_id') 
					-> where('assigned_roles.role_id', '=', $role_id) 
					-> select(array('users.id', 'users.name', 'users.surname', 'users.username', 'users.email', 'roles.name as rolename', 'users.confirmed','users.last_login','users.created_at'));

		return Datatables::of($users) -> edit_column('confirmed', '@if($confirmed) Yes @else No @endif') 
						 -> add_column('actions', ' <a href="{{{ URL::to(\'admin/users/\' . $id . \'/usershistory\' ) }}}" class="btn btn-sm btn-link"><i class="icon-signal "></i></a>                               
						 		<a href="{{{ URL::to(\'admin/users/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-sm btn-default"><i class="icon-edit "></i></a>
                                @if($id == \'1\')
                                @else
                                    <a href="{{{ URL::to(\'admin/users/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-sm btn-danger"><i class="icon-trash "></i></a>
                                @endif
            ') -> remove_column('id') -> make();
	}
	
	/*edit admin user profile*/
	/**
	 * Users settings page
	 *
	 * @return View
	 */
	public function getProfileEdit() {
					
			$user_auth = Auth::user();
			$user = User::where('id', '=', $user_auth->id) -> first();
			// Title
			$title = Lang::get('admin/users/title.user_update');			
			// mode
			$mode = 'edit';
			return View::make('admin/users/profile', compact('user', 'title', 'mode'));
	}
	
	 
	 /**
	 * Edits a user
	 *
	 */
	public function postProfileEdit() {
			// Declare the rules for the form validation
		$rules = array('surname' => 'required', 
						'name'=>'required');
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);
		
		$user_auth = Auth::user();
		$user = User::where('id', '=', $user_auth->id) -> first();		
		if ($validator -> passes()) {
		
			$oldUser = clone $user;
			$user -> name = Input::get('name');
			$user -> surname = Input::get('surname');
			$password = Input::get('password');
			$passwordConfirmation = Input::get('password_confirmation');
			if(Input::hasFile('avatar'))
			{
				$file = Input::file('avatar');
				$destinationPath = public_path() . '\avatar\\/';
				$filename = $file->getClientOriginalName();				
				$extension = $file -> getClientOriginalExtension();
				$name = sha1($filename . time()) . '.' . $extension;			
				Input::file('avatar')->move($destinationPath, $name);
				Thumbnail::generate_image_thumbnail($destinationPath. $name, $destinationPath .$name,$this->useravatwidth,$this->useravatheight);
				$user -> avatar = $name;
			}
			if (!empty($password)) {
				if ($password === $passwordConfirmation) {
					$user -> password = $password;
					// The password confirmation will be removed from model
					// before saving. This field will be used in Ardent's
					// auto validation.
					$user -> password_confirmation = $passwordConfirmation;
				} else {
					// Redirect to the new user page
					return Redirect::to('users') -> with('error', Lang::get('admin/users/messages.password_does_not_match'));
				}
			} else {
				unset($user -> password);
				unset($user -> password_confirmation);
			}
			$user -> amend();
		}
		// Get validation errors (see Ardent package)
		$error = $user -> errors() -> all();
		
		if (empty($error)) {
			return Redirect::to('admin/users/profile') -> with('success', Lang::get('user/user.user_account_updated'));
		} else {
			return Redirect::to('admin/users/profile') -> withInput(Input::except('password', 'password_confirmation')) -> with('error', $error);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getHistory($user_id) {

		$pageitem = 2;
		$settings = Settings::all();
        foreach ($settings as $v) {
                if ($v -> varname == 'pageitem') {
                        $pageitem = $v -> value;
                }
        }

		$title = Lang::get('admin/users/title.history_login');

		$user = User::find($user_id);
		$historylogin = UserLoginHistory::where('user_id','=',$user_id)-> paginate($pageitem);
		
		// Show the page
		return View::make('admin/users/history_login', compact('title', 'user','historylogin'));
	}
	

}
