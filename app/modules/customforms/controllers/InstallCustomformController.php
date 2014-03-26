<?php namespace App\Modules\Customforms\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Str,DB;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Adminmenu\Models\Adminmenu;
use App\Modules\Adminmenu\Models\Adminsubmenu;
use App\Modules\Roles\Models\Permission;
use App\Modules\Pages\Models\PluginFunction;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Pages\Models\PagePluginFunction;

class InstallCustomformController extends \AdminController {

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
		return View::make('customforms::install/install');
	}
	
	public function postInstall()
	{
		DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."custom_forms` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `user_id` int(10) unsigned NOT NULL,
					  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `recievers` text COLLATE utf8_unicode_ci,
					  `message` text COLLATE utf8_unicode_ci NOT NULL,
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `custom_forms_user_id_index` (`user_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."custom_form_fields` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `customform_id` int(10) unsigned NOT NULL,
					  `user_id` int(10) unsigned NOT NULL,
					  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `options` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `type` int(11) NOT NULL,
					  `order` int(11) NOT NULL,
					  `mandatory` tinyint(1) NOT NULL,
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `custom_form_fields_customform_id_index` (`customform_id`),
					  KEY `custom_form_fields_user_id_index` (`user_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."custom_forms`
					  ADD CONSTRAINT `custom_forms_user_id_foreign` FOREIGN KEY (`user_id`) 
					  REFERENCES `".DB::getTablePrefix()."users` (`id`) ON DELETE CASCADE;");
			
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."custom_form_fields`
					  ADD CONSTRAINT `custom_form_fields_user_id_foreign` FOREIGN KEY (`user_id`) 
					  REFERENCES `".DB::getTablePrefix()."users` (`id`) ON DELETE CASCADE,
					  ADD CONSTRAINT `custom_form_fields_customform_id_foreign` FOREIGN KEY (`customform_id`) 
					  REFERENCES `".DB::getTablePrefix()."custom_forms` (`id`) ON DELETE CASCADE;");
			
			/*add to plugins*/
			$plugin = new Plugin;
			$plugin -> name = 'customforms';
			$plugin -> title = 'Custom form';
			$plugin -> function_id = 'getCustomFormId';
			$plugin -> function_grid = NULL;
			$plugin -> can_uninstall = '1';
			$plugin -> pluginversion = '1.0';
			$plugin -> active = '1';
			$plugin -> save();
			$plugin_id = $plugin->id;
			
			/*add to admin root navigation*/
			$adminmenu = new Adminmenu;
			$adminmenu -> plugin_id = $plugin_id;
			$adminmenu -> icon = 'icon-list-alt';
			$adminmenu -> background_color = 'pink';
			$adminmenu -> order = '0';
			$adminmenu -> save();
			
			/*add plugin permission*/
			$permission = new Permission;
			$permission -> name = 'manage_customform';
			$permission -> display_name = 'Manage custom forms';
			$permission -> is_admin = '1';
			$permission -> save();
			
			/*add plugin function*/
			$permission = new PluginFunction;
			$permission -> title = 'Display custom form';
			$permission -> plugin_id = $plugin_id;
			$permission -> function = 'showCustomFormId';
			$permission -> params = 'id;';
			$permission -> type = 'content';
			$permission -> save();
	}
	
	public function getUninstall()
	{
		return View::make('customforms::install/uninstall');
	}
	
	public function postUninstall()
	{
		/*delete permissions from roles*/	
			$permission = Permission::where('name','=','manage_customform')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			/*delete plugin functions from pages*/
			$plugin_id = Plugin::where('name','=','customforms')->first();
			
			$plugins = PluginFunction::where('plugin_id','=',$plugin_id->id)->get();
						
			foreach ($plugins as $item) {
					PagePluginFunction::where('plugin_function_id','=',$item->id)->delete();
					PluginFunction::where('id','=',$item->id)->delete();
			}	

			/*delete admin navigation*/			
			$navigation = Adminmenu::where('plugin_id','=',$plugin_id->id)->first();			
			Adminsubmenu::where('admin_navigation_id','=',$navigation->id)->delete();
			$navigation->delete();
			
			/*delete plugin*/
			$plugin_id->delete(); 
			
			/*drop custom_form tables*/
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."custom_form_fields`");
			
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."custom_forms`");
	}	
}