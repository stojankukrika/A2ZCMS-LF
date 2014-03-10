<?php namespace App\Modules\Roles\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator;

use App\Modules\Roles\Models\Role;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Roles\Models\Permission;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\AssignedRoles;

class AdminRolesController extends \AdminController{
	
	/**
	 * User Model
	 * @var User
	 */
	protected $user;

	/**
	 * Role Model
	 * @var Role
	 */
	protected $role;

	/**
	 * Permission Model
	 * @var Permission
	 */
	protected $permission;

	/**
	 * Inject the models.
	 * @param User $user
	 * @param Role $role
	 * @param Permission $permission
	 */
	public function __construct(User $user, Role $role, Permission $permission) {
		parent::__construct();
		$this -> user = $user;
		$this -> role = $role;
		$this -> permission = $permission;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex() {
		// Title
		$title = "Role management";
		// Grab all the groups
		$roles = $this -> role;

		// Show the page
		return View::make('roles::admin/index', compact('roles', 'title'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate() {
		// Get all the available permissions
		$permissionsAdmin = $this -> permission -> where('is_admin','=',1)->get();
		$permissionsUser = $this -> permission -> where('is_admin','=',0)->get();

		// Selected permissions
		$permisionsadd =Input::old('permissions', array());
		
		// Show the page
		return View::make('roles::admin/create_edit', compact('permissionsAdmin', 'permissionsUser','permisionsadd'));
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate() {

		// Declare the rules for the form validation
		$rules = array('name' => 'required');
		$is_admin = 0;
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);
		// Check if the form validates with success
		if ($validator -> passes()) {
			
			// Check if role is for admin user
			$permissionsAdmin = $this -> permission -> where('is_admin','=',1)->get();
			
			foreach ($permissionsAdmin as $perm){
		            foreach(Input::get('permission') as $item){
	            		if($item==$perm['id'] && $perm['is_admin']=='1')
						{
							$is_admin = 1;
						}
		            }
				}
						
			$this -> role -> is_admin = $is_admin;
			$this -> role -> name = Input::get('name');
			$this -> role -> save();
			
			foreach (Input::get('permission') as $item) {
				$permission = new PermissionRole;
				$permission->permission_id = $item;
				$permission->role_id = $this -> role->id;
				$permission -> save();
			}			
		}

		// Form validation failed
		return Redirect::to('admin/roles/' . $this -> role -> id . '/create_edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $role
	 * @return Response
	 */
	public function getEdit($id) {
		$role = Role::find($id);
		if (!empty($role)) {
			$permissionsAdmin = $this -> permission -> where('is_admin','=',1)->get();
			$permissionsUser = $this -> permission -> where('is_admin','=',0)->get();

		} else {
			// Redirect to the roles management page
			return Redirect::to('admin/roles');
		}
		$permisionsadd = PermissionRole::where('role_id','=',$id)->select('permission_id')->get();
		
		// Show the page
		return View::make('roles::admin/create_edit', compact('role', 'permissionsAdmin', 'permissionsUser','permisionsadd'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $role
	 * @return Response
	 */
	public function postEdit($id) {
		// Declare the rules for the form validation
		$rules = array('name' => 'required');
		$is_admin = 0;
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);
		
		// Check if the form validates with success
		if ($validator -> passes()) {
			// Update the role data
			$permissionsAdmin = $this -> permission -> where('is_admin','=',1)->get();
			foreach ($permissionsAdmin as $perm){
		            foreach(Input::get('permission') as $item){
	            		if($item==$perm['id'] && $perm['is_admin']=='1')
						{
							$is_admin = 1;
						}
		            }
				}
			$role = Role::find($id);
			$role -> is_admin = $is_admin;
			$role -> name = Input::get('name');
			$role -> save();
			
			PermissionRole::where('role_id','=',$id) -> delete();
				
			foreach (Input::get('permission') as $item) {
				$permission = new PermissionRole;
				$permission->permission_id = $item;
				$permission->role_id = $role->id;
				$permission -> save();
			}
		}

		// Form validation failed
		return Redirect::to('admin/roles/' . $role -> id . '/create_edit') -> withInput() -> withErrors($validator);
	}

	/**
	 * Remove user page.
	 *
	 * @param $role
	 * @return Response
	 */
	public function getDelete($role) {
		
			if ($role -> delete()) {
			// Redirect to the role management page
			return Redirect::to('admin/roles') -> with('success', Lang::get('admin/roles/messages.delete.success'));
		}

		// There was a problem deleting the role
		return Redirect::to('admin/roles') -> with('error', Lang::get('admin/roles/messages.delete.error'));
	}

	/**
	 * Show a list of all the roles formatted for Datatables.
	 *
	 * @return Datatables JSON
	 */
	public function getData() {
		$roles = Role::select(array('roles.id', 'roles.name', 'roles.id as users', 'roles.created_at'));

		return Datatables::of($roles) -> edit_column('users', '<a href="{{{ URL::to(\'admin/users/\' . $id . \'/usersforrole\' ) }}}" class="btn btn-link btn-sm" >{{{ App\Modules\Users\Models\AssignedRoles::where("role_id", "=", $id)->count() }}}</a>') 
					-> add_column('actions', '<a href="{{{ URL::to(\'admin/roles/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-sm btn-default"><i class="icon-edit "></i></a>
                                <a href="{{{ URL::to(\'admin/roles/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger"><i class="icon-trash "></i></a>
                    ') -> remove_column('id') -> make();
	}

}
?>