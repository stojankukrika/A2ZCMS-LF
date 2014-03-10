<?php namespace App\Modules\Plugins\Controllers;

use App, View, Session,Auth,URL,Input;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Adminmenu\Models\Adminmenu;

class AdminPluginsController extends \AdminController{

	protected $plugin;
	
	function __construct(Plugin $plugin)
	{
		parent::__construct();
		$this -> plugin = $plugin;
	}
	
	function getIndex(){
		
        $plugin = Plugin::Join('admin_navigations','plugins.id','=','admin_navigations.plugin_id')
						->orderBy('admin_navigations.order')
						->get(array('plugins.id','plugins.name','plugins.title','plugins.can_uninstall','plugins.created_at'));
				
		$temp = array();
		foreach ($plugin as $item) {
			$temp[]=$item->name;
		}
					
		foreach (glob( base_path().'/app/modules' . '/*', GLOB_ONLYDIR) as $dir) {
			$dir = str_replace(base_path().'/app/modules/', '', $dir);
			if(!in_array($dir,$temp) && $dir!='install' && $dir!='testmodule' && $dir!='offline' && $dir!='menu' && $dir!='adminmenu')
			$plugin[] =(object) array('name' => $dir, 'id'=>0,
			'title' => ucfirst($dir), 'created_at' => '', 'can_uninstall' =>0, 'not_installed'=>TRUE);
		}
		return View::make('plugins::admin/index', compact('plugin'));
	}
	
	function getReorder()
	{
		$list =Input::get('list');
		$items = explode(",", $list);
		$order = 1;
		foreach ($items as $value) {
			if ($value != '') {
				$adminemu = Adminmenu::where('plugin_id','=',$value)->update(array('order'=> $order));
				$order++;
			}
		};
	}
	
	function getDashboard()
	{
		$plugin = Plugin::Join('admin_navigations','plugins.id','=','admin_navigations.plugin_id')
						->orderBy('admin_navigations.order')
						->get(array('admin_navigations.id','plugins.title','admin_navigations.background_color',
						'admin_navigations.icon', 'plugins.name', 'admin_navigations.order'));

	return View::make('plugins::admin/dashboard', compact('plugin'));
	}
	

}