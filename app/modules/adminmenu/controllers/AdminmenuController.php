<?php namespace App\Modules\Adminmenu\Controllers;

use App, View, Session,Auth,URL;
use App\Modules\Adminmenu\Models\Adminmenu;
use App\Modules\Adminmenu\Models\Adminsubmenu;

class AdminmenuController extends \AdminController{
	
	function left_navigation(){
			
		$mainadminmenu = Adminmenu::leftJoin('plugins', 'plugins.id', '=', 'admin_navigations.plugin_id')
		->orderBy('admin_navigations.order','ASC')
		->get(array('admin_navigations.id','plugins.title', 'admin_navigations.icon', 'plugins.name as url', 'admin_navigations.order'));
		
		foreach ($mainadminmenu as $item) {
			$mainadminsubmenu = Adminsubmenu::where('admin_navigation_id',$item->id)
					->orderBy("order", "asc")
					->orderBy("admin_navigation_id", "asc")
					->get(array('id','admin_navigation_id', 'title', 'icon', 'url','order'));
					if(!isset($mainadminsubmenu)){
						$item->adminsubmenu = $mainadminsubmenu;
					}			
		}
		$adminmenuleft = '<div id="sidebar-left" class="col-lg-2 col-sm-1 ">
					<div class="sidebar-nav nav-collapse collapse navbar-collapse">
						<ul class="nav main-menu">							
		<li>
			<a href="'. URL::to('admin/').'"><i class="icon-dashboard"></i><span class="hidden-sm text">Dashboard</span></a>
		</li>';
		foreach ($mainadminmenu as $adminmainmenu) {
			if(!empty($adminmainmenu->adminsubmenu))
			{
				$adminmenuleft .='<li>
						<a class="dropmenu" href="'. URL::to('admin/'.$adminmainmenu->url).'"><i class="'.$adminmainmenu->icon.'"></i>
							<span class="hidden-sm text">'.$adminmainmenu->title.'</span></a><ul>';
				foreach($adminmainmenu->adminsubmenu as $adminsubmenu)
				{
					$adminmenuleft .='<li>
						<a href="'. URL::to('admin/'.$adminsubmenu->url).'"><i class="'.$adminsubmenu->icon.'"></i>
							<span class="hidden-sm text">'.$adminsubmenu->title.'</span></a>
					</li>';
				}	
				$adminmenuleft .='</ul></li>';			
			}
			else {
				$adminmenuleft .='<li>
						<a href="'. URL::to('admin/'.$adminmainmenu->url).'"><i class="'.$adminmainmenu->icon.'"></i>
							<span class="hidden-sm text">'.$adminmainmenu->title.'</span></a>
					</li>';
			}
		}		
	$adminmenuleft.='</ul>
	</div>
		<a href="#" id="main-menu-min" class="full visible-md visible-lg"><i class="icon-double-angle-left"></i></a>
		</div>
			<!-- end: Main Menu -->';
		return $adminmenuleft;
	}

	
		
}

?>