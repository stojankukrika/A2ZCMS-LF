<?php namespace App\Modules\Users\Models;

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\Confide;
use Zizaco\Confide\ConfideEloquentRepository;
use Zizaco\Entrust\HasRole;
use Robbo\Presenter\PresentableInterface;
use Carbon\Carbon;

use App\Modules\Roles\Models\Role;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Users\Models\AssignedRoles;

use Auth;

class User extends ConfideUser {
	
	protected $table = 'users';	
	
	public function getPresenter()
    {
        return new UserPresenter($this);
    }

    /**
     * Get user by username
     * @param $username
     * @return mixed
     */
    public function getUserByUsername( $username )
    {
        return $this->where('username', '=', $username)->first();
    }

    /**
     * Get the date the user was created.
     *
     * @return string
     */
    public function joined()
    {
        return String::date(Carbon::createFromFormat('Y-n-j G:i:s', $this->created_at));
    }

    /**
     * Returns user's current role ids only.
     * @return array|bool
     */
    public function currentRoleIds()
    {
    	$allow_admin = 0;
		$user = Auth::user();
		$assigned = PermissionRole::join('permissions','permissions.id','=','permission_role.permission_id')
											->join('assigned_roles','assigned_roles.role_id','=','permission_role.role_id')
											->where('assigned_roles.user_id','=',$user->id)
											->select('permissions.name','permissions.is_admin')
											->get();
		$roleIds = array();            	
		if( !empty( $assigned ) ) {
           foreach( $assigned as $item )
            {
            	$roleIds[$item->name] = $item->name;
				if($item->is_admin=='1')
				$allow_admin = 1;
            }
        }
        return array('roleIds' => $roleIds, 'allow_admin' => $allow_admin);
    }

    /**
     * Redirect after auth.
     * If ifValid is set to true it will redirect a logged in user.
     * @param $redirect
     * @param bool $ifValid
     * @return mixed
     */
    public static function checkAuthAndRedirect($redirect, $ifValid=false)
    {
        // Get the user information
        $user = Auth::user();
        $redirectTo = false;

        if(empty($user->id) && ! $ifValid) // Not logged in redirect, set session.
        {
            Session::put('loginRedirect', $redirect);
            $redirectTo = Redirect::to('users/login')
                ->with( 'notice', 'Login first' );
        }
        elseif(!empty($user->id) && $ifValid) // Valid user, we want to redirect.
        {
            $redirectTo = Redirect::to($redirect);
        }

        return array($user, $redirectTo);
    }

    public function currentUser()
    {
        return (new Confide(new ConfideEloquentRepository()))->user();
    }

}
