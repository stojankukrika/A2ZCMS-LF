<?php namespace App\Modules\Install\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,File,Response,Config,Artisan,DB,Hash,DateTime;

use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\AssignedRoles;
use App\Modules\Users\Models\User;
use App\Modules\Settings\Models\Setting;
use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Models\NavigationGroup;
use App\Modules\Pages\Models\PluginFunction;

class InstallController extends \BaseController {

	/**
	 * Create a new Install controller.
	 *
	*/
	public function __construct() {
		// If the config is marked as installed then bail with a 404.
		if (Config::get("a2zcms.installed") === true) {
			return Redirect::to('');
		}
		define('STDIN',fopen("php://stdin","r"));
	}
	 public $errors = array();
	 
	 /*folder that need to be a writable*/
	 public $writable_dirs = array(
        'avatar' => FALSE,
        'page' => FALSE,
    );

	/**
	 * Get the install index.
	 *
	 * @return Response
	 */
	public function getIndex() {
		return View::make('install::step1');
	}

	/**
	 * Run the chenck if user accept the licence
	 *
	 * @return Response
	 */
	public function postIndex() {
			$form = Validator::make($input = Input::all(), array('accept' => array('required')));

		if ($form -> passes()) {
			return Redirect::to('install/step2');
		} else {
			return Redirect::to('install/index') -> withErrors($form);
		}
	}
	
	/*
	 * Run validate if files and folders are on server and writable 
	 * */
	 
