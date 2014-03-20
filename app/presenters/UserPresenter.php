<?php

use Robbo\Presenter\Presenter;

use App\Modules\Roles\Models\Role;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Users\Models\AssignedRoles;

class UserPresenter extends Presenter
{

    public function isActivated()
    {
        if( $this->confirmed )
        {
            return false;
        }
        else
        {
            return true;
        }

    }
	
	public function currentRoleIds()
    {
    	$allow_admin = 0;
		$user = Auth::user();
		$assignedroles = AssignedRoles::where('user_id','=',$user->id)->get();
		$roles = false;
		foreach ($assignedroles as $item) {
			$roles[] = Role::find($item->role_id);
		}	
		if( !empty( $roles ) ) {
            $roleIds = array();
            foreach( $roles as $role )
            {
            	 $roleIds[] = $role->id;
				if($role->is_admin=='1')
				$allow_admin = 1;
            }
        }
        return array('roleIds' => $roleIds, 'allow_admin' => $allow_admin);
    }
	

    public function currentUser()
    {
        if( Auth::check() )
        {
            return Auth::user()->email;
        }
        else
        {
            return null;
        }
    }

    public function displayDate()
    {
        return date('m-d-y', strtotime($this->created_at));
    }

    /**
     * Returns the date of the user creation,
     * on a good and more readable format :)
     *
     * @return string
     */
    public function created_at()
    {
        return String::date($this->created_at);
    }

    /**
     * Returns the date of the user last update,
     * on a good and more readable format :)
     *
     * @return string
     */
    public function updated_at()
    {
        return String::date($this->updated_at);
    }
}