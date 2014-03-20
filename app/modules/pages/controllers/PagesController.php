<?php namespace App\Modules\Pages\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect,String;

use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Models\Navigation;
use App\Modules\Settings\Models\Setting;
use App\Modules\Users\Models\User;

class PagesController extends \BaseController {

	/**
	 * Page Model
	 * @var Page
	 */
	protected $page;
	/**
	 * Settings Model
	 * @var Setting
	 */
	protected $settings;
	/**
	 * Inject the models.
	 * @param Blog $blog
	 * @param User $user
	 */
	 protected $user;
	 
	 public function __construct(Page $page, Setting $settings, User $user) {
		parent::__construct();
		$this -> page = $page;
		$settings = Setting::all();
		$this -> settings = $settings;
		$this->user = $user;

	}
 /*function for plugins*/
	public function getView($slug=0) {
		if($slug==0) $slug = 1;
		// Get this webpage data
		$navigation_link = Navigation::where('id', '=', $slug) -> first();
		$page = $this -> page -> find($navigation_link->page_id);
		$page -> hits = $page -> hits + 1;
		$page -> update();
		$data=array();
		// Check if the blog page exists
		if (is_null($page)) {
			// If we ended up in here, it means that a page didn't exist.
			// So, this means that it is time for 404 error page.
			return App::abort(404);
		}
		$pagecontent = \BaseController::createSiderContent($page->id);
		// Show the page
		$data['sidebar_right'] = $pagecontent['sidebar_right'];
		$data['sidebar_left'] = $pagecontent['sidebar_left'];
		$data['content'] = $pagecontent['content'];		
		$data['page'] = $page;
		
		return View::make('pages::site/viewPage', $data);
	}
	
	public function content($page_id)
	{
		$user = $this -> user -> currentUser();
		$canPageVote = false;
		$page = Page::find($page_id);
		return View::make('pages::site/content', compact('page','user','canPageVote'));
	}
	
	public function login_partial($params)
	{
		$user = $this->user;
		return View::make('pages::site/login_partial',compact('user'));
	}
	public function sideMenu($params)
	{
		$side_menu = \BaseController::main_menu('side');
		return View::make('pages::site/sideMenu',compact('side_menu'));
	}
	public function search($params)
	{
		return View::make('pages::site/search');
	}
}