	private function validate()
    {
    	$cms_root = getcwd().'/';		
		
        if ( ! is_writable($cms_root . '../app/config/app.php'))
        {
            $this->errors[] =  $cms_root . '../app/config/app.php is not writable.';
        }

        if ( ! is_writable($cms_root . '../app/config/database.php'))
        {
            $this->errors[] =  $cms_root . '../app/config/database.php is not writable.';
        }
		if ( ! is_writable($cms_root . '../app/config/a2zcms.php'))
        {
            $this->errors[] =  $cms_root . '../app/config/a2zcms.php is not writable.';
        }
		$writable_dirs = $this->writable_dirs;
        foreach ($writable_dirs as $path => $is_writable)
        {
            if(!is_writable($cms_root . $path))
            {
            	$this->errors[] = $cms_root . $path . ' is not writable.';
            }
        }

        if (phpversion() < '5.3.7')
        {
            $this->errors[] = 'You need to use PHP 5.3.7 or greater.';
        }

        if ( ! ini_get('file_uploads'))
        {
            $this->errors[] = 'File uploads need to be enabled in your PHP configuration.';
        }

        if ( ! extension_loaded('mysql'))
        {
            $this->errors[] = 'The PHP MySQL extension is required.';
        }

        if ( ! extension_loaded('gd'))
        {
            $this->errors[] = 'The PHP GD extension is required.';
        }

        if ( ! extension_loaded('curl'))
        {
            $this->errors[] = 'The PHP cURL extension is required.';
        }
        if (empty($this->errors))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
	/**
	 * Get the user form to show info of files and folders
	 *
	 * @return Response
	 */
	public function getStep2() {
		clearstatcache();
		$cms_root = getcwd().'/';
		$writable_dirs = $this->writable_dirs;
        foreach ($writable_dirs as $path => $is_writable)
        {
            $this->writable_dirs[$path] = is_writable($cms_root . $path);
        }
		
		$data['writable_dirs'] = $this->writable_dirs;
        $data['cms_root'] = $cms_root;
		return View::make('install::step2',$data);
	}
	/**
	 * Run the validation of writable files and folders
	 *
	 * @return Response
	 */
	public function postStep2() {
		if ($this->validate()) {
			return Redirect::to('install/step3');
		} else {
			return Redirect::to('install/step2') -> withErrors($this->errors);
		}
		
	}

	/**
	 * Get the user form to enter database params.
	 *
	 * @return Response
	 */
	public function getStep3() {
		return View::make('install::step3');
	}

	/**
	 * Add database settings and migrate database
	 *
	 * @return Response
	 */
	public function postStep3() {

		$form = Validator::make($input = Input::all(), array('hostname' => array('required'), 'username' => array('required'), 'database' => array('required'), ));

		if ($form -> passes()) {

			$search = array_map(function($key) {
				return '{{' . $key . '}}';

			}, array_keys($input));
			$replace = array_values($input);

			$stub = File::get(app_path(). '\config\database_temp.php');

			$stub = str_replace($search, $replace, $stub);

			File::put(app_path(). '\config\/' . App::environment() . '\database.php', $stub);

			
			/*delete temp file*/

			//File::delete($stub);
			$url = URL::to('/');
			$this -> setA2ZApp($url.'/');
			Artisan::call('migrate',  array('--force' => true));
			
			//triger for update user last_login affter user is login to system
			DB::unprepared("CREATE TRIGGER ".Input::get('prefix')."user_login_historys_after_inserts 
							AFTER INSERT ON ".Input::get('prefix')."user_login_historys
							 FOR EACH ROW UPDATE ".Input::get('prefix')."users set ".Input::get('prefix')."users.last_login = 
							(select ".Input::get('prefix')."user_login_historys.created_at 
							 from ".Input::get('prefix')."user_login_historys
							 where ".Input::get('prefix')."user_login_historys.id = NEW.id) 
							WHERE id = (select ".Input::get('prefix')."user_login_historys.user_id 
							 from ".Input::get('prefix')."user_login_historys
							 where ".Input::get('prefix')."user_login_historys.id = NEW.id)");
			
			
			return Redirect::to('install/step4');
		} else {
			return Redirect::to('install/step3') -> withErrors($form);
		}
	}

	/**
	 * Update the configs based on passed data
	 *
	 * @param string $url
	 *
	 * @return
	 */
	protected function setA2ZApp($url) {
		$content = str_replace('##url##', $url, File::get(app_path() . '\config\app.php'));
		return File::put(app_path(). '\config\/' . App::environment() . '\app.php', $content);
	}

	/**
	 * Get the user form for creating admin user.
	 *
	 * @return Response
	 */
	public function getStep4() {
		return View::make('install::step4');
	}

	/**
	 * Add the user as admin
	 *
	 * @return Response
	 */
	public function postStep4() {

		$rules = array('first_name' => 'required', 'last_name' => 'required', 'username' => 'required', 'email' => 'required', 'password' => 'required', );
		
		
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		if (!$validator -> passes() || Input::get('password')!=Input::get('passwordconfirm')) {
			Redirect::to('install/step4') -> withInput() -> withErrors($validator);
		}
		
		$user_id = DB::table('users') -> insertGetId(array('name' => Input::get('first_name'), 
							'surname' => Input::get('last_name'), 'username' => Input::get('username'), 
							'email' => Input::get('email'), 'password' => Hash::make(Input::get('password')),
							'confirmation_code' => md5(microtime() . Config::get('app.key')), 
							'confirmed' => '1', 'active' => '1'
							));
		
		$adminRole = new Role;
		$adminRole -> name = 'Administrator';
		$adminRole -> is_admin = 1;
		$adminRole -> save();
		
		$assigned_role = new AssignedRoles;
		$assigned_role -> user_id = $user_id;
		$assigned_role -> role_id = $adminRole -> id;
		$assigned_role -> save();
		
		$this->seed();
		//Artisan::call('db:seed');
		
		$settings = Setting::all();
		foreach ($settings as $v) {
			switch ($v->varname) {
				case 'email' :
					$v -> value = Input::get('email');
					break;
			}
			Setting::where('varname', '=', $v -> varname) -> update(array('value' => $v -> value));
		}

		return Redirect::to('install/step5');
	}

	/**
	 * Get the config form.
	 */
	public function getStep5() {
		return View::make('install::step5');
	}

	/**
	 * Save the config files and FINISH install
	 */
	public function postStep5() {
		$this -> setA2ZConfig(Input::get('title', 'Site Name'), Input::get('theme', 'a2z-default'),
								Input::get('per_page', 5));
		return View::make('install::complete');
	}

	/**
	 * Update the configs based on passed data
	 *
	 * @param string $title
	 * @param string $theme
	 * @param int    $per_page
	 *
	 * @return
	 */
	protected function setA2ZConfig($title, $theme, $per_page) {
		$content = str_replace(array('##theme##', "'##installed##'"), array($theme, 'true'), 
		File::get(app_path() . '\config\a2zcms_temp.php'));

		$settings = Setting::all();
		foreach ($settings as $v) {

			switch ($v->varname) {
				case 'title' :
					$v -> value = $title;
					break;
				case 'pageitem' :
					$v -> value = $per_page;
					break;
				case 'sitetheme' :
					$v -> value = $theme;
					break;
			}
			Setting::where('varname', '=', $v -> varname) -> update(array('value' => $v -> value));
		}

		return File::put(app_path() . '\config\a2zcms.php', $content);
	}
	
	protected function seed()
	{		
		/*OTHER SEED*/
		
		DB::table('permissions') -> insert(array( 
						array('name' => 'manage_users', 'display_name' => 'Manage users',
							'is_admin' => 1), 
						array('name' => 'manage_roles', 'display_name' => 'Manage roles',
							'is_admin' => 1),
						array('name' => 'manage_navigation', 'display_name' => 'Manage navigation',
							'is_admin' => 1),
						array('name' => 'manage_pages', 'display_name' => 'Manage pages',
							'is_admin' => 1),
						array('name' => 'manage_navigation_groups', 'display_name' => 'Manage navigation groups',
							'is_admin' => 1),
						array('name' => 'manage_settings', 'display_name' => 'Manage settings',
							'is_admin' => 1),
						array('name' => 'manage_plugins', 'display_name' => 'Manage plugins',
							'is_admin' => 1)));
				
		DB::table('permission_role') -> insert(array( array('role_id' => 1, 'permission_id' => 1), 
								array('role_id' => 1, 'permission_id' => 2), 
								array('role_id' => 1, 'permission_id' => 3), 
								array('role_id' => 1, 'permission_id' => 4), 
								array('role_id' => 1, 'permission_id' => 5), 
								array('role_id' => 1, 'permission_id' => 6),
								array('role_id' => 1, 'permission_id' => 7)));
		
		DB::table('settings') -> insert(array( 
											array('varname' => 'updatetime', 
												'vartitle' => 'Update time', 
												'groupname' => 'version', 
												'value' => time(), 
												'defaultvalue' => time(),
												'type' =>'text',
												'rule' => ''), 
											array('varname' => 'offline', 
												'vartitle' => 'Offline',
												'groupname' => 'offline', 
												'value' => 'No', 
												'type' =>'radio',
												'defaultvalue' => 'Yes;No',
												'rule' => ''), 
											array('varname' => 'version', 
												'vartitle' => 'Version',
												'groupname' => 'version', 
												'value' => '1.0', 
												'type' =>'text',
												'defaultvalue' => '1.0',
												'rule' => ''), 
											array('varname' => 'offlinemessage', 
												'vartitle' => 'Offline message',
												'groupname' => 'offline', 
												'value' => '<p>Sorry, the site is unavailable at the moment while we are testing some functionality.</p>', 
												'type' =>'textarea',
												'defaultvalue' => 'Sorry, the site is unavailable at the moment while we are testing some functionality.',
												'rule' => ''), 
											array('varname' => 'title', 
												'vartitle' => 'Title',
												'groupname' => 'general', 
												'value' => 'A2Z CMS-dev', 
												'type' =>'text',
												'defaultvalue' => 'A2Z CMS',
												'rule' => 'required'),
											array('varname' => 'copyright', 
												'vartitle' => 'Copyright',
												'groupname' => 'general', 
												'value' => 'yoursite.com &copy; 2014', 
												'type' =>'text',
												'defaultvalue' => 'A2Z CMS 2014',
												'rule' => ''), 
											array('varname' => 'metadesc', 
												'vartitle' => 'Meta desc',
												'groupname' => 'metadata', 
												'value' => '', 
												'type' =>'textarea',
												'defaultvalue' => '',
												'rule' => ''),
											array('varname' => 'metakey', 
												'vartitle' => 'Meta key',
												'groupname' => 'metadata', 
												'value' => '', 
												'type' =>'textarea',
												'defaultvalue' => '',
												'rule' => ''),
											array('varname' => 'metaauthor', 
												'vartitle' => 'Meta author',
												'groupname' => 'metadata', 
												'value' => 'http://www.yoursite.com', 
												'type' =>'text',
												'defaultvalue' => 'http://www.a2zcms.com',
												'rule' => ''), 
											array('varname' => 'analytics', 
												'vartitle' => 'Analytics',
												'groupname' => 'analitic', 
												'value' => '', 
												'type' =>'textarea',
												'defaultvalue' => '',
												'rule' => ''), 
											array('varname' => 'contactemail', 
												'vartitle' => 'Contact email',
												'groupname' => 'general', 
												'value' => 'admin@mail.com', 
												'type' =>'text',
												'defaultvalue' => 'admin@mail.com',
												'rule' => 'required|email'), 
											array('varname' => 'dateformat', 
												'vartitle' => 'Date format',
												'groupname' => 'general', 
												'value' => 'd.m.Y', 
												'type' =>'text',
												'defaultvalue' => 'd.m.Y',
												'rule' => 'required'), 
											array('varname' => 'timeformat', 
												'vartitle' => 'Time format',
												'groupname' => 'general', 
												'value' => ' - H:i', 
												'type' =>'text',
												'defaultvalue' => 'h:i A',
												'rule' => 'required'), 
											array('varname' => 'useravatwidth', 
												'vartitle' => 'User avatar width',
												'groupname' => 'general', 
												'value' => '150', 
												'type' =>'text',
												'defaultvalue' => '150',
												'rule' => 'required|integer'), 
											array('varname' => 'useravatheight', 
												'vartitle' => 'User avatar height',
												'groupname' => 'general', 
												'value' => '113', 
												'type' =>'text',
												'defaultvalue' => '113',
												'rule' => 'required|integer'), 
											array('varname' => 'pageitem', 
												'vartitle' => 'Per page item',
												'groupname' => 'general', 
												'value' => '8', 
												'type' =>'text',
												'defaultvalue' => '8',
												'rule' => 'required|integer'), 
											array('varname' => 'searchcode', 
												'vartitle' => 'Search code',
												'groupname' => 'search', 
												'value' => '', 
												'type' =>'textarea',
												'defaultvalue' => '',
												'rule' => ''), 
											array('varname' => 'sitetheme', 
												'vartitle' => 'Site theme',
												'groupname' => 'general', 
												'value' => 'a2z-default', 
												'type' =>'option',
												'defaultvalue' => 'ASSETS_PATH_FULL',
												'rule' => 'required'), 
											array('varname' => 'passwordpolicy', 
												'vartitle' => 'Password policy',
												'groupname' => 'password', 
												'value' => 'No', 
												'type' =>'radio',
												'defaultvalue' => 'Yes;No',
												'rule' => ''), 
											array('varname' => 'minpasswordlength', 
												'vartitle' => 'Password length',
												'groupname' => 'password', 
												'value' => '6', 
												'type' =>'text',
												'defaultvalue' => '6',
												'rule' => 'integer'), 
											array('varname' => 'minpassworddigits', 
												'vartitle' => 'Digits',
												'groupname' => 'password', 
												'value' => '1', 
												'type' =>'text',
												'defaultvalue' => '1',
												'rule' => 'integer'), 
											array('varname' => 'minpasswordlower', 
												'vartitle' => 'Lowercase letters',
												'groupname' => 'password', 
												'value' => '1', 
												'type' =>'text',
												'defaultvalue' => '1',
												'rule' => 'integer'), 
											array('varname' => 'minpasswordupper', 
												'vartitle' => 'Uppercase letters',
												'groupname' => 'password', 
												'value' => '1', 
												'type' =>'text',
												'defaultvalue' => '1',
												'rule' => 'integer'), 
											array('varname' => 'usegravatar', 
												'vartitle' => 'Use Gravatar',
												'groupname' => 'general', 
												'value' => 'No', 
												'type' =>'radio',
												'defaultvalue' => 'Yes;No',
												'rule' => '')
																							
										)
									);
			
			DB::table('plugins')->insert(array( 
					array('name' => 'pages', 
						'title' => 'Pages', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),					
					array('name' => 'settings', 
						'title' => 'Settings', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'users', 
						'title' => 'Users', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'roles', 
						'title' => 'Roles', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'plugins', 
						'title' => 'Plugins', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'adminmenu', 
						'title' => 'Admin menu', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'menu', 
						'title' => 'Website menu', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, )	
						
				));
				
				DB::table('plugin_functions')->insert(array( 
					array('title' => 'Login form', 
						'plugin_id' => 0,
						'function'=>'login_partial',
						'params'=>'',
						'type' => 'sidebar',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),					
					array('title' => 'Search Form', 
						'plugin_id' => 0,
						'function'=>'search',
						'params'=>'',
						'type' => 'sidebar',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),
					array('title' => 'Content', 
						'plugin_id' => 0,
						'function'=>'content',
						'params'=>'',
						'type' => 'content',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),
					array('title' => 'Side menu', 
						'plugin_id' => 0,
						'function'=>'sideMenu',
						'params'=>'',
						'type' => 'sidebar',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ))
						);
						
		DB::table('navigation_groups')->insert(array( 
					array('title' => 'Main menu', 
						'slug' => 'main-menu',
						'showmenu' => '1',
						'showfooter' => '0',
						'showsidebar' => '0',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ))
						);
		
		$content = '<div><h1>A2Z CMS 1.0</h1><p>Welcome to your very own A2Z CMS 1.1 installation.</p></div><div><p>Login into your profile and change this page and enjoy in A2ZCMS.</p><p>If you have any questions feel free to check the <a href="https://github.com/mrakodol/A2ZCMS/issues">Issues</a> at any time or create a new issue.</p><p>Enjoy A2Z CMS and welcome a board.</p><p>Kind Regards</p><p>Stojan Kukrika - A2Z CMS</p></div>';
		 DB::table('pages')->insert(array( 
					array('title' => 'Home', 
						'slug' => 'home',
						'meta_title' => '',
						'meta_description' => '',
						'meta_keywords' => '',
						'page_css' => '',
						'page_javascript' => '',
						'sidebar' => '1',
						'showtags' => '1',
						'showtitle' => '1',
						'showvote' => '1',
						'showdate' => '1',
						'voteup' => '0',
						'votedown' => '0',
						'password' => '',
						'tags' => 'tag1',
						'hits' => '0',
						'content' => $content,
						'image' => '',
						'status' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, )	
						
				));
				
		DB::table('navigation_links')->insert(array( 
					array('title' => 'Home', 
						'parent' => NULL,
						'link_type' => 'page',
						'page_id' => 1,
						'url' => '',
						'uri' => '',
						'navigation_group_id' => 1,
						'position' => '1',
						'target' => '',
						'restricted_to' => '',
						'class' => '',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, )							
				));
		
		DB::table('page_plugin_functions')->insert(array( 
					array('page_id' => 1, 
						'plugin_function_id' => 1,
						'order' => '1',
						'param' => '',
						'type' => '',
						'value' => '',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),
					array('page_id' => 1, 
						'plugin_function_id' => 3,
						'order' => '1',
						'param' => '',
						'type' => '',
						'value' => '',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ))
				);
				
		DB::table('admin_navigations') -> insert(array( 
											array('plugin_id' => 1, 
												'icon' =>'icon-globe', 
												'background_color' =>'red',
												'order' => 1,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,),											
											array('plugin_id' => 3, 
												'icon' =>'icon-user', 
												'background_color' =>'yellow',
												'order' => 2,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('plugin_id' => 4, 
												'icon' =>'icon-group', 
												'background_color' =>'green',
												'order' => 3,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('plugin_id' => 5, 
												'icon' =>'icon-cloud', 
												'background_color' =>'blue',
												'order' => 4,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('plugin_id' => 2, 
												'icon' =>'icon-cogs', 
												'background_color' =>'orange',
												'order' => 5,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
										)
									);
			
			DB::table('admin_subnavigations') -> insert(array( 
											array('admin_navigation_id' => 1, 
												'title' => 'Navigation group', 
												'url' => 'pages/navigationgroups', 
												'icon' =>'icon-th-list',
												'order' => 1,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,),											
											array('admin_navigation_id' => 1, 
												'title' => 'Pages', 
												'url' => 'pages', 
												'icon' =>'icon-th-large',
												'order' => 2,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('admin_navigation_id' => 1, 
												'title' => 'Navigation', 
												'url' => 'pages/navigation', 
												'icon' =>'icon-th',
												'order' => 3,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
										)
									);
		/*OTHER SEED*/
	}
}
