<?php
use App\Modules\Users\Models\AssignedRoles;
use App\Modules\Users\Models\User;

class AdminController extends Controller {

	/**
	 * Initializer.
	 *
	 * @return \AdminController
	 */
	protected $user;
	public $roles=array();
	public function __construct() {
		// Apply the admin auth filter
		$this -> beforeFilter('check_admin');
		$this -> beforeFilter('csrf', array('on' => 'post'));
		$this -> beforeFilter('detectLang');
		$this -> beforeFilter('auth');
		$this -> beforeFilter('before');
		
		if(!defined('ASSETS_PATH_FULL')){
			  define('ASSETS_PATH_FULL', '\public\assets\site');
    	} 
		$user = Auth::user();			
		if(!empty($user)){
			$user2 = new User;	
			$user = $user2;	
			$this -> user = $user;
			$roles = $user->currentRoleIds();
			if($roles['allow_admin']=='0'){
				URL::to('/');
			}
			$this->roles = $roles['roleIds'];	
		}		
	}

}
