<?php
use App\Modules\Settings\Models\Setting;
use App\Modules\Users\Models\Message;
use App\Modules\Users\Models\User;
use App\Modules\Pages\Models\NavigationGroup;
use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Models\PagePluginFunction;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Config;
class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	
	public function __construct() {
		$user = new User;
		$this -> user = $user;
		$this -> beforeFilter('csrf', array('on' => 'post'));
		$this -> beforeFilter('detectLang');
		// Redirect to /install if the db isn't setup.
		if (Config::get("a2zcms.installed") !== true) {
			$url = URL::to('install');
			header('Location: '.$url);
			exit ;
		}
		$settings = Setting::whereIn('varname', 
						array('offline', 'metadesc','offlinemessage','metakey','metaauthor','title', 'contactemail','pageitem',
							'useravatwidth','useravatheight','copyright','analytics','dateformat','timeformat','searchcode',
							'sitetheme','usegravatar','passwordpolicy'))->get();
		$offline = 0;
		
		foreach ($settings as $v) {
				if ($v -> varname == 'offline') {
					$offline = $v -> value;
				}
				View::share($v -> varname,  $v -> value);
		}
		
		if($offline==1)
		{
			header('Location: '. Config::get("app.url").'/offline');
			exit ;
		}
		$user = Auth::user();
		if(!empty($user)){
			$unreadmessages = Message::where('user_id_to','=',$user->id)->where('read','=','0')->count();
			View::share('unreadmessages',  $unreadmessages);
		}
		$top_navigation = $this->main_menu('top');
		if(!empty($top_navigation)){
			View::share('top_menu',  $top_navigation);
		}
		
		$footer_navigation = $this->main_menu('footer');
		if(!empty($footer_navigation)){
			View::share('footer_menu',  $footer_navigation);
		}
		
		$side_navigation = $this->main_menu('side');
		if(!empty($side_navigation)){
			View::share('side_menu',  $side_navigation);
		}
	}

	/* Attempt to do login
	 *
	 */
	public function postLogin() {
		$input = array('email' => Input::get('email'), // May be the username too
		'username' => Input::get('email'), // May be the username too
		'password' => Input::get('password'), 'remember' => Input::get('remember'), );

		// If you wish to only allow login from confirmed users, call logAttempt
		// with the second parameter as true.
		// logAttempt will check if the 'email' perhaps is the username.
		// Check that the user is confirmed.
		if (Confide::logAttempt($input, true)) {				
			$login_user = Auth::user();			
			$userloginlog = new UserLoginHistory;
			$userloginlog -> user_id = $login_user->id;
			$userloginlog -> save();
				
			$r = Session::get('loginRedirect');
			if (!empty($r)) {
				Session::forget('loginRedirect');
				return Redirect::to($r);
			}
			return Redirect::back();
		} else {
			// Check if there was too many login attempts
			if (Confide::isThrottled($input)) {
				$err_msg = 'Too many attempts';
			} elseif ($this -> user -> checkUserExists($input) && !$this -> user -> isConfirmed($input)) {
				$err_msg = 'You are not confirmed account';
			} else {
				$err_msg = 'Wrong credentials';
			}

			return Redirect::back() -> withInput(Input::except('password')) -> with('error', $err_msg);
		}
	}

	public function main_menu($type) {
		$navigation ="";
		switch ($type) {
			case 'top':
				$navigation = NavigationGroup::join('navigation_links','navigation_groups.id', '=', 'navigation_links.navigation_group_id')
										->where('navigation_groups.showmenu','=','1') 
										-> select('navigation_links.id', 'navigation_links.title', 'navigation_links.parent', 'navigation_links.page_id', 'navigation_links.link_type', 'navigation_links.url', 'navigation_links.uri','navigation_links.link_type', 'navigation_links.target', 'navigation_links.position','navigation_links.class') 
										-> get();
				break;
			case 'footer':
				$navigation =NavigationGroup::join('navigation_links','navigation_groups.id', '=', 'navigation_links.navigation_group_id')
										->where('navigation_groups.showfooter','=','1') 
										-> select('navigation_links.id', 'navigation_links.title', 'navigation_links.parent', 'navigation_links.page_id', 'navigation_links.link_type', 'navigation_links.url', 'navigation_links.uri','navigation_links.link_type', 'navigation_links.target', 'navigation_links.position','navigation_links.class') 
										-> get();
				break;
			case 'side':
				$navigation = NavigationGroup::join('navigation_links','navigation_groups.id', '=', 'navigation_links.navigation_group_id')
										->where('navigation_groups.showsidebar','=','1') 
										-> select('navigation_links.id', 'navigation_links.title', 'navigation_links.parent', 'navigation_links.page_id', 'navigation_links.link_type','navigation_links.url', 'navigation_links.uri','navigation_links.link_type', 'navigation_links.target', 'navigation_links.position','navigation_links.class') 
										-> get();
				break;
		}		
		
		$menu = array('items' => array(), 'parents' => array());
		// Builds the array lists with data from the menu table
		foreach ($navigation as $key => $items) {
			
			// Creates entry into items array with current menu item id ie. $menu['items'][1]
			$menu['items'][$items['id']] = $items;
			// Creates entry into parents array. Parents array contains a list of all items with children
			$items['parent'] = (is_null($items['parent'])) ? 0 : $items['parent'];
			$menu['parents'][$items['parent']][] = $items['id'];
		}
		return $this -> buildMenu(0, $menu);
	}

	// Menu builder function, parentId 0 is the root
	public function buildMenu($parent, $menu) {
		$html = '';
		if (isset($menu['parents'][$parent])) {
			foreach ($menu['parents'][$parent] as $itemId) {
				if (!isset($menu['parents'][$itemId])) {

					$html .= "<li> <a target='".$menu['items'][$itemId]['target']."' class='".$menu['items'][$itemId]['class']."' href='";
					switch ($menu['items'][$itemId]['link_type']) {
						case 'page':
							$html .=URL::to('page') ."/". $menu['items'][$itemId]['id'];
							break;
						case 'url':
							$html .=URL::to($menu['items'][$itemId]['uri']);
							break;
						case 'uri':
							$html .=$menu['items'][$itemId]['url'];
							break;
					}
					$html .="'>" . $menu['items'][$itemId]['title'] . "</a></li>";
				}
				if (isset($menu['parents'][$itemId])) {
					$html .= "<li class='dropdown'> <a target='".$menu['items'][$itemId]['target']."' class='dropdown-toggle ".$menu['items'][$itemId]['class']."' href='";
					switch ($menu['items'][$itemId]['link_type']) {
						case 'page':
							$html .=URL::to('page') ."/". $menu['items'][$itemId]['id'];
							break;
						case 'url':
							$html .=URL::to($menu['items'][$itemId]['uri']);
							break;
						case 'uri':
							$html .=$menu['items'][$itemId]['url'];
							break;
					}
					$html .="'>" . $menu['items'][$itemId]['title'] . "</a>
						<ul class='dropdown-menu'>";
					$html .= $this -> buildMenu($itemId, $menu);
					$html .= " </ul></li>";
				}
			}
		}
		return $html;
	}
	/*function for moduls and custom forms to shows in webpage*/
	public function createSiderContent($page_id=0)
	{
		
		if($page_id==0) {
			$page_id = Page::first()->id;
		}
		$page = Page::find($page_id);
		
		$page_functions = $this->readModulsForPage($page_id);
		
		$function_content = $page_functions['pluginfunction_content'];
		$function_slider = $page_functions['pluginfunction_slider'];
		

		$sidebar_right = array(); 
		$sidebar_left = array(); 
		$content = array();		

		foreach ($function_slider as $item)
		{
			if(!isset($item['name']))
			{
				$item['name'] = 'pages';
			}
			$function = $item->function;
			$params = $item->params;
			$address = 'App\Modules\\'.ucfirst($item['name']).'\Controllers\\';
			$controller = ucfirst($item['name']).'Controller';	
			
			if($page->sidebar==1){
				$sidebar_right[] = array('content' => App::make($address.$controller)->$function($params));
			}
			if($page->sidebar==0){
				$sidebar_left[] = array('content' => App::make($address.$controller)->$function($params));
			}
		}
		
		foreach ($function_content as $item)
		{
			if(!isset($item['name']))
			{
				$item['name'] = 'pages';
			}
			$function = $item->function;
			$ids = $item->ids;
			$grids = $item->grids;
			$sorts = $item->sorts;
			$limits = $item->limits;
			$orders = $item->orders;
			$address = 'App\Modules\\'.ucfirst($item['name']).'\Controllers\\';
			$controller = ucfirst($item['name']).'Controller';	
			
			if($item->params=="")
			{
				$content[] = array('content' =>App::make($address.$controller)->$function($page_id));
			}
			else {
				$content[] = array('content' =>App::make($address.$controller)->$function($ids,$grids,$sorts,$limits,$orders));
			}
		}
		return $pagecontent = array('sidebar_right' => $sidebar_right, 'sidebar_left'=>$sidebar_left, 'content'=>$content);	
	}
	public function readModulsForPage($page_id)
	{
		$pluginfunction_content = PagePluginFunction::leftJoin('plugin_functions','plugin_functions.id','=','page_plugin_functions.plugin_function_id')
									->leftJoin('plugins', 'plugins.id', '=', 'plugin_functions.plugin_id')
									->where('page_plugin_functions.page_id', '=' ,$page_id)
									->where('plugin_functions.type','=','content')
									->orderBy('page_plugin_functions.order','ASC')
									->groupBy('plugin_functions.id')
									->get(array('plugin_functions.id','page_plugin_functions.plugin_function_id',
									'plugin_functions.title','plugins.name','page_plugin_functions.order','plugins.function_id','plugin_functions.function','plugin_functions.params','plugins.function_grid'));

			foreach ($pluginfunction_content as $key => $value) {
				if($value['plugin_function_id']!=""){
					
					$value['ids'] = PagePluginFunction::where('param','=','id')->where('page_id','=',$page_id)->where('plugin_function_id','=',$value['plugin_function_id'])->pluck('value');
					$value['grids'] = PagePluginFunction::where('param','=','grid')->where('page_id','=',$page_id)->where('plugin_function_id','=',$value['plugin_function_id'])->pluck('value');
					$value['sorts'] = PagePluginFunction::where('param','=','sort')->where('page_id','=',$page_id)->where('plugin_function_id','=',$value['plugin_function_id'])->pluck('value');
					$value['limits'] = PagePluginFunction::where('param','=','limit')->where('page_id','=',$page_id)->where('plugin_function_id','=',$value['plugin_function_id'])->pluck('value');
					$value['orders'] = PagePluginFunction::where('param','=','order')->where('page_id','=',$page_id)->where('plugin_function_id','=',$value['plugin_function_id'])->pluck('value');
				}
			}
				
			$pluginfunction_slider = PagePluginFunction::leftJoin('plugin_functions','plugin_functions.id','=','page_plugin_functions.plugin_function_id')
								->leftJoin('plugins', 'plugins.id', '=', 'plugin_functions.plugin_id')
								->where('page_plugin_functions.page_id', '=' ,$page_id)
								->where('plugin_functions.type','=','sidebar')
								->orderBy('page_plugin_functions.order','ASC')
								->groupBy('plugin_functions.id')
								->get(array('plugin_functions.id', 'plugins.name', 'plugin_functions.title' ,'plugin_functions.params' ,
								'plugin_functions.function' ,'page_plugin_functions.order'));
	
		return $arrayName = array('pluginfunction_content' => $pluginfunction_content, 'pluginfunction_slider' => $pluginfunction_slider);
	}
	
	public function splitParams($params)
	{
		$return = array();
		$params = explode(';', $params);
		foreach ($params as $param) {
			if($param!=""){
				$param = explode(':', $param);
				$return[$param[0]] = $param[1];
				}
			}
		return $return;	
	}	

}