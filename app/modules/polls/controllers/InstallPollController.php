<?php namespace App\Modules\Polls\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Str,DB;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Adminmenu\Models\Adminmenu;
use App\Modules\Adminmenu\Models\Adminsubmenu;
use App\Modules\Roles\Models\Permission;
use App\Modules\Pages\Models\PluginFunction;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Pages\Models\PagePluginFunction;

class InstallPollController extends \AdminController{

	function __construct(\AdminController $admin)
	{
		parent::__construct();
		if (!array_key_exists('manage_plugins',$admin->roles)){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit ;
		}
	}
	public function getInstall()
	{
		return View::make('polls::install/install');
	}
	
	public function postInstall()
	{
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."polls` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `user_id` int(10) unsigned NOT NULL,
					  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'if the poll is active or not',
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `polls_user_id_foreign` (`user_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
					
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."polls`
					  ADD CONSTRAINT `polls_user_id_foreign` FOREIGN KEY (`user_id`) 
					  REFERENCES `".DB::getTablePrefix()."users` (`id`);");
					  
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."poll_options` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `poll_id` int(10) unsigned NOT NULL,
					  `votes` int(11) DEFAULT '0',
					  `order` int(11) DEFAULT '0',
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `options_polls_id_foreign` (`poll_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
					
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."poll_options`
					  ADD CONSTRAINT `options_polls_id_foreign` FOREIGN KEY (`poll_id`) 
					  REFERENCES `".DB::getTablePrefix()."polls` (`id`);");
					  
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."poll_votes` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `option_id` int(10) unsigned NOT NULL,
					  `ip_address` varchar(45) NOT NULL DEFAULT '' COMMENT 'max ipv6',
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `votes_options_id_foreign` (`option_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
					
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."poll_votes`
					  ADD CONSTRAINT `votes_options_id_foreign` FOREIGN KEY (`option_id`) 
					  REFERENCES `".DB::getTablePrefix()."poll_options` (`id`);");
			
			/*add to plugins*/
			$plugin = new Plugin;
			$plugin -> name = 'polls';
			$plugin -> title = 'Polls';
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
			$adminmenu -> icon = 'icon-signal';
			$adminmenu -> background_color = 'yellow';
			$adminmenu -> order = '0';
			$adminmenu -> save();
			$adminmenu_id = $adminmenu->id;
			
			/*add plugin permission*/
			$permission = new Permission;
			$permission -> name = 'manage_polls';
			$permission -> display_name = 'Manage polls';
			$permission -> is_admin = '1';
			$permission -> save();
			
			/*add plugin function*/
			$permission = new PluginFunction;
			$permission -> title = 'Active poll';
			$permission -> plugin_id = $plugin_id;
			$permission -> function = 'activePoll';
			$permission -> params = 'sort:asc;order:id;limit:1;';
			$permission -> type = 'sidebar';
			$permission -> save();
	}
	
	public function getUninstall()
	{
		return View::make('polls::install/uninstall');
	}
	
	public function postUninstall()
	{
			$permission = Permission::where('name','=','manage_polls')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();

			$plugin_id = Plugin::where('name','=','polls')->first();
						
			/*delete admin navigation*/			
			$navigation = Adminmenu::where('plugin_id','=',$plugin_id->id)->first();			
			Adminsubmenu::where('admin_navigation_id','=',$navigation->id)->delete();
			$navigation->delete();
			
			/*delete from pages*/
			$plugins = PluginFunction::where('plugin_id','=',$plugin_id->id)->get();						
			foreach ($plugins as $item) {
					PagePluginFunction::where('plugin_function_id','=',$item->id)->delete();
					PluginFunction::where('id','=',$item->id)->delete();
			}		
			
			/*delete plugin*/
			$plugin_id->delete(); 
			
			/*drop todolists tables*/
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."poll_votes`");
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."poll_options`");
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."polls`");
	}

}
