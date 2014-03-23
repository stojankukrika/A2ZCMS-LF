<?php namespace App\Modules\Todolist\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Str,DB;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Adminmenu\Models\Adminmenu;
use App\Modules\Adminmenu\Models\Adminsubmenu;
use App\Modules\Roles\Models\Permission;
use App\Modules\Pages\Models\PluginFunction;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Pages\Models\PagePluginFunction;

class InstallTodolistController extends \AdminController{

	public function getInstall()
	{
		return View::make('todolist::install/install');
	}
	
	public function postInstall()
	{
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."todolists` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `user_id` int(10) unsigned NOT NULL,
					  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `content` text COLLATE utf8_unicode_ci NOT NULL,
					  `finished` decimal(5,2) NOT NULL,
					  `work_done` tinyint(1) NOT NULL,
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `todolist_user_id_foreign` (`user_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
					
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."todolists`
					  ADD CONSTRAINT `todolist_user_id_foreign` FOREIGN KEY (`user_id`) 
					  REFERENCES `".DB::getTablePrefix()."users` (`id`);");
			
			/*add to plugins*/
			$plugin = new Plugin;
			$plugin -> name = 'todolist';
			$plugin -> title = 'To-do list';
			$plugin -> function_id = NULL;
			$plugin -> function_grid = NULL;
			$plugin -> can_uninstall = '1';
			$plugin -> pluginversion = '1.0';
			$plugin -> active = '1';
			$plugin -> save();
			$plugin_id = $plugin->id;
			
			/*add to admin root navigation*/
			$adminmenu = new Adminmenu;
			$adminmenu -> plugin_id = $plugin_id;
			$adminmenu -> icon = 'icon-bell';
			$adminmenu -> background_color = 'green';
			$adminmenu -> order = '0';
			$adminmenu -> save();
			$adminmenu_id = $adminmenu->id;
			
			/*add plugin permission*/
			$permission = new Permission;
			$permission -> name = 'manage_todolists';
			$permission -> display_name = 'Manage todolists';
			$permission -> is_admin = '1';
			$permission -> save();
	}
	
	public function getUninstall()
	{
		return View::make('todolist::install/uninstall');
	}
	
	public function postUninstall()
	{
			$permission = Permission::where('name','=','manage_todolists')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			/*delete plugin functions from pages*/
			$plugin_id = Plugin::where('name','=','todolist')->first();
						
						
			/*delete admin navigation*/			
			$navigation = Adminmenu::where('plugin_id','=',$plugin_id->id)->first();			
			Adminsubmenu::where('admin_navigation_id','=',$navigation->id)->delete();
			$navigation->delete();
			
			/*delete plugin*/
			$plugin_id->delete(); 
			
			/*drop todolists tables*/
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."todolists`");
			
	}

}
